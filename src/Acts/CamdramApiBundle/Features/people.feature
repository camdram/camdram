Feature: REST API for people

  Scenario: Retrieve a person in XML format
    Given the person "Test person"
    When I send a GET request to "/people/test-person.xml"
    Then the response code should be 200
    And the response type should be "text/xml"

  Scenario: Retrieve a person in JSON format
    Given the person "Test person"
    When I send a GET request to "/people/test-person.json"
    Then the response code should be 200
    And the response type should be "application/json"

  Scenario: Create a new person
    Given I have an OAuth access token
    When I send a POST request to "/people.json" with the "person" data
        | name          | Test person            |
    Then the response code should be 403

  Scenario: Edit an existing person
    Given the person "Test person"
    And "authoriser@camdram.net" is linked to the person "Test person"
    And I have an OAuth access token
    When I send a PUT request to "/people/test-person.json" with the "person" data
      | name          | New name                |
    Then the response code should be 201
    And when I go to the location
    Then the response key "name" should equal "New name"
    And the response key "slug" should equal "new-name"

  Scenario: Patch an existing person
    Given the person "Test person"
    And "authoriser@camdram.net" is linked to the person "Test person"
    And I have an OAuth access token
    When I send a PATCH request to "/people/test-person.json" with the "person" data
      | name          | New name                |
    Then the response code should be 201
    And when I go to the location
    Then the response key "name" should equal "New name"
    And the response key "slug" should equal "new-name"