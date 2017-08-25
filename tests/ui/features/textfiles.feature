Feature: textFiles

	Background:
		Given a regular user exists
		And I am logged in as a regular user
		And I am on the files page

	Scenario Outline: Create a text file
		When I create a text file with the name <file_name>
		And I input <example_text> in the text area
		And I close the text editor
		Then the file <file_name> should be listed
		And the files page is reloaded
		Then the file <file_name> should be listed
		Examples:
		|file_name               |example_text                      |
		|'सिमप्ले text file.txt'    |'some text'                       |
		|'"somequotes1" text.txt'|'John said,"hello."'              |
		|"'somequotes2' text.txt"|'special !@#$%^&*()[]{}\|-_+=\/?' |

	Scenario: Create a text file with the default name and file extension
		When I create a text file with the name ""
		And I input "stuff" in the text area
		And I close the text editor
		Then the file "New text file.txt" should be listed
		And the files page is reloaded
		Then the file "New text file.txt" should be listed

	Scenario: Create a text file with the default file extension and do not close the editor
		When I create a text file with the name "abc" without changing the default file extension
		And I input "something" in the text area
		Then the file "abc.txt" should be listed
		And the files page is reloaded
		Then the file "abc.txt" should be listed

	Scenario: Create a text file with the default file extension and unicode file name
		When I create a text file with the name "सिमप्ले text file" without changing the default file extension
		And I input "नेपाल" in the text area
		And I close the text editor
		Then the file "सिमप्ले text file.txt" should be listed
		And the files page is reloaded
		Then the file "सिमप्ले text file.txt" should be listed

	Scenario: Create a text file with multiple lines of text in it
		When I create a text file with the name "atextfile.txt"
		And I input the following text in the text area:
		"""
		What is this?
		This is some "example" text!
		That goes on some lines in a 'text' file.

		नेपाल
		1 2 3 4 5 6 7 8 9 0
		"""
		And I close the text editor
		Then the file "atextfile.txt" should be listed
		And the files page is reloaded
		Then the file "atextfile.txt" should be listed
