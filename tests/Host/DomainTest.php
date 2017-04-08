<?php

namespace West\Uri\Host;

use PHPUnit\Framework\TestCase;
use West\Uri\Exception\InvalidArgumentException;

class DomainTest extends TestCase
{
    /**
     * @param string $invalidDomain
     *
     * @dataProvider providerTestDomainException
     */
    public function testDomainException($invalidDomain)
    {
        $this->expectException(InvalidArgumentException::class);

        new Domain($invalidDomain);
    }

    public function providerTestDomainException()
    {
        return [
            [''],
            ['!www.example.com']
        ];
    }

    /**
     * @param string $validDomain
     *
     * @dataProvider providerTestDomainString
     */
    public function testDomainString($validDomain)
    {
        $domain = new Domain($validDomain);

        $this->assertEquals($validDomain, (string) $domain);
    }

    public function providerTestDomainString()
    {
        return [
            ['www.example.com']
        ];
    }
}
