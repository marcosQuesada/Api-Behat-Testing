Feature: GeoCode API
  In Order to test Geo Localization
  As a developer
  I want to get the Postal Code of my home address

  Scenario:
    Given My Home Address is "Joan Pallares 19 Hospitalet del Llobregat"
    And I call GeoCode API
    Then I get a response
    And the response is JSON
    And the response code is 200
    And the response has "results" key
    And the "results" has "address_components"
    And in "Address Components" has "Postal Code"
    And Postal Code is "08901"