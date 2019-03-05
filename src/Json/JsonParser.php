<?php

declare(strict_types=1);

namespace Alsciende\Behat\Json;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * The class is responsible for knowing how to read a Json object with a XPath-like syntax
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
            ->getPropertyAccessor()
        ;
    }

    /**
     * @param Json   $json
     * @param string $expression
     *
     * @return array|mixed
     *
     * @throws \Exception
     */
    public function evaluate(Json $json, string $expression)
    {
        return $this->propertyAccessor->getValue($json->getDecoded(), $expression);
    }
}
