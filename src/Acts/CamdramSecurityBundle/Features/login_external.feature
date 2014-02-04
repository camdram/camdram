Feature: Logging in as an external user
  In order to log in with a social media account
  I need to be able to log with an external user

Scenario: Log in using an existing Facebook user
  Given the external "Facebook" user:
      | username  | test.facebook.user                           |
      | name      | Test Facebook User                           |
      | email     | test@facebook.com                            |
  When I log in using "Facebook" as "test.facebook.user"
  Then I should see "Test Facebook User" in the "#account-link" element

Scenario: Log in using a Google user that does not already exist
  When I log in using "Google" as "test.google.user" with name "Test Google User"
  Then I should see "Test Google User" in the "#account-link" element

Scenario: Log in using a Raven user
  When I log in using "Raven" as "abc123"
  Then I should see "abc123" in the "#account-link" element