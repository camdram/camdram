Feature: REST API for shows

  Scenario: Retrieve a show in XML format
    Given the show "Test Show" with society "Test Society" and venue "Test Venue"
    When I send a GET request to "/shows/test-show.xml"
    Then the response code should be 200
    And the response type should be "text/xml"

  Scenario: Retrieve a show in JSON format
    Given the show "Test Show" with society "Test Society" and venue "Test Venue"
    When I send a GET request to "/shows/test-show.json"
    Then the response code should be 200
    And the response type should be "application/json"

  Scenario: Create a new show
    Given I have an OAuth access token
    When I send a POST request to "/shows.json" with the "show" data
        | name          | Test Show               |
        | description   | Lorem ipsum             |
        | category      | drama                   |
    Then the response code should be 201
    And the response type should be "application/json"
    And when I go to the location
    Then the response key "name" should equal "Test Show"
    And the response key "slug" should equal "test-show"
    And the response key "slug" should equal "test-show"

  Scenario: Edit an existing show
    Given the show "Test Show" with society "Test Society" and venue "Test Venue"
    And "authoriser@camdram.net" is the owner of the show "Test Show"
    And I have an OAuth access token
    When I send a PUT request to "/shows/test-show.json" with the "show" data
      | name          | New name                |
      | description   | Lorem ipsum             |
      | category      | drama                   |
    Then the response code should be 201
    And when I go to the location
    Then the response key "name" should equal "New name"
    And the response key "slug" should equal "new-name"

  Scenario: Delete a show
    Given the show "Test Show" with society "Test Society" and venue "Test Venue"
    And "authoriser@camdram.net" is the owner of the show "Test Show"
    And I have an OAuth access token
    When I send a DELETE request to "/shows/test-show.json"
    Then the response code should be 204