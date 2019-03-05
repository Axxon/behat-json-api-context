<?php

declare(strict_types=1);

namespace Alsciende\Behat;

use Assert\Assertion;
use Behat\Behat\Context\Context;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Alsciende <alsciende@icloud.com>
 *
 * @see    https://github.com/imbo/behat-api-extension
 */
class ApiContext implements Context
{
    use DataStoreContextGatheringTrait;

    /**
     * @var array
     */
    private $requestOptions = [];

    /**
     * @var string
     */
    private $content = '';

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @var array
     */
    private $files = [];

    /**
     * @var array
     */
    private $server = [];

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $method;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @Given the request header :header is :value
     *
     * @param string $header
     * @param string $value
     */
    public function setRequestHeader(string $header, string $value)
    {
        $this->server[strtoupper('HTTP_' . $header)] = $value;
    }

    /**
     * @Given the request body is:
     *
     * @param string $body
     */
    public function setRequestBody(string $body)
    {
        if (!empty($this->requestOptions['multipart']) || !empty($this->requestOptions['form_params'])) {
            throw new \InvalidArgumentException(
                'Cannot set a request body when using multipart/form-data or form parameters.'
            );
        }
        $this->content = $body;
    }

    /**
     * @When I request :method :path
     *
     * @param string $path The path to request
     */
    public function setRequestPath(string $method, string $path)
    {
        $this->method = $method;
        $this->path = $path;

        $this->sendRequest();
    }

    /**
     * @Then the response code is :code
     *
     * @param int $code
     */
    public function assertResponseCodeIs(int $code)
    {
        $this->requireResponse();
        Assertion::same(
            $actual = $this->dataStoreContext['response']['status'],
            $expected = $this->validateResponseCode($code),
            sprintf('Expected response code %d, got %d.', $expected, $actual)
        );
    }

    /**
     * @Then the response header :header is :expected
     *
     * @param string $header
     * @param string $expected
     */
    public function assertResponseHeaderIs(string $header, string $expected)
    {
        $this->requireResponse();
        Assertion::same(
            $actual = $this->dataStoreContext['response']['headers'][$header],
            $expected,
            sprintf('Expected response header %s, got %s.', $expected, $actual)
        );
    }

    /**
     * Send the request and store the response
     */
    private function sendRequest()
    {
        $this->client->request(
            $this->method,
            $this->path,
            $this->parameters,
            $this->files,
            $this->server,
            $this->content
        );

        /** @var Response $response */
        $response = $this->client->getResponse();
        $this->dataStoreContext['response'] = [
            'status' => $response->getStatusCode(),
            'headers' => $response->headers->all(),
            'content' => $response->getContent(),
        ];
    }

    /**
     * Assert we have a response
     */
    private function requireResponse()
    {
        Assertion::keyIsset($this->dataStoreContext, 'response', 'The request has not been made yet, so no response object exists.');
    }

    /**
     * Assert the HTTP code is valid
     *
     * @param int $code
     *
     * @return int
     */
    private function validateResponseCode(int $code)
    {
        Assertion::range($code, 100, 599, sprintf('Response code must be between 100 and 599, got %d.', $code));

        return $code;
    }
}
