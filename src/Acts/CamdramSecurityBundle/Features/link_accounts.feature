Feature: Linking user accounts together
  In order to be able to log into a Camdram account using Facebook/Google/Raven
  I need to be able to link external accounts to Camdram accounts

  Background:
    Given the user "John Smith" with the email "xyz987@cam.ac.uk" and the password "password"

  Scenario: I log into Facebook after logging into a Camdram account
    Given I am logged in as "xyz987@cam.ac.uk" with "password"
    When I log in using "Facebook" as "test.facebook.user" with name "John Smith"
    And I go to "/auth/settings"
    Then I should see "Your Camdram account is linked to the Facebook account test.facebook.user"

  Scenario: I log into Google after logging into a Camdram account
    Given I am logged in as "xyz987@cam.ac.uk" with "password"
    When I log in using "Google" as "test.google.user" with name "John Smith"
    And I go to "/auth/settings"
    Then I should see "Your Camdram account is linked to the Google account test.google.user"

  Scenario: I log into using Raven after logging into a Camdram account
    Given I am logged in as "xyz987@cam.ac.uk" with "password"
    When I log in using "Raven" as "abc123"
    And I press "Link the two accounts together"
    And I go to "/auth/settings"
    Then I should see "Your Camdram account is linked to the Raven account abc123"

  Scenario: I log into using Raven after logging into Camdram, but choose to stay logged into Camdram
    Given I am logged in as "xyz987@cam.ac.uk" with "password"
    When I log in using "Raven" as "abc123"
    And I press "old"
    And I go to "/auth/settings"
    Then I should not see "Your Camdram account is linked to the Raven account abc123"

  Scenario: I log into using Raven after logging into Camdram, but choose to stay logged into Raven
    Given I am logged in as "xyz987@cam.ac.uk" with "password"
    When I log in using "Raven" as "abc123"
    And I press "new"
    Then I should see "abc123" in the "#account-link" element

  Scenario: I log into using Raven after logging into Camdram and the email address matches
    Given I am logged in as "xyz987@cam.ac.uk" with "password"
    When I log in using "Raven" as "xyz987" with email "xyz987@cam.ac.uk"
    And I go to "/auth/settings"
    Then I should see "Your Camdram account is linked to the Raven account xyz987"