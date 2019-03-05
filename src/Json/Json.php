<?php

declare(strict_types=1);

namespace Alsciende\Behat\Json;

/**
 * The class is responsible for representing a JSON value
 */
class Json
{
    /**
     * @var mixed
     */
    private $decoded;

    /**
     * Json constructor.
     *
     * @param string $content
     *
     * @throws \Exception
     */
    public function __construct(string $content)
    {
        $this->decoded = $this->decode($content);
    }

    /**
     * @param string $content
     *
     * @return mixed
     *
     * @throws \Exception
     */
    private function decode(string $content)
    {
        $result = json_decode($content, false);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \Exception(
                sprintf('The string "%s" is not valid json', $content)
            );
        }

        return $result;
    }

    public function getDecoded()
    {
        return $this->decoded;
    }

    public function __toString()
    {
        return $this->encode(false);
    }

    private function encode($pretty = true)
    {
        if (true === $pretty && defined('JSON_PRETTY_PRINT')) {
            return json_encode($this->decoded, JSON_PRETTY_PRINT);
        }

        return json_encode($this->decoded);
    }
}
