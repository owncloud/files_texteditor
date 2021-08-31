@webUI @insulated
Feature: edit text files received as user, group and public link shares

  Background:
    Given these users have been created without skeleton files:
      | username |
      | Alice    |
      | Brian    |
    And user "Alice" has uploaded file with content "stuff" to "/sharedFile.txt"


  Scenario: Edit a text file received as a user share
    Given user "Alice" has shared file "/sharedFile.txt" with user "Brian"
    And user "Brian" has logged in using the webUI
    When the user opens file "sharedFile.txt" using the webUI
    And the user inputs "other text before " in the text area
    And the user closes the text editor
    And the user opens file "sharedFile.txt" using the webUI
    Then line 1 of the text should be "other text before stuff"
    And the content of file "/sharedFile.txt" for user "Alice" should be "other text before stuff"


  Scenario: Edit a text file received as a group share
    Given group "grp1" has been created
    And user "Brian" has been added to group "grp1"
    And user "Alice" has shared file "/sharedFile.txt" with group "grp1"
    And user "Brian" has logged in using the webUI
    When the user opens file "sharedFile.txt" using the webUI
    And the user inputs "other text before " in the text area
    And the user closes the text editor
    And the user opens file "sharedFile.txt" using the webUI
    Then line 1 of the text should be "other text before stuff"
    And the content of file "/sharedFile.txt" for user "Alice" should be "other text before stuff"


  Scenario: Try to edit a text file received as a read-only user share
    Given user "Alice" has shared file "/sharedFile.txt" with user "Brian" with permissions "read"
    And user "Brian" has logged in using the webUI
    When the user opens file "sharedFile.txt" using the webUI
    # Brian only has read access, so the attempt at typing in the text area should result in no change to the file
    And the user inputs "other text before " in the text area
    And the user closes the text editor
    And the user opens file "sharedFile.txt" using the webUI
    Then line 1 of the text should be "stuff"
    And the content of file "/sharedFile.txt" for user "Alice" should be "stuff"


  Scenario: Try to edit a text file received as a read-only group share
    Given group "grp1" has been created
    And user "Brian" has been added to group "grp1"
    And user "Alice" has shared file "/sharedFile.txt" with group "grp1" with permissions "read"
    And user "Brian" has logged in using the webUI
    When the user opens file "sharedFile.txt" using the webUI
    # grp1 only has read access, so the attempt at typing in the text area should result in no change to the file
    And the user inputs "other text before " in the text area
    And the user closes the text editor
    And the user opens file "sharedFile.txt" using the webUI
    Then line 1 of the text should be "stuff"
    And the content of file "/sharedFile.txt" for user "Alice" should be "stuff"


  # Note: it is not possible to create a public link share of a single file and give it change permission
  #       so there is no scenario to test editing a public link share of a single file
  # This scenario just checks that a single file shared as a read-only public link share has the content displayed
  Scenario: View a text file received as a read-only public link share
    Given user "Alice" has created a public link share with settings
      | path        | /sharedFile.txt |
      | permissions | read            |
    When the public accesses the last created public link using the webUI
    # Note: in this case there is no option to open the text file in an online editor
    #       the file is read-only and the text is displayed directly on the page.
    Then the text preview of the public link should contain "stuff"
