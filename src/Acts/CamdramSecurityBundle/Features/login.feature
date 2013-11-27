Feature: Logging in
  @reset
  In order to customise Camdram's content
  I need to be able to log in

Scenario: Log in as ordinary user
  When I log in as "user1@camdram.net"
  Then I should see "Test User 1" in the "#account-link" element

Scenario: Log in as an administrator
  When I log in as "admin@camdram.net"
  Then I should see "Admin User" in the "#account-link" element
  And I should see "Administration" in the "#admin-link" element

Scenario: I log out
  Given I am logged in as "user1@camdram.net"
  Then I log out
  Then I should see "Log In" in the "#login-link" element