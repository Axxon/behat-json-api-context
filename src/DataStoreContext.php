<?php

declare(strict_types=1);

namespace Alsciende\Behat;

use Behat\Behat\Context\Context;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * This class is responsible for holding key-value pairs in an ArrayAccess interface
 * and returning these values, directly or by replacing placeholders in a string
 */
class DataStoreContext implements Context, \ArrayAccess
{
    /**
     * @var array
     */
    private $store = [];

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
                                                ->enableExceptionOnInvalidIndex()
                                                ->getPropertyAccessor();
    }

    public function offsetExists($offset)
    {
        return isset($this->store[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->store[$offset]) ? $this->store[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->store[] = $value;
        } else {
            $this->store[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->store[$offset]);
    }

    /**
     * Find all placeholders delimited by -< >- and replace them
     *
     * @example When the store is ['fu' => 'bar'], then the template "Hello -<[fu]>-" is evaluated as "Hello bar"
     *
     * @param string $template
     *
     * @return string
     */
    public function evaluate(string $template): string
    {
        $result = preg_replace_callback('/\-\<(.*?)\>\-/U', function ($matches) {
            return $this->propertyAccessor->getValue($this->store, trim($matches[1]));
        }, $template);

        if (!is_string($result)) {
            throw new \LogicException('Error with preg_replace_callback');
        }

        return $result;
    }
}
