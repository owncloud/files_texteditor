<?php
/**
 * @author Björn Schießle <schiessle@owncloud.com>
 *
 * @copyright Copyright (c) 2015, ownCloud, Inc.
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


namespace OCA\Files_Texteditor\Controller;


use OC\Files\View;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\IL10N;
use OCP\ILogger;
use OCP\IRequest;

class FileHandlingController extends Controller{

	/** @var IL10N */
	private $l;

	/** @var View */
	private $view;

	/** @var ILogger */
	private $logger;

	/**
	 * @NoAdminRequired
	 *
	 * @param string $AppName
	 * @param IRequest $request
	 * @param IL10N $l10n
	 * @param View $view
	 * @param ILogger $logger
	 */
	public function __construct($AppName,
								IRequest $request,
								IL10N $l10n,
								View $view,
								ILogger $logger) {
		parent::__construct($AppName, $request);
		$this->l = $l10n;
		$this->view = $view;
		$this->logger = $logger;
	}

	/**
	 * load text file
	 *
	 * @NoAdminRequired
	 *
	 * @param string $dir
	 * @param string $filename
	 * @return DataResponse
	 */
	public function load($dir, $filename) {
		try {
			if (!empty($filename)) {
				$path = $dir . '/' . $filename;
				$filecontents = $this->view->file_get_contents($path);
				if ($filecontents !== false) {
					$writable = $this->view->isUpdatable($path);
					$mime = $this->view->getMimeType($path);
					$mtime = $this->view->filemtime($path);
					$encoding = mb_detect_encoding($filecontents . "a", "UTF-8, WINDOWS-1252, ISO-8859-15, ISO-8859-1, ASCII", true);
					if ($encoding == "") {
						// set default encoding if it couldn't be detected
						$encoding = 'ISO-8859-15';
					}
					$filecontents = iconv($encoding, "UTF-8", $filecontents);
					return new DataResponse(
						[
							'filecontents' => $filecontents,
							'writeable' => $writable,
							'mime' => $mime,
							'mtime' => $mtime
						],
						Http::STATUS_OK
					);
				} else {
					return new DataResponse(['message' => (string)$this->l->t('Can not read the file.')], Http::STATUS_BAD_REQUEST);
				}
			} else {
				return new DataResponse(['message' => (string)$this->l->t('Invalid file path supplied.')], Http::STATUS_BAD_REQUEST);
			}

		} catch (\Exception $e) {
			if(method_exists($e, 'getHint')) {
				$message = (string)$e->getHint();
			} else {
				$message = (string)$this->l->t('An internal server error occurred.');
			}
			return new DataResponse(['message' => $message], Http::STATUS_BAD_REQUEST);
		}
	}

	/**
	 * save text file
	 *
	 * @NoAdminRequired
	 *
	 * @param string $path
	 * @param string $filecontents
	 * @param integer $mtime
	 * @return DataResponse
	 */
	public function save($path, $filecontents, $mtime) {

		if($path !== '' && (is_integer($mtime) && $mtime > 0)) {
			// Get file mtime
			$filemtime = $this->view->filemtime($path);
			if($mtime !== $filemtime) {
				// Then the file has changed since opening
				$this->logger->error('File: ' . $path . ' modified since opening.',
					['app' => 'files_texteditor']);
				return new DataResponse(
					['message' => $this->l->t('Cannot save file as it has been modified since opening')],
					Http::STATUS_BAD_REQUEST);
			} else {
				// File same as when opened, save file
				if($this->view->isUpdatable($path)) {
					$filecontents = iconv(mb_detect_encoding($filecontents), "UTF-8", $filecontents);
					$this->view->file_put_contents($path, $filecontents);
					// Clear statcache
					clearstatcache();
					// Get new mtime
					$newmtime = $this->view->filemtime($path);
					$newsize = $this->view->filesize($path);
					return new DataResponse(['mtime' => $newmtime, 'size' => $newsize], Http::STATUS_OK);
				} else {
					// Not writeable!
					$this->logger->error('User does not have permission to write to file: ' . $path,
						['app' => 'files_texteditor']);
					return new DataResponse([ 'message' => $this->l->t('Insufficient permissions')],
						Http::STATUS_BAD_REQUEST);
				}
			}
		} else if($path === '') {
			$this->logger->error('No file path supplied');
			return new DataResponse(['message' => $this->l->t('File path not supplied')], Http::STATUS_BAD_REQUEST);
		} else if(!is_integer($mtime) || $mtime <= 0) {
			$this->logger->error('No file mtime supplied', ['app' => 'files_texteditor']);
			return new DataResponse(['message' => $this->l->t('File mtime not supplied')], Http::STATUS_BAD_REQUEST);
		}
	}

}
