<?php

namespace West\Uri\Factory;

use PHPUnit\Framework\TestCase;

class RelativeUriFactoryTest extends TestCase
{
    /** @var $relativeUriFactory RelativeUriFactory Relative URI Factory */
    private $relativeUriFactory;

    public function setUp()
    {
        $this->relativeUriFactory = new RelativeUriFactory();
    }

    public function testTrue()
    {
        $this->assertTrue(true);
    }

    /**
     * @dataProvider providerTestValidUri
     */
    public function testValidUri($uriString)
    {
        $uri = $this->relativeUriFactory->createFromString($uriString);

        $this->assertEquals($uriString, (string) $uri);
    }

    public function providerTestValidUri()
    {
        return [
            ['//user@www.example.com:80/path?query#fragment'],
            ['//user@255.255.255.0:80/path?query#fragment'],
        ];
    }
}
