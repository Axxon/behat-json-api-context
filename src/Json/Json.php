<?php

declare(strict_types=1);

namespace Alsciende\Behat\Json;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class Json
{
    /**
     * @var mixed
     */
    private $content;

    /**
     * Json constructor.
     *
     * @param mixed $content
     * @param bool  $encodedAsString
     *
     * @throws \Exception
     */
    public function __construct($content, $encodedAsString = true)
    {
        $this->content = true === $encodedAsString ? $this->decode((string) $content) : $content;
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
        $result = json_decode($content);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \Exception(
                sprintf('The string "%s" is not valid json', $content)
            );
        }

        return $result;
    }

    public static function fromRawContent($content)
    {
        return new static($content, false);
    }

    /**
     * @param string                    $expression
     * @param PropertyAccessorInterface $accessor
     *
     * @return array|mixed
     */
    public function read(string $expression, PropertyAccessorInterface $accessor)
    {
        if (is_array($this->content)) {
            $expression = preg_replace('/^root/', '', $expression);
        } else {
            $expression = preg_replace('/^root./', '', $expression);
        }

        if (null === $expression) {
            throw new \Exception(sprintf('Regex matching failed : error code [%d]', preg_last_error()));
        }

        // If root asked, we return the entire content
        if (strlen(trim($expression)) <= 0) {
            return $this->content;
        }

        return $accessor->getValue($this->content, $expression);
    }

    public function getRawContent()
    {
        return $this->content;
    }

    public function __toString()
    {
        return $this->encode(false);
    }

    public function encode($pretty = true)
    {
        if (true === $pretty && defined('JSON_PRETTY_PRINT')) {
            return json_encode($this->content, JSON_PRETTY_PRINT);
        }

        return json_encode($this->content);
    }
}
