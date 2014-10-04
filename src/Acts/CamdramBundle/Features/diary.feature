Feature: Diary page
    To get an overview of events happening now, in the future and in the past
    For any user
    I need a diary page

  Scenario: I view the diary homepage
    Given the show "Test Show" starting in 1 day and lasting 4 days at 19:30
    And the show "Test Show 2" starting in 2 days and lasting 1 day at 14:00
    When I go to "/diary"
    Then I should see "Test Show" in the "#diary" element
    And I should see "Test Show 2" in the "#diary" element

  Scenario: I view a particular date in the diary
    Given the show "Test Show" starting in -7 days and lasting 2 days at 19:30
    When I go to "/diary/2000-06-25?end=2000-07-01"
    Then I should see "Test Show" in the "#diary" element

  Scenario: I view a particular year in the diary
    Given the show "Test Show" starting in -30 days and lasting 7 days at 19:30
    When I go to "/diary/2000?end=2000-12-30"
    Then I should see "Test Show" in the "#diary" element

  Scenario: I view a particular period in the diary
    Given the show "Test Show" starting in -14 days and lasting 2 days at 19:30
    And the time period "Test Period" from "2000-06-01" to "2000-07-15"
    When I go to "/diary/2000/test-period"
    Then I should see "Test Show" in the "#diary" element
    And I should see "Test Period" in the ".diary-period-label" element