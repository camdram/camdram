Feature: Homepage Diary
  In order to quickly see what's on in the next week
  As any user
  I need a diary view on the home page

Scenario: List 2 files in a directory
    Given I am on the homepage
    Then I should see "February Week 4"
    And I should see "Week 1"
    And I should see "Week 2"