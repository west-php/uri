<?php

namespace West\Uri\Factory;

use PHPUnit\Framework\TestCase;
use West\Uri\Exception\InvalidArgumentException;

class HttpFactoryTest extends TestCase
{
    /** @var $relativeUriFactory HttpFactory HTTP URI Factory */
    private $httpUriFactory;

    public function setUp()
    {
        $this->httpUriFactory = new HttpFactory();
    }

    public function testTrue()
    {
        $this->assertTrue(true);
    }

    /**
     * Test valid HTTP URI
     *
     * @dataProvider providerTestValidUri
     */
    public function testValidUri($uriString)
    {
        $uri = $this->httpUriFactory->createFromString($uriString);

        $this->assertEquals($uriString, (string) $uri);
    }

    public function providerTestValidUri()
    {
        return [
            ['http://user@www.example.com:80/path?query#fragment']
        ];
    }

    /**
     * Test invalid HTTP URI
     *
     * @dataProvider providerTestInvalidUri
     */
    public function testInvalidUri($uriString)
    {
        $this->expectException(InvalidArgumentException::class);

        $this->httpUriFactory->createFromString($uriString);
    }

    public function providerTestInvalidUri()
    {
        return [
            ['invalid://user@www.example.com:80/path?query#fragment']
        ];
    }
}
