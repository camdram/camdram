Feature: REST API for societies

  Scenario: Retrieve a society in XML format
    Given the society "Test Society"
    When I send a GET request to "/societies/test-society.xml"
    Then the response code should be 200
    And the response type should be "text/xml"

  Scenario: Retrieve a society in JSON format
    Given the society "Test Society"
    When I send a GET request to "/societies/test-society.json"
    Then the response code should be 200
    And the response type should be "application/json"

  Scenario: Create a new society
    Given I have an OAuth access token
    When I send a POST request to "/societies.json" with the "society" data
        | name          | Test Society            |
        | description   | Lorem ipsum             |
    Then the response code should be 403

  Scenario: Edit an existing society
    Given the society "Test Society"
    And "authoriser@camdram.net" is the owner of the society "Test Society"
    And I have an OAuth access token
    When I send a PUT request to "/societies/test-society.json" with the "society" data
      | name          | New name                |
    Then the response code should be 201
    And when I go to the location
    Then the response key "name" should equal "New name"
    And the response key "slug" should equal "new-name"

  Scenario: Patch an existing society
    Given the society "Test Society"
    And "authoriser@camdram.net" is the owner of the society "Test Society"
    And I have an OAuth access token
    When I send a PATCH request to "/societies/test-society.json" with the "society" data
      | name          | New name                |
    Then the response code should be 201
    And when I go to the location
    Then the response key "name" should equal "New name"
    And the response key "slug" should equal "new-name"
    And the response key "short_name" should equal "Test Society"