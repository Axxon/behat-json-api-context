<?php

declare(strict_types=1);

namespace Alsciende\Behat\Json;

class JsonInspector
{
    /**
     * @var JsonParser
     */
    private $jsonParser;

    /**
     * @var JsonStorage
     */
    private $jsonStorage;

    public function __construct()
    {
        $this->jsonParser = new JsonParser();
        $this->jsonStorage = new JsonStorage();
    }

    /**
     * @param string $jsonNodeExpression
     *
     * @return array|mixed
     *
     * @throws \Exception
     */
    public function readJsonNodeValue(string $jsonNodeExpression)
    {
        return $this->jsonParser->evaluate(
            $this->readJson(),
            $jsonNodeExpression
        );
    }

    public function readJson()
    {
        return $this->jsonStorage->readJson();
    }

    /**
     * @param JsonSchema $jsonSchema
     *
     * @throws \Exception
     */
    public function validateJson(JsonSchema $jsonSchema)
    {
        $this->jsonParser->validate(
            $this->readJson(),
            $jsonSchema
        );
    }

    public function writeJson($jsonContent)
    {
        $this->jsonStorage->writeRawContent($jsonContent);
    }
}
