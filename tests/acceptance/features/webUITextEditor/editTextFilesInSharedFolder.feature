@webUI @insulated
Feature: edit text files received in user, group and public link shared folders

  Background:
    Given these users have been created without skeleton files:
      | username |
      | Alice    |
      | Brian    |
    And user "Alice" has created folder "/FOLDER"
    And user "Alice" has uploaded file with content "stuff" to "/FOLDER/textfile0.txt"


  Scenario: Edit a text file in a received user share
    Given user "Alice" has shared folder "/FOLDER" with user "Brian"
    And user "Brian" has logged in using the webUI
    When the user opens folder "FOLDER" using the webUI
    And the user opens file "textfile0.txt" using the webUI
    And the user inputs "other text before " in the text area
    And the user closes the text editor
    And the user opens file "textfile0.txt" using the webUI
    Then line 1 of the text should be "other text before stuff"
    And the content of file "/FOLDER/textfile0.txt" for user "Alice" should be "other text before stuff"


  Scenario: Edit a text file in a received group share
    Given group "grp1" has been created
    And user "Brian" has been added to group "grp1"
    And user "Alice" has shared folder "/FOLDER" with group "grp1"
    And user "Brian" has logged in using the webUI
    When the user opens folder "FOLDER" using the webUI
    And the user opens file "textfile0.txt" using the webUI
    And the user inputs "other text before " in the text area
    And the user closes the text editor
    And the user opens file "textfile0.txt" using the webUI
    Then line 1 of the text should be "other text before stuff"
    And the content of file "/FOLDER/textfile0.txt" for user "Alice" should be "other text before stuff"


  Scenario: Try to edit a text file in a received read-only user share
    Given user "Alice" has shared folder "/FOLDER" with user "Brian" with permissions "read"
    And user "Brian" has logged in using the webUI
    When the user opens folder "FOLDER" using the webUI
    And the user opens file "textfile0.txt" using the webUI
    # Brian only has read access, so the attempt at typing in the text area should result in no change to the file
    And the user inputs "other text before " in the text area
    And the user closes the text editor
    And the user opens file "textfile0.txt" using the webUI
    Then line 1 of the text should be "stuff"
    And the content of file "/FOLDER/textfile0.txt" for user "Alice" should be "stuff"


  Scenario: Try to edit a text file in a received read-only group share
    Given group "grp1" has been created
    And user "Brian" has been added to group "grp1"
    And user "Alice" has shared folder "/FOLDER" with group "grp1" with permissions "read"
    And user "Brian" has logged in using the webUI
    When the user opens folder "FOLDER" using the webUI
    And the user opens file "textfile0.txt" using the webUI
    # grp1 only has read access, so the attempt at typing in the text area should result in no change to the file
    And the user inputs "other text before " in the text area
    And the user closes the text editor
    And the user opens file "textfile0.txt" using the webUI
    Then line 1 of the text should be "stuff"
    And the content of file "/FOLDER/textfile0.txt" for user "Alice" should be "stuff"


  Scenario: Edit a text file in a public link share
    Given user "Alice" has created a public link share with settings
      | path        | /FOLDER |
      | permissions | change  |
    When the public accesses the last created public link using the webUI
    And the user opens file "textfile0.txt" using the webUI
    And the user inputs "other text before " in the text area
    And the user closes the text editor
    And the user opens file "textfile0.txt" using the webUI
    Then line 1 of the text should be "other text before stuff"
    And the content of file "/FOLDER/textfile0.txt" for user "Alice" should be "other text before stuff"


  Scenario: Try to edit a text file in a read-only public link share
    Given user "Alice" has created a public link share with settings
      | path        | /FOLDER |
      | permissions | read    |
    When the public accesses the last created public link using the webUI
    And the user opens file "textfile0.txt" using the webUI
    # the public link only has read access, so the attempt at typing in the text area should result in no change to the file
    And the user inputs "other text before " in the text area
    And the user closes the text editor
    And the user opens file "textfile0.txt" using the webUI
    Then line 1 of the text should be "stuff"
    And the content of file "/FOLDER/textfile0.txt" for user "Alice" should be "stuff"
