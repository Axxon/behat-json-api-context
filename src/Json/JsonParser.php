<?php

declare(strict_types=1);

namespace Alsciende\Behat\Json;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * The class is responsible for knowing how to read arbitrary data with a XPath-like syntax
 */
class JsonParser
{
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

    /**
     * @param mixed  $data
     * @param string $expression
     *
     * @return array|mixed
     */
    public function evaluate($data, string $expression = '')
    {
        return empty($expression) ?
            $data
            : $this->propertyAccessor->getValue($data, $expression);
    }
}
