<?php

namespace App\Tests;

use App\Api\Population;
use GuzzleHttp\Client;
use PSX\Framework\Test\ControllerDbTestCase;
use PSX\Http\Factory\NativeFactory;

class ApiTestCase extends ControllerDbTestCase
{
    /**
     * @var \GuzzleHttp\Client
     */
    private static $httpClient;

    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__ . '/api_fixture.xml');
    }

    protected function getPaths()
    {
        return array(
            [['ANY'], '/population', Population\Collection::class],
            [['ANY'], '/population/:id', Population\Entity::class],
        );
    }

    /**
     * We overwrite this method to actually send an HTTP request instead of 
     * using an internal request. This is slower but tests the aerys to psx http
     * communication
     * 
     * @param string $uri
     * @param string $method
     * @param array $headers
     * @param null $body
     * @return \PSX\Http\Response
     */
    protected function sendRequest($uri, $method, $headers = array(), $body = null)
    {
        $response = self::getHttpClient()->request($method, $uri, [
            'headers' => $headers,
            'body'    => $body,
        ]);

        return NativeFactory::createResponse($response);
    }

    /**
     * @return \GuzzleHttp\Client
     */
    private static function getHttpClient()
    {
        if (self::$httpClient) {
            return self::$httpClient;
        }

        return self::$httpClient = new Client([
            'base_uri'    => 'http://127.0.0.1:8080/',
            'http_errors' => false,
        ]);
    }
}
