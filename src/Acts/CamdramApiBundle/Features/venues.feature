Feature: REST API for venues

  Scenario: Retrieve a venue in XML format
    Given the venue "Test venue"
    When I send a GET request to "/venues/test-venue.xml"
    Then the response code should be 200
    And the response type should be "text/xml"

  Scenario: Retrieve a venue in JSON format
    Given the venue "Test venue"
    When I send a GET request to "/venues/test-venue.json"
    Then the response code should be 200
    And the response type should be "application/json"

  Scenario: Create a new venue
    Given I have an OAuth access token
    When I send a POST request to "/venues.json" with the "venue" data
        | name          | Test venue            |
        | description   | Lorem ipsum             |
    Then the response code should be 403

  Scenario: Edit an existing venue
    Given the venue "Test venue"
    And "authoriser@camdram.net" is the owner of the venue "Test venue"
    And I have an OAuth access token
    When I send a PUT request to "/venues/test-venue.json" with the "venue" data
      | name          | New name                |
    Then the response code should be 201
    And when I go to the location
    Then the response key "name" should equal "New name"
    And the response key "slug" should equal "new-name"

  Scenario: Patch an existing venue
    Given the venue "Test venue"
    And "authoriser@camdram.net" is the owner of the venue "Test venue"
    And I have an OAuth access token
    When I send a PATCH request to "/venues/test-venue.json" with the "venue" data
      | name          | New name                |
    Then the response code should be 201
    And when I go to the location
    Then the response key "name" should equal "New name"
    And the response key "slug" should equal "new-name"
    And the response key "short_name" should equal "Test venue"