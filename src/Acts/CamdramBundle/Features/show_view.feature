Feature: Editing shows
  In order for users to be able to edit shows on Camdram
  I need to be able to create a user account

Background:
  Given the user "Fred Smith" with the email "user1@camdram.net" and the password "password"
  And the external "Raven" user:
    | username  | fs123                 |
    | email     | fs123@cam.ac.uk       |
    | email     | test@facebook.com     |
    | user      | user1@camdram.net     |
  And the show "Test Show" with society "Test Society" and venue "Test Venue"

  Scenario: View show page
    Given I am on "/shows/test-show"
    Then I should see "Test Show"
    And I should not see "Edit this show"

  Scenario: View show page as show owner
    Given "user1@camdram.net" is the owner of the show "Test Show"
    When I am logged in as "user1@camdram.net" with "password"
    And I am on "/shows/test-show"
    Then I should see "Test Show"
    And I should see "Edit this show"

  Scenario: View show page as show owner when logged as an admin
    Given the administrator "Admin User" with the email "admin@camdram.net" and the password "password"
    When I am logged in as "admin@camdram.net" with "password"
    And I am on "/shows/test-show"
    Then I should see "Test Show"
    And I should see "Edit this show"

  Scenario: Edit show as show owner
    Given "user1@camdram.net" is the owner of the show "Test Show"
    When I am logged in as "user1@camdram.net" with "password"
    And I am on "/shows/test-show/edit"
    Then I should see "Edit Show"
    And the "Name" field should contain "Test Show"
