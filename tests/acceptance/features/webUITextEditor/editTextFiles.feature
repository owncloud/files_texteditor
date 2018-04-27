@webUI @insulated
Feature: textFiles

	Background:
		Given these users have been created:
			|username|password|displayname|email       |
			|user1   |1234    |User One   |u1@oc.com.np|
		And the user has browsed to the login page
		And the user has logged in with username "user1" and password "1234" using the webUI
		And the user has browsed to the files page

	Scenario: Edit a text file with the default name and file extension in a sub-folder
		When the user opens the folder "simple-folder" using the webUI
		And the user creates a text file with the name "" using the webUI
		And the user inputs "stuff" in the text area
		And the user closes the text editor
		Then the file "New text file.txt" should be listed on the webUI
		And the user reloads the current page of the webUI
		Then the file "New text file.txt" should be listed on the webUI
		And the user opens the file "New text file.txt" using the webUI
		Then line 1 of the text should be "stuff"
		And the user inputs "other text before " in the text area
		And the user closes the text editor
		And the user opens the file "New text file.txt" using the webUI
		Then line 1 of the text should be "other text before stuff"
