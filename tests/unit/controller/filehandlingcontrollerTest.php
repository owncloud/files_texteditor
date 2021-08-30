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
use OCP\Constants;
use OCP\Files\ForbiddenException;
use OCP\Lock\LockedException;
use Test\TestCase;

class FileHandlingControllerTest extends TestCase {

	/** @var FileHandlingController */
	protected $controller;

	/** @var string */
	protected $appName;

	/** @var \OCP\IRequest|\PHPUnit\Framework\MockObject\MockObject */
	protected $requestMock;

	/** @var \OCP\IL10N|\PHPUnit\Framework\MockObject\MockObject */
	private $l10nMock;

	/** @var \OCP\ILogger|\PHPUnit\Framework\MockObject\MockObject */
	private $loggerMock;

	/** @var \OCP\Share\IManager|\PHPUnit\Framework\MockObject\MockObject */
	private $shareManagerMock;

	/** @var \OCP\IUserSession|\PHPUnit\Framework\MockObject\MockObject */
	private $userSessionMock;

	/** @var \OCP\Files\IRootFolder|\PHPUnit\Framework\MockObject\MockObject */
	private $rootMock;

	/** @var \OCP\Files\File|\PHPUnit\Framework\MockObject\MockObject */
	private $fileMock;

	/** @var \OCP\IUser|\PHPUnit\Framework\MockObject\MockObject */
	private $userMock;

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
		$this->shareManagerMock = $this->getMockBuilder('OCP\Share\IManager')
			->disableOriginalConstructor()
			->getMock();
		$this->userSessionMock = $this->getMockBuilder('OCP\IUserSession')
			->disableOriginalConstructor()
			->getMock();
		$this->rootMock = $this->getMockBuilder('OCP\Files\IRootFolder')
			->disableOriginalConstructor()
			->getMock();

		$this->fileMock = $this->getMockBuilder('OCP\Files\File')
			->disableOriginalConstructor()
			->getMock();

		$this->userMock = $this->getMockBuilder('OCP\IUser')
			->disableOriginalConstructor()
			->getMock();

		$this->l10nMock->expects($this->any())->method('t')->willReturnCallback(
			function ($message) {
				return $message;
			}
		);

		$this->controller = new FileHandlingController(
			$this->appName,
			$this->requestMock,
			$this->l10nMock,
			$this->loggerMock,
			$this->shareManagerMock,
			$this->userSessionMock,
			$this->rootMock
		);
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
		$this->fileMock->expects($this->any())
			->method('getContent')
			->willReturn($fileContent);

		$this->fileMock->expects($this->any())
			->method('getPermissions')
			->willReturn(Constants::PERMISSION_ALL);

		$this->rootMock->expects($this->any())
			->method('get')
			->willReturn($this->fileMock);

		$this->userMock->expects($this->any())
			->method('getUID')
			->willReturn('admin');

		$this->userSessionMock->expects($this->any())
			->method('getUser')
			->willReturn($this->userMock);

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
		$this->fileMock->expects($this->any())
			->method('getContent')
			->willReturnCallback(function () use ($exception) {
				throw $exception;
			});

		$this->fileMock->expects($this->any())
			->method('getPermissions')
			->willReturn(Constants::PERMISSION_ALL);

		$this->rootMock->expects($this->any())
			->method('get')
			->willReturn($this->fileMock);

		$this->userMock->expects($this->any())
			->method('getUID')
			->willReturn('admin');

		$this->userSessionMock->expects($this->any())
			->method('getUser')
			->willReturn($this->userMock);

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
		$this->fileMock->expects($this->any())
			->method('putContent')
			->willReturnCallback(function () use ($exception) {
				throw $exception;
			});

		$this->fileMock->expects($this->any())
			->method('getPermissions')
			->willReturn(Constants::PERMISSION_ALL);

		$this->rootMock->expects($this->any())
			->method('get')
			->willReturn($this->fileMock);

		$this->userMock->expects($this->any())
			->method('getUID')
			->willReturn('admin');

		$this->userSessionMock->expects($this->any())
			->method('getUser')
			->willReturn($this->userMock);

		$this->fileMock->expects($this->any())
			->method('getMTime')
			->willReturn(42);

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
		if ($isUpdatable) {
			$permissions = Constants::PERMISSION_ALL;
		} else {
			$permissions = Constants::PERMISSION_READ;
		}
		$this->fileMock->expects($this->any())
			->method('getPermissions')
			->willReturn($permissions);

		$this->rootMock->expects($this->any())
			->method('get')
			->willReturn($this->fileMock);

		$this->userMock->expects($this->any())
			->method('getUID')
			->willReturn('admin');

		$this->userSessionMock->expects($this->any())
			->method('getUser')
			->willReturn($this->userMock);

		$this->fileMock->expects($this->any())
			->method('getMTime')
			->willReturn($fileMTime);

		if ($expectedStatus === 200) {
			$this->fileMock->expects($this->once())->method('putContent')->with($fileContents);
		} else {
			$this->fileMock->expects($this->never())->method('putContent');
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

	public function testFileTooBig() {
		$this->fileMock->expects($this->any())
		->method('getPermissions')
		->willReturn(Constants::PERMISSION_ALL);

		$this->rootMock->expects($this->any())
		->method('get')
		->willReturn($this->fileMock);

		$this->userMock->expects($this->any())
		->method('getUID')
		->willReturn('admin');

		$this->userSessionMock->expects($this->any())
		->method('getUser')
		->willReturn($this->userMock);

		$this->fileMock->expects($this->any())
			->method('getSize')
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
}
