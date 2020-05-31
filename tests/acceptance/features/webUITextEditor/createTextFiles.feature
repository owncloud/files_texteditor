@webUI @insulated
Feature: textFiles

  Background:
    Given these users have been created with skeleton files:
      | username |
      | Alice    |
    And user "Alice" has logged in using the webUI
    And the user has browsed to the files page

  Scenario Outline: Create a text file
    When the user creates a text file with the name <file_name> using the webUI
    And the user inputs <example_text> in the text area
    And the user closes the text editor
    Then file <file_name> should be listed on the webUI
    And the user reloads the current page of the webUI
    Then file <file_name> should be listed on the webUI
    And the user opens file <file_name> using the webUI
    Then there should be 1 line of text in the text area
    And line 1 of the text should be <example_text>
    Examples:
      | file_name                | example_text                      |
      | 'सिमप्ले text file.txt'  | 'some text'                       |
      | '"somequotes1" text.txt' | 'John said,"hello."'              |
      | "'somequotes2' text.txt" | 'special !@#$%^&*()[]{}\|-_+=\/?' |

  Scenario: Create a text file with the default name and file extension
    When the user creates a text file with the name "" using the webUI
    And the user inputs "stuff" in the text area
    And the user closes the text editor
    Then file "New text file.txt" should be listed on the webUI
    And the user reloads the current page of the webUI
    Then file "New text file.txt" should be listed on the webUI

  Scenario: Create a text file with the default file extension and do not close the editor
    When the user creates a text file with the name "abc" using the webUI without changing the default file extension
    And the user inputs "something" in the text area
    Then file "abc.txt" should be listed on the webUI
    And the user reloads the current page of the webUI
    Then file "abc.txt" should be listed on the webUI

  Scenario: Create a text file with the default file extension and unicode file name
    When the user creates a text file with the name "सिमप्ले text file" using the webUI without changing the default file extension
    And the user inputs "नेपाल" in the text area
    And the user closes the text editor
    Then file "सिमप्ले text file.txt" should be listed on the webUI
    And the user reloads the current page of the webUI
    Then file "सिमप्ले text file.txt" should be listed on the webUI
    And the user opens file "सिमप्ले text file.txt" using the webUI
    Then there should be 1 line of text in the text area
    And line 1 of the text should be "नेपाल"

  Scenario: Create a text file with multiple lines of text in it
    When the user creates a text file with the name "atextfile.txt" using the webUI
    And the user inputs the following text in the text area:
      """
      What is this?
      This is some "example" text!
      That goes on some lines in a 'text' file.

      नेपाल
      1 2 3 4 5 6 7 8 9 0
      """
    And the user closes the text editor
    Then file "atextfile.txt" should be listed on the webUI
    And the user reloads the current page of the webUI
    Then file "atextfile.txt" should be listed on the webUI
    And the user opens file "atextfile.txt" using the webUI
    Then there should be 6 lines of text in the text area
    And line 1 of the text should be "What is this?"
    And line 2 of the text should be 'This is some "example" text!'
    And line 3 of the text should be "That goes on some lines in a 'text' file."
    And line 4 of the text should be ""
    And line 5 of the text should be "नेपाल"
    And line 6 of the text should be "1 2 3 4 5 6 7 8 9 0"

  Scenario: Create a text file with the default name and file extension in a sub-folder
    When the user opens folder "simple-folder" using the webUI
    And the user creates a text file with the name "" using the webUI
    And the user inputs "stuff" in the text area
    And the user closes the text editor
    Then file "New text file.txt" should be listed on the webUI
    And the user reloads the current page of the webUI
    Then file "New text file.txt" should be listed on the webUI
    And the user opens file "New text file.txt" using the webUI
    Then line 1 of the text should be "stuff"

  Scenario: Create a text file in a sub-folder using special characters in the names
    When the user creates a folder with the name "सिमप्ले फोल्देर $%#?&@" using the webUI
    And the user opens folder "सिमप्ले फोल्देर $%#?&@" using the webUI
    And the user creates a text file with the name "सिमप्ले $%#?&@ name.txt" using the webUI
    And the user inputs "a line of text" in the text area
    And the user closes the text editor
    Then file "सिमप्ले $%#?&@ name.txt" should be listed on the webUI
    And the user reloads the current page of the webUI
    Then file "सिमप्ले $%#?&@ name.txt" should be listed on the webUI

  Scenario: Create a text file putting a name of a file which already exists
    When the user creates a text file with the name "lorem.txt" using the webUI
    Then near the new text file box a tooltip with the text 'lorem.txt already exists' should be displayed on the webUI

  Scenario: Create a text file named with forward slash
    When the user creates a text file with the name "simple-folder/a.txt" using the webUI
    Then near the new text file box a tooltip with the text 'File name cannot contain "/".' should be displayed on the webUI

  Scenario: Create a text file named ..
    When the user creates a text file with the name ".." using the webUI
    Then near the new text file box a tooltip with the text '".." is an invalid file name.' should be displayed on the webUI

  Scenario: Create a text file named .
    When the user creates a text file with the name "." using the webUI
    Then near the new text file box a tooltip with the text '"." is an invalid file name.' should be displayed on the webUI

  Scenario: Create a text file with file extension .part
    When the user creates a text file with the name "data.part" using the webUI
    Then near the new text file box a tooltip with the text '"data.part" has a forbidden file type/extension.' should be displayed on the webUI
