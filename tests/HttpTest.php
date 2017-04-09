<?php

namespace West\Uri;

use PHPUnit\Framework\TestCase;
use West\Uri\Exception\DomainException;
use West\Uri\Exception\InvalidArgumentException;
use West\Uri\Host\Domain;
use West\Uri\Host\HostInterface;
use West\Uri\Host\NullHost;

class HttpTest extends TestCase
{

    public function testWithScheme()
    {
        $host = new Domain('www.example.com');
        $uri = (new Http('http', $host, '', 0, '', '', ''))
            ->withScheme('https');

        $expectedValue = 'https://www.example.com';

        $this->assertEquals((string) $uri, $expectedValue);

    }

    public function testInvalidScheme()
    {
        $this->expectException(InvalidArgumentException::class);

        $host = new Domain('www.example.com');
        new Http('invalid', $host, '', 0, '', '', '');
    }

    /**
     * @param string $scheme
     * @param string $mappedScheme
     *
     * @dataProvider providerTestValidScheme
     */
    public function testValidScheme($scheme, $mappedScheme)
    {
        $host = new Domain('www.example.com');
        $uri = new Http($scheme, $host, '', 0, '', '', '');

        $this->assertEquals($uri->getScheme(), $mappedScheme);
    }

    public function providerTestValidScheme()
    {
        return [
            ['http', 'http'],
            ['https', 'https'],
            ['HTTP', 'http'],
            ['HTTPS', 'https']
        ];
    }

    public function testResolveRelativeException()
    {
        $this->expectException(DomainException::class);

        $host = new Domain('www.example.com');
        $uri = new Http('https', $host, '', 0, '', '', 'fragment');
        $relativeUri = new Http('https', $host, '', 0, '', '', 'new-fragment');

        $uri->resolveRelative($relativeUri);
    }

    /**
     * @param UriInterface $baseUri
     * @param UriInterface $relativeUri
     * @param string $expectedResult
     *
     * @dataProvider providerTestResolveRelative
     */
    public function testResolveRelative($baseUri, $relativeUri, $expectedResult)
    {
        $resolvedUri = $baseUri->resolveRelative($relativeUri);

        $this->assertEquals((string) $resolvedUri, $expectedResult);
    }

    public function providerTestResolveRelative()
    {
        $host = new Domain('www.example.com');
        $relativeHost = new Domain('www.test.com');
        $nullHost = new NullHost();

        $baseUri = new Http('http', $host, 'username', 80, '/a/b/c', 'query', '');

        $relativeUriScheme = new Http('https', $host, '', 0, '', '', '');
        $relativeUriAuthority = new RelativeUri($relativeHost, 'user', 40, '', '', '');
        $relativeNoAuthorityNoPath = new RelativeUri($nullHost, '', 0, '', '', '');
        $relativeNoAuthorityNoPathQuery = new RelativeUri($nullHost, '', 0, '', 'new-query', '');
        $relativeNoAuthorityPath = new RelativeUri($nullHost, '', 0, '/a/b/c', '', '');
        $relativeNoAuthorityPathMerge = new RelativeUri($nullHost, '', 0, 'd/e/f', '', '');

        return [
            [$baseUri, $relativeUriScheme, 'https://www.example.com'],
            [$baseUri, $relativeUriAuthority, 'http://user@www.test.com:40'],
            [$baseUri, $relativeNoAuthorityNoPath, 'http://username@www.example.com:80?query'],
            [$baseUri, $relativeNoAuthorityNoPathQuery, 'http://username@www.example.com:80?new-query'],
            [$baseUri, $relativeNoAuthorityPath, 'http://username@www.example.com:80/a/b/c'],
            [$baseUri, $relativeNoAuthorityPathMerge, 'http://username@www.example.com:80/a/b/d/e/f'],
        ];
    }

    /**
     * @param string $basePath
     * @param string $relativePath
     * @param string $expectedResult
     *
     * @dataProvider providerTestPathMerge
     */
    public function testPathMerge($basePath, $relativePath, $expectedResult)
    {
        $host = new Domain('www.example.com');
        $baseUri = new Http('http', $host, '', 0, $basePath, '', '');
        $relativeUri = new RelativeUri(new NullHost(), '', 0, $relativePath, '', '');

        $resolvedUri = $baseUri->resolveRelative($relativeUri);

        $this->assertEquals($expectedResult, $resolvedUri->getPath());
    }

    public function providerTestPathMerge()
    {
        return [
            ['', 'd/e/f', '/d/e/f'],
            ['/b/c', 'd/e/f', '/b/d/e/f'],
            ['/b/c/', 'd/e/f', '/b/c/d/e/f'],

            // RFC 3986 section 5.4.1
            ['/b/c/d', '.', '/b/c/'],
            ['/b/c/d', './', '/b/c/'],
            ['/b/c/d', '..', '/b/'],
            ['/b/c/d', '../', '/b/'],
            ['/b/c/d', '../g', '/b/g'],
            ['/b/c/d', '../..', '/'],
            ['/b/c/d', '../../', '/'],
            ['/b/c/d', '../../g', '/g'],

            // RFC 3986 section 5.4.2
            ['/b/c/d', '../../../g', '/g'],
            ['/b/c/d', '../../../../g', '/g'],
            ['/b/c/d', '/./g', '/g'],
            ['/b/c/d', '/../g', '/g'],
            ['/b/c/d', 'g.', '/b/c/g.'],
            ['/b/c/d', '.g', '/b/c/.g'],
            ['/b/c/d', 'g..', '/b/c/g..'],
            ['/b/c/d', '..g', '/b/c/..g'],
            ['/b/c/d', './../g', '/b/g'],
            ['/b/c/d', './g/.', '/b/c/g/'],
            ['/b/c/d', 'g/./h', '/b/c/g/h'],
            ['/b/c/d', 'g/../h', '/b/c/h'],
            ['/b/c/d', 'g;x=1/./y', '/b/c/g;x=1/y'],
            ['/b/c/d', 'g;x=1/../y', '/b/c/y'],
        ];
    }
}
