<?php

declare(strict_types=1);

namespace Alsciende\Behat;

use Alsciende\Behat\Json\Json;
use Alsciende\Behat\Json\JsonInspector;
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
     * @var string
     */
    private $jsonSchemaBaseUrl;

    /**
     * @var JsonInspector
     */
    private $jsonInspector;

    public function __construct($jsonSchemaBaseUrl = '')
    {
        $this->jsonSchemaBaseUrl = rtrim($jsonSchemaBaseUrl, '/');
    }

    /**
     * @When /^I load JSON:$/
     */
    public function iLoadJson(PyStringNode $jsonContent)
    {
        $this->jsonInspector = new JsonInspector((string) $jsonContent);
    }

    /**
     * @When /^I load the response as JSON$/
     */
    public function iLoadTheResponseAsJson()
    {
        $this->jsonInspector = new JsonInspector($this->dataStoreContext['response']['content']);
    }

    /**
     * @Then /^the JSON node "(?P<jsonNode>[^"]*)" should be the string "(?P<expectedValue>.*)"$/
     */
    public function theJsonNodeShouldBeTheString(string $jsonNode, string $expectedValue)
    {
        $realValue = $this->jsonInspector->evaluate($jsonNode);
        Assertion::same($realValue, $expectedValue);
    }

    /**
     * @Then /^the JSON node "(?P<jsonNode>[^"]*)" should be the integer (?P<expectedValue>\d+)$/
     */
    public function theJsonNodeShouldBeTheInteger(string $jsonNode, int $expectedValue)
    {
        $realValue = $this->jsonInspector->evaluate($jsonNode);
        Assertion::same($realValue, $expectedValue);
    }

    /**
     * @Then /^the JSON node "(?P<jsonNode>[^"]*)" should be true$/
     */
    public function theJsonNodeShouldBeTrue(string $jsonNode)
    {
        $realValue = $this->jsonInspector->evaluate($jsonNode);
        Assertion::true($realValue);
    }

    /**
     * @Then /^the JSON node "(?P<jsonNode>[^"]*)" should be false$/
     */
    public function theJsonNodeShouldBeFalse(string $jsonNode)
    {
        $realValue = $this->jsonInspector->evaluate($jsonNode);
        Assertion::false($realValue);
    }

    /**
     * @Then /^the JSON node "(?P<jsonNode>[^"]*)" should be null$/
     */
    public function theJsonNodeShouldBeNull(string $jsonNode)
    {
        $realValue = $this->jsonInspector->evaluate($jsonNode);
        Assertion::null($realValue);
    }

    /**
     * @Then /^the JSON node "(?P<jsonNode>[^"]*)" should be an array/
     */
    public function theJsonNodeShouldBeAnArray(string $jsonNode)
    {
        $realValue = $this->jsonInspector->evaluate($jsonNode);
        Assertion::isArray($realValue);
    }

    /**
     * @Then /^the JSON node "(?P<jsonNode>[^"]*)" should have (?P<expectedNth>\d+) elements?$/
     */
    public function theJsonNodeShouldHaveElements($jsonNode, int $expectedNth)
    {
        $realValue = $this->jsonInspector->evaluate($jsonNode);
        Assertion::isArray($realValue);
        Assertion::count($realValue, $expectedNth);
    }

    /**
     * @Given /^the JSON node "(?P<jsonNode>[^"]*)" should exist$/
     */
    public function theJsonNodeShouldExist($jsonNode)
    {
        try {
            $this->jsonInspector->evaluate($jsonNode);
        } catch (\Exception $e) {
            throw new \Exception(sprintf("The node '%s' does not exist.", $jsonNode), 0, $e);
        }
    }

    /**
     * @Given /^the JSON node "(?P<jsonNode>[^"]*)" should not exist$/
     */
    public function theJsonNodeShouldNotExist($jsonNode)
    {
        try {
            $realValue = $this->jsonInspector->evaluate($jsonNode);
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

        $this->jsonInspector->validate($tempFilename);

        unlink($tempFilename);
    }

    /**
     * @Then /^the JSON should be valid according to the schema "(?P<filename>[^"]*)"$/
     */
    public function theJsonShouldBeValidAccordingToTheSchema($filename)
    {
        $this->jsonInspector->validate($this->resolveFilename($filename));
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
}
