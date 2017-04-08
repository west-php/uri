<?php

namespace West\Uri\Host;

use PHPUnit\Framework\TestCase;
use West\Uri\Exception\InvalidArgumentException;

class IPv4Test extends TestCase
{
    /**
     * @param string $invalidIPv4
     *
     * @dataProvider providerTestIPv4Exception
     */
    public function testIPv4Exception($invalidIPv4)
    {
        $this->expectException(InvalidArgumentException::class);

        new IPv4($invalidIPv4);
    }

    public function providerTestIPv4Exception()
    {
        return [
            ['256.0.0.0'],
            ['-1.0.0.1']
        ];
    }

    /**
     * @param string $validIPv4
     *
     * @dataProvider providerTestIPv4String
     */
    public function testIPv4String($validIPv4)
    {
        $iPv4 = new IPv4($validIPv4);

        $this->assertEquals($validIPv4, (string) $iPv4);
    }

    public function providerTestIPv4String()
    {
        return [
            ['127.0.0.1'],
            ['255.255.255.0']
        ];
    }
}
