Feature: Logging in
  In order to customise Camdram's content
  I need to be able to log in

Scenario: Log in as ordinary user
  Given I am logged in
  Then I should see "Test User 1" in the ".top-bar" element

Scenario: Log in as an administrator
  Given I am an administrator
  Then I should see "Admin User" in the ".top-bar" element
  And I should see "Administration" in the ".top-bar" element