<?php

declare(strict_types=1);

namespace Alsciende\Behat\Json;

/**
 * This class is responsible for controlling the other objects
 */
class JsonInspector
{
    /**
     * @var mixed
     */
    private $data;

    /**
     * @var JsonParser
     */
    private $jsonParser;

    /**
     * @var JsonSchema
     */
    private $jsonSchema;

    public function __construct($data)
    {
        $this->data = $data;
        $this->jsonParser = new JsonParser();
        $this->jsonSchema = new JsonSchema();
    }

    public static function fromString(string $jsonContent)
    {
        return new static((new Json($jsonContent))->getDecoded());
    }

    public function evaluate(string $jsonNodeExpression)
    {
        return $this->jsonParser->evaluate($this->data, $jsonNodeExpression);
    }

    public function validate(string $filename)
    {
        $this->jsonSchema->validate($this->data, $filename);
    }
}
