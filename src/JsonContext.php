<?php

declare(strict_types=1);

namespace Alsciende\Behat;

use Alsciende\Behat\Json\Json;
use Alsciende\Behat\Json\JsonInspector;
use Alsciende\Behat\Json\JsonSchema;
use Assert\Assertion;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;

/**
 * @see https://github.com/ubirak/rest-api-behat-extension
 */
class JsonContext implements Context
{
    use DataStoreContextGatheringTrait;

    /**
     * @var JsonInspector
     */
    private $jsonInspector;

    /**
     * @var string
     */
    private $jsonSchemaBaseUrl;

    public function __construct($jsonSchemaBaseUrl = '')
    {
        $this->jsonInspector = new JsonInspector();
        $this->jsonSchemaBaseUrl = rtrim($jsonSchemaBaseUrl, '/');
    }

    /**
     * @When /^I load JSON:$/
     */
    public function iLoadJson(PyStringNode $jsonContent)
    {
        $this->jsonInspector->writeJson((string) $jsonContent);
    }

    /**
     * @When /^I load the response as JSON$/
     */
    public function iLoadTheResponseAsJson()
    {
        $this->jsonInspector->writeJson($this->dataStoreContext['response']['content']);
    }

    /**
     * @Then /^the JSON should be valid$/
     */
    public function responseShouldBeInJson()
    {
        $this->jsonInspector->readJson();
    }

    /**
     * @Then /^the JSON node "(?P<jsonNode>[^"]*)" should be equal to "(?P<expectedValue>.*)"$/
     */
    public function theJsonNodeShouldBeEqualTo($jsonNode, $expectedValue)
    {
        $realValue = $this->evaluateJsonNodeValue($jsonNode);
        $expectedValue = $this->evaluateExpectedValue($expectedValue);
        Assertion::eq($realValue, $expectedValue);
    }

    /**
     * @param string $jsonNode
     *
     * @return array|mixed
     *
     * @throws \Exception
     */
    private function evaluateJsonNodeValue(string $jsonNode)
    {
        return $this->jsonInspector->readJsonNodeValue($jsonNode);
    }

    private function evaluateExpectedValue($expectedValue)
    {
        if (in_array($expectedValue, ['true', 'false'])) {
            return filter_var($expectedValue, FILTER_VALIDATE_BOOLEAN);
        }

        if ('null' === $expectedValue) {
            return null;
        }

        return $expectedValue;
    }

    /**
     * @Then /^the JSON node "(?P<jsonNode>[^"]*)" should have (?P<expectedNth>\d+) elements?$/
     * @Then /^the JSON array node "(?P<jsonNode>[^"]*)" should have (?P<expectedNth>\d+) elements?$/
     */
    public function theJsonNodeShouldHaveElements($jsonNode, int $expectedNth)
    {
        $realValue = $this->evaluateJsonNodeValue($jsonNode);
        Assertion::isArray($realValue);
        Assertion::count($realValue, $expectedNth);
    }

    /**
     * @Then /^the JSON array node "(?P<jsonNode>[^"]*)" should contain "(?P<expectedValue>.*)" element$/
     */
    public function theJsonArrayNodeShouldContainElements($jsonNode, $expectedValue)
    {
        $realValue = $this->evaluateJsonNodeValue($jsonNode);
        Assertion::isArray($realValue);
        Assertion::inArray($expectedValue, $realValue);
    }

    /**
     * @Then /^the JSON array node "(?P<jsonNode>[^"]*)" should not contain "(?P<expectedValue>.*)" element$/
     */
    public function theJsonArrayNodeShouldNotContainElements($jsonNode, $expectedValue)
    {
        $realValue = $this->evaluateJsonNodeValue($jsonNode);
        Assertion::isArray($realValue);
        Assertion::notInArray($expectedValue, $realValue);
    }

    /**
     * @Then /^the JSON node "(?P<jsonNode>[^"]*)" should contain "(?P<expectedValue>.*)"$/
     */
    public function theJsonNodeShouldContain($jsonNode, $expectedValue)
    {
        $realValue = $this->evaluateJsonNodeValue($jsonNode);
        Assertion::contains($realValue, $expectedValue);
    }

    /**
     * Checks, that given JSON node does not contain given value.
     *
     * @Then /^the JSON node "(?P<jsonNode>[^"]*)" should not contain "(?P<unexpectedValue>.*)"$/
     */
    public function theJsonNodeShouldNotContain($jsonNode, $unexpectedValue)
    {
        $realValue = $this->evaluateJsonNodeValue($jsonNode);
        Assertion::false(strstr($realValue, $unexpectedValue));
    }

    /**
     * Checks, that given JSON node exist.
     *
     * @Given /^the JSON node "(?P<jsonNode>[^"]*)" should exist$/
     */
    public function theJsonNodeShouldExist($jsonNode)
    {
        try {
            $this->evaluateJsonNodeValue($jsonNode);
        } catch (\Exception $e) {
            throw new \Exception(sprintf("The node '%s' does not exist.", $jsonNode), 0, $e);
        }
    }

    private function readJson()
    {
        return $this->jsonInspector->readJson();
    }

    /**
     * Checks, that given JSON node does not exist.
     *
     * @Given /^the JSON node "(?P<jsonNode>[^"]*)" should not exist$/
     */
    public function theJsonNodeShouldNotExist($jsonNode)
    {
        try {
            $realValue = $this->evaluateJsonNodeValue($jsonNode);
        } catch (\Exception $e) {
            return;
        }

        throw new \Exception(
            sprintf("The node '%s' exists and contains '%s'.", $jsonNode, json_encode($realValue))
        );
    }

    /**
     * @Then /^the JSON should be valid according to this schema:$/
     */
    public function theJsonShouldBeValidAccordingToThisSchema(PyStringNode $jsonSchemaContent)
    {
        $tempFilename = tempnam(sys_get_temp_dir(), 'rae');
        if (false === $tempFilename) {
            throw new \Exception('Cannot write in ' . sys_get_temp_dir());
        }

        file_put_contents($tempFilename, $jsonSchemaContent);

        $this->jsonInspector->validateJson(new JsonSchema($tempFilename));

        unlink($tempFilename);
    }

    /**
     * @Then /^the JSON should be valid according to the schema "(?P<filename>[^"]*)"$/
     */
    public function theJsonShouldBeValidAccordingToTheSchema($filename)
    {
        $filename = $this->resolveFilename($filename);

        $this->jsonInspector->validateJson(new JsonSchema($filename));
    }

    private function resolveFilename($filename)
    {
        if (true === is_file($filename)) {
            return realpath($filename);
        }

        if (null === $this->jsonSchemaBaseUrl) {
            throw new \RuntimeException(sprintf(
                'The JSON schema file "%s" doesn\'t exist',
                $filename
            ));
        }

        $filename = $this->jsonSchemaBaseUrl . '/' . $filename;

        if (false === is_file($filename)) {
            throw new \RuntimeException(sprintf(
                'The JSON schema file "%s" doesn\'t exist',
                $filename
            ));
        }

        return realpath($filename);
    }

    /**
     * @Then /^the JSON should be equal to:$/
     */
    public function theJsonShouldBeEqualTo(PyStringNode $jsonContent)
    {
        $realJsonValue = $this->readJson();

        try {
            $expectedJsonValue = new Json($jsonContent);
        } catch (\Exception $e) {
            throw new \Exception('The expected JSON is not a valid');
        }

        Assertion::eq($realJsonValue, $expectedJsonValue);
    }
}
