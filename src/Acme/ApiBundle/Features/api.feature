Feature: My Own API
  In Order to test my own API service
  As a developer
  I want to service Google Maps geolocalization

  Scenario: Call with no paramas
    Given I call "http://api.symfony.local/app_dev.php/api/"
    Then I get a response
    And the response status code should be "200"
    And the response is JSON

  Scenario: API call with my address
    Given I set my address as "Joan Pallares 19 Hospitalet del Llobregat"
    And I call "http://api.symfony.local/app_dev.php/api/"
    Then I get a response
    And the response status code should be "200"
    And the response is JSON
    And the post code is "08901"


