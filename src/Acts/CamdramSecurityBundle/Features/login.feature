Feature: Logging in
  In order to customise Camdram's content
  I need to be able to log in

  Scenario: Log in as ordinary user
    Given the user "John Smith" with the email "user1@camdram.net" and the password "password1"
    When I log in as "user1@camdram.net" with "password1"
    Then I should see "John Smith" in the "#account-link" element

  Scenario: Log in as an administrator
    Given the administrator "Fred Smith" with the email "admin@camdram.net" and the password "password2"
    When I log in as "admin@camdram.net" with "password2"
    Then I should see "Fred Smith" in the "#account-link" element
    And I should see "Administration" in the "#admin-link" element

  Scenario: Logging out
    Given the user "John Smith" with the email "user1@camdram.net" and the password "password1"
    And I log in as "user1@camdram.net" with "password1"
    Then I log out
    Then I should see "Log In" in the "#login-link" element