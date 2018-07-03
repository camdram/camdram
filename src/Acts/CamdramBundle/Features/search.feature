Feature: Search and autocomplete

  Background:
    Given the show "Test Show" with society "Test Society" and venue "Test Venue"
    And the show "Test Show 2" with society "Test Society 2" and venue "Test Venue 2"

  Scenario: I perform a full text search
    Given I am on the homepage
    When I fill in "searchfield" with "test"
    And I press "Search"
    Then I should see "Test Show" in the "#content" element
    And I should see "Test Show 2" in the "#content" element
    And I should see "Test Society" in the "#content" element
    And I should see "Test Society 2" in the "#content" element
    And I should see "Test Venue" in the "#content" element
    And I should see "Test Venue 2" in the "#content" element

  Scenario: I use the autocomplete callback
    When I go to "/search.json?q=test"
    Then the response status code should be 200
    And the response should contain "Test Show"
    And the response should contain "Test Show 2"
    And the response should contain "Test Society"
    And the response should contain "Test Society 2"
    And the response should contain "Test Venue"
    And the response should contain "Test Venue 2"