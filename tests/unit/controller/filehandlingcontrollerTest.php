<?php
/**
 * @author Björn Schießle <schiessle@owncloud.com>
 *
 * @copyright Copyright (c) 2018, ownCloud GmbH
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\Files_Texteditor\Tests\Controller;

use OC\HintException;
use OCA\Files_Texteditor\Controller\FileHandlingController;
use OCP\Files\ForbiddenException;
use OCP\Files\Storage\IPersistentLockingStorage;
use OCP\Files\Storage\IStorage;
use OCP\Lock\LockedException;
use Test\TestCase;

// Dummy interface because phpunit9 createMock() will no longer allow an array of interfaces
interface IPersistentLockingStorageTest extends IPersistentLockingStorage, IStorage {
}

class FileHandlingControllerTest extends TestCase {
	/** @var FileHandlingController */
	protected $controller;

	/** @var string */
	protected $appName;

	/** @var \OCP\IRequest | \PHPUnit\Framework\MockObject\MockObject */
	protected $requestMock;

	/** @var \OCP\IL10N | \PHPUnit\Framework\MockObject\MockObject */
	private $l10nMock;

	/** @var \OCP\ILogger | \PHPUnit\Framework\MockObject\MockObject */
	private $loggerMock;

	/** @var \OC\Files\View | \PHPUnit\Framework\MockObject\MockObject */
	private $viewMock;

	/** @var \OCP\IUserSession | \PHPUnit\Framework\MockObject\MockObject */
	private $userSessionMock;

	/** @var \OCP\IConfig | \PHPUnit\Framework\MockObject\MockObject */
	private $configMock;

	public function setUp(): void {
		parent::setUp();
		$this->appName = 'files_texteditor';
		$this->requestMock = $this->getMockBuilder('OCP\IRequest')
			->disableOriginalConstructor()
			->getMock();
		$this->l10nMock = $this->getMockBuilder('OCP\IL10N')
			->disableOriginalConstructor()
			->getMock();
		$this->loggerMock = $this->getMockBuilder('OCP\ILogger')
			->disableOriginalConstructor()
			->getMock();
		$this->viewMock = $this->getMockBuilder('OC\Files\View')
			->disableOriginalConstructor()
			->getMock();
		$this->userSessionMock = $this->getMockBuilder('OCP\IUserSession')
			->disableOriginalConstructor()
			->getMock();
		$this->configMock = $this->getMockBuilder('OCP\IConfig')
			->disableOriginalConstructor()
			->getMock();

		$this->l10nMock->expects($this->any())->method('t')->willReturnCallback(
			function ($message) {
				return $message;
			}
		);

		$userMock = $this->createMock('OCP\IUser');
		$userMock->expects($this->any())
			->method('getUID')
			->willReturn('testUser1');
		$this->userSessionMock->expects($this->any())
			->method('getUser')
			->willReturn($userMock);

		$this->controller = new FileHandlingController(
			$this->appName,
			$this->requestMock,
			$this->l10nMock,
			$this->viewMock,
			$this->loggerMock,
			$this->userSessionMock,
			$this->configMock);
	}

	/**
	 * @dataProvider dataTestLoad
	 *
	 * @param string $filename
	 * @param string|boolean $fileContent
	 * @param integer $expectedStatus
	 * @param string $expectedMessage
	 */
	public function testLoad($filename, $fileContent, $expectedStatus, $expectedMessage) {
		$this->viewMock->expects($this->any())
			->method('file_get_contents')
			->willReturn($fileContent);

		$result = $this->controller->load('/', $filename);
		$data = $result->getData();
		$status = $result->getStatus();
		$this->assertSame($status, $expectedStatus);
		if ($status === 200) {
			$this->assertArrayHasKey('filecontents', $data);
			$this->assertArrayHasKey('writeable', $data);
			$this->assertArrayHasKey('mime', $data);
			$this->assertArrayHasKey('mtime', $data);
			$this->assertSame($data['filecontents'], $fileContent);
		} else {
			$this->assertArrayHasKey('message', $data);
			$this->assertSame($expectedMessage, $data['message']);
		}
	}

	public function dataTestLoad() {
		return [
			['test.txt', 'file content', 200, ''],
			['test.txt', '', 200, ''],
			['test.txt', '0', 200, ''],
			['', 'file content', 400, 'Invalid file path supplied.'],
			['test.txt', false, 400, 'Cannot read the file.'],
		];
	}

	public function dataLoadExceptionWithException() {
		return [
			[new \Exception(), 'An internal server error occurred.'],
			[new HintException('error message', 'test exception'), 'test exception'],
			[new ForbiddenException('firewall', false), 'firewall'],
			[new LockedException('secret/path/https://github.com/owncloud/files_texteditor/pull/96'), 'The file is locked.'],
		];
	}

	/**
	 * @dataProvider dataLoadExceptionWithException
	 * @param \Exception $exception
	 * @param string $expectedMessage
	 */
	public function testLoadExceptionWithException(\Exception $exception, $expectedMessage) {
		$this->viewMock->expects($this->any())
			->method('file_get_contents')
			->willReturnCallback(function () use ($exception) {
				throw $exception;
			});

		$result = $this->controller->load('/', 'test.txt');
		$data = $result->getData();

		$this->assertSame(400, $result->getStatus());
		$this->assertArrayHasKey('message', $data);
		$this->assertSame($expectedMessage, $data['message']);
	}

	/**
	 * @dataProvider dataLoadExceptionWithException
	 * @param \Exception $exception
	 * @param string $expectedMessage
	 */
	public function testSaveExceptionWithException(\Exception $exception, $expectedMessage) {
		$this->viewMock->expects($this->any())
			->method('file_put_contents')
			->willReturnCallback(function () use ($exception) {
				throw $exception;
			});

		$this->viewMock->expects($this->any())
			->method('filemtime')
			->willReturn(42);
		$this->viewMock->expects($this->any())
			->method('isUpdatable')
			->willReturn(true);

		$fileInfoMock = $this->createMock('\OCP\Files\FileInfo');
		$this->viewMock->expects($this->any())
			->method('getFileInfo')
			->willReturn($fileInfoMock);

		$result = $this->controller->save('/test.txt', 'content', 42);
		$data = $result->getData();

		$this->assertSame(400, $result->getStatus());
		$this->assertArrayHasKey('message', $data);
		$this->assertSame($expectedMessage, $data['message']);
	}

	/**
	 * @dataProvider dataTestSave
	 *
	 * @param $path
	 * @param $fileContents
	 * @param $mTime
	 * @param $fileMTime
	 * @param $isUpdatable
	 * @param $expectedStatus
	 * @param $expectedMessage
	 */
	public function testSave($path, $fileContents, $mTime, $fileMTime, $isUpdatable, $expectedStatus, $expectedMessage) {
		$this->viewMock->expects($this->any())
			->method('filemtime')
			->willReturn($fileMTime);

		$this->viewMock->expects($this->any())
			->method('isUpdatable')
			->willReturn($isUpdatable);

		$this->configMock->expects($this->any())
			->method('getAppValue')
			->willReturn('no');

		$storageMock = $this->createMock(IPersistentLockingStorageTest::class);
		$storageMock->expects($this->any())
			->method('instanceOfStorage')
			->willReturn(true);

		$fileInfoMock = $this->createMock('\OCP\Files\FileInfo');
		$fileInfoMock->expects($this->any())
			->method('getStorage')
			->willReturn($storageMock);

		$this->viewMock->expects($this->any())
			->method('getFileInfo')
			->willReturn($fileInfoMock);

		if ($expectedStatus === 200) {
			$this->viewMock->expects($this->once())
				->method('file_put_contents')->with($path, $fileContents);
		} else {
			$this->viewMock->expects($this->never())->method(('file_put_contents'));
		}

		$result = $this->controller->save($path, $fileContents, $mTime);
		$status = $result->getStatus();
		$data = $result->getData();

		$this->assertSame($expectedStatus, $status);
		if ($status === 200) {
			$this->assertArrayHasKey('mtime', $data);
			$this->assertArrayHasKey('size', $data);
		} else {
			$this->assertArrayHasKey('message', $data);
			$this->assertSame($expectedMessage, $data['message']);
		}
	}

	/**
	 * @dataProvider dataTestSaveWithLock
	 *
	 * @param $expectedStatus
	 * @param $expectedMessage
	 * @param $fileLockOwner
	 */
	public function testSaveWithLock($expectedStatus, $expectedMessage, $fileLockOwner) {
		$mTime = 65638643;
		$path = 'test.txt';
		$fileContents = 'content';

		$this->viewMock->expects($this->any())
			->method('filemtime')
			->willReturn($mTime);
		$this->viewMock->expects($this->any())
			->method('isUpdatable')
			->willReturn(true);
		$this->configMock->expects($this->any())
			->method('getAppValue')
			->willReturn('yes');

		$lockMock = $this->createMock('OCP\Lock\Persistent\ILock');
		$lockMock->expects($this->any())
			->method('getOwner')
			->willReturn($fileLockOwner);

		$storageMock = $this->createMock(IPersistentLockingStorageTest::class);
		$storageMock->expects($this->any())
			->method('instanceOfStorage')
			->willReturn(true);
		$storageMock->expects($this->any())
			->method('getLocks')
			->willReturn([$lockMock]);

		$fileInfoMock = $this->createMock('\OCP\Files\FileInfo');
		$fileInfoMock->expects($this->any())
			->method('getStorage')
			->willReturn($storageMock);
		$fileInfoMock->expects($this->any())
			->method('getInternalPath')
			->willReturn('');

		$this->viewMock->expects($this->any())
			->method('getFileInfo')
			->willReturn($fileInfoMock);

		$result = $this->controller->save($path, $fileContents, $mTime);
		$status = $result->getStatus();
		$data = $result->getData();

		$this->assertSame($expectedStatus, $status);
		if ($status !== 200) {
			$this->assertSame($expectedMessage, $data['message']);
		}
	}

	public function testFileTooBig() {
		$this->viewMock->expects($this->any())
			->method('filesize')
			->willReturn(4194304 + 1);

		$result = $this->controller->load('/', 'foo.bar');
		$data = $result->getData();
		$status = $result->getStatus();
		$this->assertSame(400, $status);
		$this->assertArrayHasKey('message', $data);
		$this->assertSame('This file is too big to be opened. Please download the file instead.', $data['message']);
	}

	public function dataTestSave() {
		return [
			['/test.txt', 'file content', 65638643, 65638643, true, 200, ''],
			['', 'file content', 65638643, 65638643, true, 400, 'File path not supplied'],
			['/test.txt', 'file content', '', 65638643, true, 400, 'File mtime not supplied'],
			['/test.txt', 'file content', 0, 65638643, true, 400, 'File mtime not supplied'],
			['/test.txt', 'file content', 65638643, 32848548, true, 400, 'Cannot save file as it has been modified since opening'],
			['/test.txt', 'file content', 65638643, 65638643, false, 400, 'Insufficient permissions'],
		];
	}

	public function dataTestSaveWithLock() {
		return [
			[400, 'The file is locked.', 'testUser2'],
			[200, '', 'testUser1'],
		];
	}
}
