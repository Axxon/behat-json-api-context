# Behat Context for JSON API 

## Installation

Add the package:
```
composer require --dev alsciende/behat-json-api-context
```

Add the Contexts to behat.yml:
```
contexts:
    - Alsciende\Behat\DataStoreContext
    - Alsciende\Behat\ApiContext:
          client: '@test.client'
    - Alsciende\Behat\JsonContext
```

###  JSON schema validation

Create a directory to store your JSON schemas:
```
mkdir -p features/json_schema/
```

Configure the context:
```
contexts:
    - Alsciende\Behat\JsonContext:
          jsonSchemaBaseUrl: 'features/json_schema/'
```

## Usage

The following directives are added to Behat:

```
Given the request header :header is :value
Given the request body is:
When I request :method :path
Then the response code is :code
Then the response header :header is :expected
Then the response content is valid JSON
Then /^the JSON node "(?P<jsonNode>[^"]*)" should be the string "(?P<expectedValue>.*)"$/
Then /^the JSON node "(?P<jsonNode>[^"]*)" should be the integer (?P<expectedValue>\d+)$/
Then /^the JSON node "(?P<jsonNode>[^"]*)" should be true$/
Then /^the JSON node "(?P<jsonNode>[^"]*)" should be false$/
Then /^the JSON node "(?P<jsonNode>[^"]*)" should be null$/
Then /^the JSON node "(?P<jsonNode>[^"]*)" should be an array$/
Then /^the JSON node "(?P<jsonNode>[^"]*)" should be an object$/
Then /^the JSON node "(?P<jsonNode>[^"]*)" should have (?P<expectedNth>\d+) elements?$/
Then /^the JSON node "(?P<jsonNode>[^"]*)" should exist$/
Then /^the JSON node "(?P<jsonNode>[^"]*)" should not exist$/
Then /^the JSON should be valid according to this schema:$/
Then /^the JSON should be valid according to the schema "(?P<filename>[^"]*)"$/
```