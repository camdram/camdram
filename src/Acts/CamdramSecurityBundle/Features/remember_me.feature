Feature: Logging in with 'remember me' option

  Background:
    Given the user "John Smith" with the email "user1@camdram.net" and the password "password1"
    And I go to "/auth/login"
    And I fill in "Email" with "user1@camdram.net"
    And I fill in "Password" with "password1"
    And I check "form_remember_me"
    And I press "Log in"

  Scenario: Logging in with 'remember me' option
    When I delete the session cookie
    And I reload the page
    Then I should see "John Smith" in the "#account-link" element

  Scenario: Account settings page causes re-authentication
    And I delete the session cookie
    And I go to "/auth/settings"
    Then I should see "you must log in again"

  Scenario: Remember me cookie remains after re-authentication
    And I delete the session cookie
    And I go to "/auth/settings"
    And I fill in "Password" with "password1"
    And I press "Log in"
    And I delete the session cookie
    And I go to the homepage
    Then I should see "John Smith" in the "#account-link" element