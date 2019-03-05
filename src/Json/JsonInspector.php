<?php

declare(strict_types=1);

namespace Alsciende\Behat\Json;

/**
 * This class is responsible for controlling the other objects
 */
class JsonInspector
{
    /**
     * @var Json
     */
    private $json;

    /**
     * @var JsonParser
     */
    private $jsonParser;

    /**
     * @var JsonSchema
     */
    private $jsonSchema;

    public function __construct(string $jsonContent)
    {
        $this->json = new Json($jsonContent);
        $this->jsonParser = new JsonParser();
        $this->jsonSchema = new JsonSchema();
    }

    public function evaluate(string $jsonNodeExpression)
    {
        return $this->jsonParser->evaluate($this->json, $jsonNodeExpression);
    }

    public function validate(string $filename)
    {
        $this->jsonSchema->validate($this->json, $filename);
    }
}
