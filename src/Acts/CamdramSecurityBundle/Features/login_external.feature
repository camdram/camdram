Feature: Logging in as an external user
  @cleanUsers @reset
  In order to log in with a social media account
  I need to be able to log with an external user

Scenario: Log in as using a test Facebook user
  When I log in using "Facebook"
  Then I should see "Test Facebook User" in the "#account-link" element

Scenario: Log in as using a test Google user
  When I log in using "Google"
  Then I should see "Test Google User" in the "#account-link" element

Scenario: Log in as using a test Raven external
  When I log in using "Raven"
  Then I should see "abc123" in the "#account-link" element

Scenario: Log in as using a test Facebook user
    Given I am logged in using "Facebook"
    Then I log out
    And I am logged in using "Facebook"
    Then I should see "Test Facebook User" in the "#account-link" element