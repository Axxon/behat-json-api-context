<?php

declare(strict_types=1);

namespace Alsciende\Behat\Json;

use JsonSchema\SchemaStorage;
use JsonSchema\Validator;

/**
 * This class is responsible for knowing how to validate a Json object against a Json schema
 */
class JsonSchema
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var SchemaStorage
     */
    private $schemaStorage;

    public function __construct()
    {
        $this->validator = new Validator();
        $this->schemaStorage = new SchemaStorage();
    }

    /**
     * @param Json   $json     The content to validate
     * @param string $filename The path of the file holding the schema
     *
     * @return bool
     */
    public function validate(Json $json, string $filename)
    {
        $schema = $this->schemaStorage->resolveRef('file://' . realpath($filename));
        $this->validator->check($json->getDecoded(), $schema);

        if (!$this->validator->isValid()) {
            $msg = 'JSON does not validate. Violations:' . PHP_EOL;
            foreach ($this->validator->getErrors() as $error) {
                $msg .= sprintf('  - [%s] %s' . PHP_EOL, $error['property'], $error['message']);
            }
            throw new \Exception($msg);
        }

        return true;
    }
}
