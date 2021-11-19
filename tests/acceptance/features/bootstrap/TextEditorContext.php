<?php declare(strict_types=1);
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

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Page\TextEditorPage;

require_once 'bootstrap.php';

/**
 * Text Editor context.
 */
class TextEditorContext extends RawMinkContext implements Context {
	private $textEditorPage;
	private $webUIGeneralContext;
	private $webUIFilesContext;

	/**
	 * TextEditorContext constructor.
	 *
	 * @param TextEditorPage $textEditorPage
	 */
	public function __construct(TextEditorPage $textEditorPage) {
		$this->textEditorPage = $textEditorPage;
	}

	/**
	 * @Given /^the user has created a text file with the name ((?:'[^']*')|(?:"[^"]*"))( without changing the default file extension|)$/
	 * @When /^the user creates a text file with the name ((?:'[^']*')|(?:"[^"]*")) using the webUI( without changing the default file extension|)$/
	 *
	 * @param string $name
	 * @param string $useDefaultFileExtension
	 *
	 * @return void
	 */
	public function createATextFileWithTheNameUsingTheWebUI(
		string $name,
		string $useDefaultFileExtension = ''
	):void {
		// The capturing group of the regex always includes the quotes at each
		// end of the captured string, so trim them.
		$name = \trim($name, $name[0]);
		$this->textEditorPage->createTextFile(
			$this->getSession(),
			$name,
			\strlen($useDefaultFileExtension) ? true : false
		);
		$this->textEditorPage->waitTillEditorIsLoaded();
	}

	/**
	 * @Then near the new text file box a tooltip with the text :toolTipText should be displayed on the webUI
	 *
	 * @param string $toolTipText
	 *
	 * @return void
	 */
	public function nearTheNewTextFileBoxATooltipShouldBeDisplayedOnTheWebUI(
		string $toolTipText
	):void {
		PHPUnit\Framework\Assert::assertEquals(
			$toolTipText,
			$this->textEditorPage->getTooltipOfNewTextFileBox()
		);
	}

	/**
	 * @Given the user has input :text in the text area
	 * @When the user inputs :text in the text area
	 *
	 * @param string $text
	 *
	 * @return void
	 */
	public function theUserInputsTextInTheTextArea(string $text):void {
		$this->textEditorPage->typeIntoTextFile(
			$this->getSession(),
			$text
		);
	}

	/**
	 * @When the user inputs the following text in the text area:
	 *
	 * @param PyStringNode $multiLineText
	 *
	 * @return void
	 */
	public function theUserInputsTheFollowingInTheTextArea(
		PyStringNode $multiLineText
	):void {
		$this->textEditorPage->typeIntoTextFile(
			$this->getSession(),
			$multiLineText->getRaw()
		);
	}

	/**
	 * @Then there should be :number line(s) of text in the text area
	 *
	 * @param int $number
	 *
	 * @return void
	 */
	public function thereShouldBeLinesOfTextInTheTextArea(int $number):void {
		PHPUnit\Framework\Assert::assertEquals(
			$number,
			\count($this->textEditorPage->textFileContent())
		);
	}

	/**
	 * @Then line :number of the text should be :text
	 *
	 * @param int $number
	 * @param string $text
	 *
	 * @return void
	 */
	public function lineOfTheTextShouldBe(int $number, string $text):void {
		$lineIndex = $number - 1;
		$textFileContent = $this->textEditorPage->textFileContent();
		PHPUnit\Framework\Assert::assertEquals(
			$text,
			$textFileContent[$lineIndex]
		);
	}
	/**
	 * @Given the user has closed the text editor
	 * @When the user closes the text editor
	 *
	 * @return void
	 */
	public function theUserClosesTheTextEditor():void {
		$this->textEditorPage->closeTheTextEditor($this->getSession());
	}

	/**
	 * general before scenario for all text editor tests.
	 * This will run before EVERY scenario.
	 * It will set the properties for this object.
	 *
	 * @BeforeScenario
	 *
	 * @param BeforeScenarioScope $scope
	 *
	 * @return void
	 */
	public function before(BeforeScenarioScope $scope):void {
		// Get the environment
		$environment = $scope->getEnvironment();
		// Get all the contexts you need in this context
		$this->webUIGeneralContext = $environment->getContext('WebUIGeneralContext');
		$this->webUIFilesContext = $environment->getContext('WebUIFilesContext');
	}
}
