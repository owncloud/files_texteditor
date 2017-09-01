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
		And I open the file <file_name>
		Then there is 1 line of text
		And line 1 of the text is <example_text>
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
		And I open the file "सिमप्ले text file.txt"
		Then there is 1 line of text
		And line 1 of the text is "नेपाल"

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
		And I open the file "atextfile.txt"
		Then there are 6 lines of text
		And line 1 of the text is "What is this?"
		And line 2 of the text is 'This is some "example" text!'
		And line 3 of the text is "That goes on some lines in a 'text' file."
		And line 4 of the text is ""
		And line 5 of the text is "नेपाल"
		And line 6 of the text is "1 2 3 4 5 6 7 8 9 0"

	Scenario: Create a text file with the default name and file extension in a sub-folder
		When I open the folder "simple-folder"
		And I create a text file with the name ""
		And I input "stuff" in the text area
		And I close the text editor
		Then the file "New text file.txt" should be listed
		And the files page is reloaded
		Then the file "New text file.txt" should be listed
		And I open the file "New text file.txt"
		Then line 1 of the text is "stuff"
		And I input "lots of other text so that I can see it working" in the text area
		And I close the text editor
		Then the file "New text file.txt" should be listed

	Scenario: Create a text file in a sub-folder using special characters in the names
		When I create a folder with the name "सिमप्ले फोल्देर $%#?&@"
		And I open the folder "सिमप्ले फोल्देर $%#?&@"
		And I create a text file with the name "सिमप्ले $%#?&@ name.txt"
		And I input "a line of text" in the text area
		And I close the text editor
		Then the file "सिमप्ले $%#?&@ name.txt" should be listed
		And the files page is reloaded
		Then the file "सिमप्ले $%#?&@ name.txt" should be listed
