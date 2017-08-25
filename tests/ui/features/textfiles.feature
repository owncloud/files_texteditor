Feature: textFiles

	Background:
		Given a regular user exists
		And I am logged in as a regular user
		And I am on the files page

	Scenario Outline: Create a text file
		When I create a text file with the name <file_name>
		And the files page is reloaded
		Then the file <file_name> should be listed
		Examples:
		|file_name    |
		|'सिमप्ले text file.txt'|
		|'"somequotes1" text.txt'|
		|"'somequotes2' text.txt"|

	Scenario: Create a text file with text in it
		When I create a text file with the name "atextfile.txt"
		And I enter "stuff here" in the text file
		And the files page is reloaded
		Then the file "atextfile.txt" should be listed
