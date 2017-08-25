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

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Page\TextEditorPage;

require_once 'bootstrap.php';

/**
 * Files context.
 */
class TextEditorContext extends RawMinkContext implements Context
{
	private $textEditorPage;
	private $featureContext;
	private $filesContext;

	public function __construct(TextEditorPage $textEditorPage)
	{
		$this->textEditorPage = $textEditorPage;
	}

	/**
	 * @When I create a text file with the name :name
	 *
	 * @param string $name
	 * @return void
	 */
	public function createATextFile($name) {
		$this->textEditorPage->createTextFile($name);
		$this->textEditorPage->waitTillEditorIsLoaded();
	}

	/**
	 * @When I input :text in the text area
	 */
	public function iInputTextInTheTextArea($text)
	{
		$this->textEditorPage->typeIntoTextFile($text);
	}

	/**
	 * @When I input the following text in the text area:
	 */
	public function iInputTheFollowingInTheTextArea(PyStringNode $multiLineText)
	{
		$this->textEditorPage->typeIntoTextFile($multiLineText->getRaw());
	}

	/**
	 * general before scenario for all text editor tests.
	 * This will run before EVERY scenario.
	 * It will set the properties for this object.
	 *
	 * @param BeforeScenarioScope $scope
	 * @return void
	 * @BeforeScenario
	 */
	public function before(BeforeScenarioScope $scope) {
		// Get the environment
		$environment = $scope->getEnvironment();
		// Get all the contexts you need in this context
		$this->featureContext = $environment->getContext('FeatureContext');
		$this->filesContext = $environment->getContext('FilesContext');
		$this->tmpDir = $this->getMinkParameter("show_tmp_dir");
		$suiteParameters = $scope->getEnvironment()->getSuite()
			->getSettings() ['context'] ['parameters'];
		$this->ocPath = $suiteParameters['ocPath'];
	}
}
