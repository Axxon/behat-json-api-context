<?php

declare(strict_types=1);

namespace Alsciende\Behat;

use Alsciende\Behat\Json\Json;
use Alsciende\Behat\Json\JsonInspector;
use Assert\Assertion;
use Behat\Behat\Context\Context;

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

    public function __construct($jsonSchemaBaseUrl = '')
    {
        $this->jsonSchemaBaseUrl = rtrim($jsonSchemaBaseUrl, '/');
    }

    /**
     * @Then the response content is valid JSON
     */
    public function theResponseContentIsValidJson()
    {
        $this->dataStoreContext['json'] = (new Json($this->dataStoreContext['response']['content']))->getDecoded();
    }

    /**
     * @Then /^the JSON node "(?P<jsonNode>[^"]*)" should be the string "(?P<expectedValue>.*)"$/
     */
    public function theJsonNodeShouldBeTheString(string $jsonNode, string $expectedValue)
    {
        $realValue = $this->getJsonInspector()->evaluate($jsonNode);
        Assertion::same($realValue, $expectedValue);
    }

    /**
     * @Then /^the JSON node "(?P<jsonNode>[^"]*)" should be the integer (?P<expectedValue>\d+)$/
     */
    public function theJsonNodeShouldBeTheInteger(string $jsonNode, int $expectedValue)
    {
        $realValue = $this->getJsonInspector()->evaluate($jsonNode);
        Assertion::same($realValue, $expectedValue);
    }

    /**
     * @Then /^the JSON node "(?P<jsonNode>[^"]*)" should be true$/
     */
    public function theJsonNodeShouldBeTrue(string $jsonNode)
    {
        $realValue = $this->getJsonInspector()->evaluate($jsonNode);
        Assertion::true($realValue);
    }

    /**
     * @Then /^the JSON node "(?P<jsonNode>[^"]*)" should be false$/
     */
    public function theJsonNodeShouldBeFalse(string $jsonNode)
    {
        $realValue = $this->getJsonInspector()->evaluate($jsonNode);
        Assertion::false($realValue);
    }

    /**
     * @Then /^the JSON node "(?P<jsonNode>[^"]*)" should be null$/
     */
    public function theJsonNodeShouldBeNull(string $jsonNode)
    {
        $realValue = $this->getJsonInspector()->evaluate($jsonNode);
        Assertion::null($realValue);
    }

    /**
     * @Then /^the JSON node "(?P<jsonNode>[^"]*)" should be an array$/
     */
    public function theJsonNodeShouldBeAnArray(string $jsonNode)
    {
        $realValue = $this->getJsonInspector()->evaluate($jsonNode);
        Assertion::isArray($realValue);
    }

    /**
     * @Then /^the JSON node "(?P<jsonNode>[^"]*)" should be an object$/
     */
    public function theJsonNodeShouldBeAnObject(string $jsonNode)
    {
        $realValue = $this->getJsonInspector()->evaluate($jsonNode);
        Assertion::isObject($realValue);
    }

    /**
     * @Then /^the JSON node "(?P<jsonNode>[^"]*)" should have (?P<expectedNth>\d+) elements?$/
     */
    public function theJsonNodeShouldHaveElements($jsonNode, int $expectedNth)
    {
        $realValue = $this->getJsonInspector()->evaluate($jsonNode);
        Assertion::isArray($realValue);
        Assertion::count($realValue, $expectedNth);
    }

    /**
     * @Given /^the JSON node "(?P<jsonNode>[^"]*)" should exist$/
     */
    public function theJsonNodeShouldExist($jsonNode)
    {
        $jsonInspector = $this->getJsonInspector();
        try {
            $jsonInspector->evaluate($jsonNode);
        } catch (\Exception $e) {
            throw new \Exception(sprintf("The node '%s' does not exist.", $jsonNode), 0, $e);
        }
    }

    /**
     * @Given /^the JSON node "(?P<jsonNode>[^"]*)" should not exist$/
     */
    public function theJsonNodeShouldNotExist($jsonNode)
    {
        $jsonInspector = $this->getJsonInspector();
        try {
            $realValue = $jsonInspector->evaluate($jsonNode);
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
    public function theJsonShouldBeValidAccordingToThisSchema(string $jsonSchemaContent)
    {
        $tempFilename = tempnam(sys_get_temp_dir(), 'rae');
        if (false === $tempFilename) {
            throw new \Exception('Cannot write in ' . sys_get_temp_dir());
        }

        file_put_contents($tempFilename, $jsonSchemaContent);

        $this->getJsonInspector()->validate($tempFilename);

        unlink($tempFilename);
    }

    /**
     * @Then /^the JSON should be valid according to the schema "(?P<filename>[^"]*)"$/
     */
    public function theJsonShouldBeValidAccordingToTheSchema($filename)
    {
        $this->getJsonInspector()->validate($this->resolveFilename($filename));
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

    private function getJsonInspector()
    {
        Assertion::keyIsset($this->dataStoreContext, 'json', 'First, you must check that the response content is valid JSON.');

        return new JsonInspector($this->dataStoreContext['json']);
    }
}
