<?php

namespace West\Uri;

use PHPUnit\Framework\TestCase;
use West\Uri\Exception\InvalidArgumentException;
use West\Uri\Host\Domain;
use West\Uri\Host\HostInterface;
use West\Uri\Host\NullHost;

class RelativeUriTest extends TestCase
{
    /**
     * Test valid relative URI strings
     *
     * @param HostInterface $host
     * @param string $user
     * @param int|null $port
     * @param string $path
     * @param string $query
     * @param string $fragment
     * @param string $expected
     * @param string $expectedAuthority
     * @param string $expectedPath
     *
     * @dataProvider providerTestHttpEquals
     */
    public function testRelativeUriEquals(
        $host,
        $user,
        $port,
        $path,
        $query,
        $fragment,
        $expected,
        $expectedAuthority,
        $expectedPath
    ) {
        $relativeUri = new RelativeUri($host, $user, $port, $path, $query, $fragment);

        $this->assertEquals((string) $relativeUri, $expected);
        $this->assertEquals($relativeUri->getScheme(), '');
        $this->assertEquals($relativeUri->getHost(), $host);
        $this->assertEquals($relativeUri->getUser(), $user);
        $this->assertEquals($relativeUri->getPort(), $port);
        $this->assertEquals($relativeUri->getAuthority(), $expectedAuthority);
        $this->assertEquals($relativeUri->getPath(), $expectedPath);
        $this->assertEquals($relativeUri->getQuery(), $query);
        $this->assertEquals($relativeUri->getFragment(), $fragment);
    }

    public function providerTestHttpEquals()
    {
        $host = new Domain('www.example.com');

        return [
            // relative URI with all components
            [
                $host,
                'username',
                80,
                '/a/b/c',
                'key=value&another=value',
                'valid-fragment',
                '//username@www.example.com:80/a/b/c?key=value&another=value#valid-fragment',
                'username@www.example.com:80',
                '/a/b/c'
            ],
            // test path reduction
            [
                $host,
                'username',
                80,
                '/a/b/./../b/c',
                'key=value&another=value',
                'valid-fragment',
                '//username@www.example.com:80/a/b/c?key=value&another=value#valid-fragment',
                'username@www.example.com:80',
                '/a/b/c'
            ],
            [
                $host,
                'username',
                80,
                '/a/b/.//../../../b/c',
                'key=value&another=value',
                'valid-fragment',
                '//username@www.example.com:80/b/c?key=value&another=value#valid-fragment',
                'username@www.example.com:80',
                '/b/c'
            ],
            // test user encoding
            [
                $host,
                'user@name',
                0,
                '/a/b/c',
                'key=value&another=value',
                'valid-fragment',
                '//user%40name@www.example.com/a/b/c?key=value&another=value#valid-fragment',
                'user%40name@www.example.com',
                '/a/b/c'
            ],
            // test path encoding
            [
                $host,
                'username',
                0,
                '/a/b/c?',
                'key=value&another=value',
                'valid-fragment',
                '//username@www.example.com/a/b/c%3F?key=value&another=value#valid-fragment',
                'username@www.example.com',
                '/a/b/c?'
            ],
            // test query encoding
            [
                $host,
                'username',
                0,
                '/a/b/c',
                'key=value&another=value#',
                'valid-fragment',
                '//username@www.example.com/a/b/c?key=value&another=value%23#valid-fragment',
                'username@www.example.com',
                '/a/b/c'
            ],
            // test fragment encoding
            [
                $host,
                'username',
                0,
                '/a/b/c',
                'key=value&another=value',
                'valid-fragment#',
                '//username@www.example.com/a/b/c?key=value&another=value#valid-fragment%23',
                'username@www.example.com',
                '/a/b/c'
            ]
        ];
    }

    /**
     * Test invalid path
     */
    public function testInvalidPath()
    {
        $this->expectException(InvalidArgumentException::class);

        new RelativeUri(new NullHost(), '', 0, '//a/b', '', '');
    }

    /**
     * Test invalid URI port values
     *
     * @param int|null $port
     *
     * @dataProvider providerTestInvalidPort
     */
    public function testInvalidPort($port)
    {
        $this->expectException(InvalidArgumentException::class);

        new RelativeUri(new NullHost(), '', $port, '/a/b', '', '');
    }

    public function providerTestInvalidPort()
    {
        return [
            [-1], [1 << 16]
        ];
    }

    /**
     * Test colon in user string
     */
    public function testInvalidUser()
    {
        $this->expectException(InvalidArgumentException::class);

        new RelativeUri(new NullHost(), 'user:colon', 0, '/a/b', '', '');
    }

    /**
     * Test absolute and non-absolute URI result
     *
     * @param string $fragment
     * @param bool $isAbsolute
     *
     * @dataProvider providerTestAbsolute
     */
    public function testAbsolute($fragment, $isAbsolute)
    {
        $relativeUri = new RelativeUri(new NullHost(), 'usercolon', 0, '/a/b', '', $fragment);

        $this->assertEquals($relativeUri->isAbsoluteUri(), $isAbsolute);
    }

    public function providerTestAbsolute()
    {
        return [
            ['', true],
            ['any-fragment', false]
        ];
    }

    /**
     * Test HasAuthority and non-HasAuthority URI result
     *
     * @param HostInterface|null $host
     * @param string $user
     * @param int|null $port
     * @param bool $hasAuthority
     *
     * @dataProvider providerTestHasAuthority
     */
    public function testHasAuthority($host, $user, $port, $hasAuthority)
    {
        $relativeUri = new RelativeUri($host, $user, $port, '/a/b', '', '');

        $this->assertEquals($hasAuthority, $relativeUri->hasAuthority());
    }

    public function providerTestHasAuthority()
    {
        $host = new Domain('www.example.com');
        $nullHost = new NullHost();

        return [
            [$nullHost, '', 0, false],
            [$host, '', 0, true],
            [$nullHost, 'user', 0, true],
            [$nullHost, '', 40, true]
        ];
    }
}
