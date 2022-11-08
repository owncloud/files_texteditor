@webUI @insulated
Feature: textFiles

  Background:
    Given these users have been created without skeleton files:
      | username |
      | Alice    |
    And user "Alice" has logged in using the webUI
    And the user has browsed to the files page


  Scenario: Edit a text file with the default name and file extension in a sub-folder
    When the user creates a folder with the name "simple-folder" using the webUI
    And the user opens folder "simple-folder" using the webUI
    And the user creates a text file with the name "" using the webUI
    And the user inputs "stuff" in the text area
    And the user closes the text editor
    Then file "New text file.txt" should be listed on the webUI
    And the user reloads the current page of the webUI
    Then file "New text file.txt" should be listed on the webUI
    And the user opens file "New text file.txt" using the webUI
    Then line 1 of the text should be "stuff"
    And the user inputs "other text before " in the text area
    And the user closes the text editor
    And the user opens file "New text file.txt" using the webUI
    Then line 1 of the text should be "other text before stuff"

  @issue-core-36233
  Scenario: Edit restored hidden text file
    Given user "Alice" has uploaded file with content "This is a hidden file" to "/.abc.txt"
    And the user has enabled the setting to view hidden files on the webUI
    When user "Alice" deletes file "/.abc.txt" using the WebDAV API
    And user "Alice" restores the file with original path "/.abc.txt" using the trashbin API
    And the user browses to the files page
    Then as "Alice" file "/.abc.txt" should exist
    When user "Alice" downloads file "/.abc.txt" using the WebDAV API
    Then the downloaded content should be "This is a hidden file"
#    When the user opens file "/.abc.txt" using the webUI
#    Then line 1 of the text should be "This is a hidden file"

  @issue-core-36233
  Scenario: Edit hidden text file
    Given the user has enabled the setting to view hidden files on the webUI
    When the user creates a text file with the name ".abc.txt" using the webUI
    And the user inputs "This is a hidden file" in the text area
    And the user closes the text editor
    Then file ".abc.txt" should be listed on the webUI
    When user "Alice" downloads file "/.abc.txt" using the WebDAV API
    Then the downloaded content should be "This is a hidden file"
#    When the user opens file ".abc.txt" using the webUI
#    Then line 1 of the text should be "This is a hidden file"
