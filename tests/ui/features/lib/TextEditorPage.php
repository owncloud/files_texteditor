<?php
/**
 * ownCloud
 *
 * @author Phillip Davis <phil@jankaritech.com>
 * @copyright 2017 Phillip Davis phil@jankaritech.com
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Page;

use Behat\Mink\Session;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\ElementNotFoundException;
use WebDriver\Key;

/**
 * Text Editor page.
 */
class TextEditorPage extends FilesPage {
	protected $newTextFileButtonXpath
		= './/div[contains(@class, "newFileMenu")]' .
			'//a[@data-templatename="New text file.txt"]';
	protected $newTextFileNameInputLabel = 'New text file.txt';
	protected $newTextFileNameInputXpath
		= './/div[contains(@class, "newFileMenu")]' .
			'//a[@data-templatename="New text file.txt"]//input';
	protected $textFileEditXpath = "//textarea[contains(@class,'ace_text-input')]";
	protected $textFileTextLayerXpath = "//div[contains(@class,'ace_text-layer')]";
	protected $textFileLineXpath = ".//div[contains(@class,'ace_line')]";
	protected $textEditorCloseButtonId = "editor_close";

	/**
	 * type in the field that matches the given xpath and optionally press enter.
	 * Note: this depends on methods that might only be in the Selenium
	 * implementation
	 *
	 * @param Session $session
	 * @param string $xpath
	 * @param string $text
	 * @param bool $pressEnter
	 * @throws ElementNotFoundException
	 */
	public function typeInField(
		Session $session,
		$xpath,
		$text,
		$pressEnter = false
	) {
		$element = $session->getDriver()->getWebDriverSession()->element(
			"xpath", $xpath
		);

		if (is_null($element)) {
			throw new ElementNotFoundException(
				"could not find element with xpath '" . $xpath . "'"
			);
		}

		$keys = preg_split('//u', $text, null, PREG_SPLIT_NO_EMPTY);
		if ($pressEnter) {
			$keys[] = Key::ENTER;
		}
		$element->postValue(array('value' => $keys));
	}

	/**
	 * create a text file with the given name.
	 * If name is not given the default is used.
	 * If $useDefaultFileExtension is true, then only the name is entered and the
	 * file extension is the default given by the application.
	 *
	 * @param Session $session
	 * @param string $name
	 * @param boolean $useDefaultFileExtension
	 * @return void
	 */
	public function createTextFile(
		Session $session,
		$name = null,
		$useDefaultFileExtension = false
	) {
		$newFileFolderButton
			= $this->find("xpath", $this->newFileFolderButtonXpath);

		if ($newFileFolderButton === null) {
			throw new ElementNotFoundException(
				"could not find new file/folder button"
			);
		}

		$newFileFolderButton->click();

		$newTextFileButton = $this->find("xpath", $this->newTextFileButtonXpath);

		if ($newTextFileButton === null) {
			throw new ElementNotFoundException(
				"could not find new text file button"
			);
		}

		$newTextFileButton->click();

		if (strlen($name)) {
			if ($useDefaultFileExtension) {
				$this->typeInField(
					$session,
					$this->newTextFileNameInputXpath,
					$name,
					true
				);
			} else {
				try {
					$this->fillField($this->newTextFileNameInputLabel, $name . "\n");
				} catch (\WebDriver\Exception\NoSuchElement $e) {
					// this seems to be a bug in MinkSelenium2Driver.
					// used to work fine in 1.3.1 but now throws this exception
					// actually all that we need does happen,
					// so we just don't do anything
				}
			}
		} else {
			$this->typeInField(
				$session,
				$this->newTextFileNameInputXpath,
				'',
				true
			);
		}

		$this->waitForAjaxCallsToStartAndFinish($session);
	}

	/**
	 * type text into the text area
	 *
	 * @param string $text
	 * @return void
	 */
	public function typeIntoTextFile(
		Session $session,
		$text
	) {
		$this->typeInField(
			$session,
			$this->textFileEditXpath,
			$text
		);
	}

	/**
	 * get the content of the open text file
	 *
	 * @return array of lines of text
	 */
	public function textFileContent() {
		$textLayer = $this->find("xpath", $this->textFileTextLayerXpath);
		if ($textLayer === null) {
			throw new ElementNotFoundException("could not find text layer");
		}
		$textLineElements = $textLayer->findAll(
			"xpath", $this->textFileLineXpath
		);
		if ($textLineElements === null) {
			throw new ElementNotFoundException("could not find text lines");
		}
		$textLines = [];
		$valid = true;
		foreach ($textLineElements as $textLineElement) {
			if ($valid) {
				$textLines[] = $textLineElement->getText();
			}
			// Only grab every 2nd line.
			// TODO:
			// Work out why they are duplicated.
			$valid = !$valid;
		}
		return $textLines;
	}

	/**
	 *
	 * @throws ElementNotFoundException
	 * @return void
	 */
	public function closeTheTextEditor() {
		$closeButton = $this->findById($this->textEditorCloseButtonId);
		if ($closeButton === null) {
			throw new ElementNotFoundException(
				"could not find text editor close button"
			);
		}
		$closeButton->click();
	}

	/**
	 * @param int $timeout_msec
	 * @return void
	 */
	public function waitTillEditorIsLoaded(
		$timeout_msec = STANDARDUIWAITTIMEOUTMILLISEC
	) {
		$this->waitTillElementIsNotNull($this->textFileEditXpath, $timeout_msec);
	}

}