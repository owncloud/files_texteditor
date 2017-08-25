<?php
/**
* ownCloud
*
* @author Phillip Davis
* @copyright 2017 Phillip Davis info@individual-it.net
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

class TextEditorFilesPage extends FilesPage
{
	protected $newTextFileButtonXpath = './/div[contains(@class, "newFileMenu")]//a[@data-templatename="New text file.txt"]';
	protected $newTextFileNameInputLabel = 'New text file.txt';

	/**
	 * create a text file with the given name.
	 * If name is not given the default is used.
	 *
	 * @param string $name
	 */
	public function createTextFile($name = null)
	{
		$this->find("xpath", $this->newFileFolderButtonXpath)->click();
		$this->find("xpath", $this->newTextFileButtonXpath)->click();
		if ($name !== null) {
			try {
				$this->fillField($this->newTextFileNameInputLabel, $name . "\n");
			} catch (\WebDriver\Exception\NoSuchElement $e) {
				//this seems to be a bug in MinkSelenium2Driver. Used to work fine in 1.3.1 but now throws this exception
				//actually all that we need does happen, so we just don't do anything
			}
		}
	}
}