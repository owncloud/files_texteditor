@webUI @insulated
Feature: textFiles

	Background:
		Given a regular user has been created
		And the regular user has logged in using the webUI
		And the user has browsed to the files page

	Scenario Outline: Create a text file
		When I create a text file with the name <file_name>
		And I input <example_text> in the text area
		And I close the text editor
		Then the file <file_name> should be listed on the webUI
		And the user reloads the current page of the webUI
		Then the file <file_name> should be listed on the webUI
		And the user opens the file <file_name> using the webUI
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
		Then the file "New text file.txt" should be listed on the webUI
		And the user reloads the current page of the webUI
		Then the file "New text file.txt" should be listed on the webUI

	Scenario: Create a text file with the default file extension and do not close the editor
		When I create a text file with the name "abc" without changing the default file extension
		And I input "something" in the text area
		Then the file "abc.txt" should be listed on the webUI
		And the user reloads the current page of the webUI
		Then the file "abc.txt" should be listed on the webUI

	Scenario: Create a text file with the default file extension and unicode file name
		When I create a text file with the name "सिमप्ले text file" without changing the default file extension
		And I input "नेपाल" in the text area
		And I close the text editor
		Then the file "सिमप्ले text file.txt" should be listed on the webUI
		And the user reloads the current page of the webUI
		Then the file "सिमप्ले text file.txt" should be listed on the webUI
		And the user opens the file "सिमप्ले text file.txt" using the webUI
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
		Then the file "atextfile.txt" should be listed on the webUI
		And the user reloads the current page of the webUI
		Then the file "atextfile.txt" should be listed on the webUI
		And the user opens the file "atextfile.txt" using the webUI
		Then there are 6 lines of text
		And line 1 of the text is "What is this?"
		And line 2 of the text is 'This is some "example" text!'
		And line 3 of the text is "That goes on some lines in a 'text' file."
		And line 4 of the text is ""
		And line 5 of the text is "नेपाल"
		And line 6 of the text is "1 2 3 4 5 6 7 8 9 0"

	Scenario: Create a text file with the default name and file extension in a sub-folder
		When the user opens the folder "simple-folder" using the webUI
		And I create a text file with the name ""
		And I input "stuff" in the text area
		And I close the text editor
		Then the file "New text file.txt" should be listed on the webUI
		And the user reloads the current page of the webUI
		Then the file "New text file.txt" should be listed on the webUI
		And the user opens the file "New text file.txt" using the webUI
		Then line 1 of the text is "stuff"
		And I input "other text before " in the text area
		And I close the text editor
		And the user opens the file "New text file.txt" using the webUI
		Then line 1 of the text is "other text before stuff"

	Scenario: Create a text file in a sub-folder using special characters in the names
		When the user creates a folder with the name "सिमप्ले फोल्देर $%#?&@" using the webUI
		And the user opens the folder "सिमप्ले फोल्देर $%#?&@" using the webUI
		And I create a text file with the name "सिमप्ले $%#?&@ name.txt"
		And I input "a line of text" in the text area
		And I close the text editor
		Then the file "सिमप्ले $%#?&@ name.txt" should be listed on the webUI
		And the user reloads the current page of the webUI
		Then the file "सिमप्ले $%#?&@ name.txt" should be listed on the webUI

	Scenario: Create a text file putting a name of a file which already exists
		When I create a text file with the name "lorem.txt"
		Then near the new text file box a tooltip with the text 'lorem.txt already exists' should be displayed

	Scenario: Create a text file named with forward slash
		When I create a text file with the name "simple-folder/a.txt"
		Then near the new text file box a tooltip with the text 'File name cannot contain "/".' should be displayed

	Scenario: Create a text file named ..
		When I create a text file with the name ".."
		Then near the new text file box a tooltip with the text '".." is an invalid file name.' should be displayed

	Scenario: Create a text file named .
		When I create a text file with the name "."
		Then near the new text file box a tooltip with the text '"." is an invalid file name.' should be displayed

	Scenario: Create a text file with file extension .part
		When I create a text file with the name "data.part"
		Then near the new text file box a tooltip with the text '"data.part" has a forbidden file type/extension.' should be displayed
