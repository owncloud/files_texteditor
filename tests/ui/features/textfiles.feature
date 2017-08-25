Feature: textFiles

	Background:
		Given a regular user exists
		And I am logged in as a regular user
		And I am on the files page

	Scenario Outline: Create a text file
		When I create a text file with the name <file_name>
		And I input <example_text> in the text area
		And the files page is reloaded
		Then the file <file_name> should be listed
		Examples:
		|file_name               |example_text                      |
		|'सिमप्ले text file.txt'    |'some text'                       |
		|'"somequotes1" text.txt'|'John said,"hello."'              |
		|"'somequotes2' text.txt"|'special !@#$%^&*()[]{}\|-_+=\/?' |

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
		And the files page is reloaded
		Then the file "atextfile.txt" should be listed
