Feature: Logging in as an external user
  In order to log in with a social media account
  I need to be able to log with an external user

Scenario: Log in using a Facebook user
  When I log in using "Facebook" as "Test Facebook User"
  Then I should see "Test Facebook User" in the "#account-link" element

Scenario: Log in using a Google user
  When I log in using "Google" as "Test Google User"
  Then I should see "Test Google User" in the "#account-link" element

Scenario: Log in using a Raven user
  When I log in using "Raven" as ""
  Then I should see "abc123" in the "#account-link" element