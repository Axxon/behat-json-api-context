<?php

declare(strict_types=1);

namespace Alsciende\Behat\Json;

use JsonSchema\SchemaStorage;
use JsonSchema\Validator;

/**
 * This class is responsible for knowing how to validate arbitrary data against a Json schema
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
     * @param mixed  $data
     * @param string $filename The path of the file holding the schema
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function validate($data, string $filename)
    {
        $schema = $this->schemaStorage->resolveRef('file://' . realpath($filename));
        $this->validator->check($data, $schema);

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
