@webUI @insulated @disablePreviews @activity-app-required
Feature: log activity of actions done by the texteditor app

  Background:
    Given these users have been created without skeleton files:
      | username |
      | Alice    |
    And user "Alice" has uploaded file with content "anything" to "/lorem.txt"
    And user "Alice" has logged in using the webUI
    And the user has browsed to the files page


  Scenario: creating a new text file should be listed in the activity list
    Given the user has created a text file with the name "activityfile.txt"
    And the user has input "stuff" in the text area
    And the user has closed the text editor
    When the user browses to the activity page
    Then the activity number 1 should contain message "You changed activityfile.txt" in the activity page
    And the activity number 2 should contain message "You created activityfile.txt" in the activity page


  Scenario: editing an existing text file should be listed in the activity list
    Given the user has opened file "lorem.txt" using the webUI
    And the user has input "changed text" in the text area
    And the user has closed the text editor
    When the user browses to the activity page
    Then the activity number 1 should contain message "You changed lorem.txt" in the activity page
