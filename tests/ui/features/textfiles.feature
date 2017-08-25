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