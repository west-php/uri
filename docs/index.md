# Getting Started

A URI is built from non-encoded components:
```php
namespace West\Uri;

$domain = new Host\Domain('example.com');
$uri = new Http(
    'https',
    $domain,
    'user',
    443,
    '/path/to/end?point',
    'query=string',
    'fragment'
);

// https://user@example.com:443/path/to/end%3Fpoint?query=string#fragment
echo (string) $uri;
```

If an encoded component is used in the constructor it will be double-encoded.  Alternatively URIs are constructed from
an encoded URI string:

```php
namespace West\Uri;

$uriString = 'https://user@example.com:443/path/to/end%3Fpoint?query=string#fragment';
$factory = new Factory\HttpFactory();

$uri = $factory->createFromString($uriString);
```

Absolute paths will have dot segments '.' and '..' removed according to the algorithm described in
[RFC 3986, Section 5.2.4](https://tools.ietf.org/html/rfc3986#section-5.2.4) when using either a URI factory or the
constructor.


# Hosts

Host are domains, IPv4 or null objects:

```php
namespace West\Uri;

$domain = new Host\Domain('example.com');
$ipv4 = new Host\IPv4('192.0.2.1');
$nullHost = new Host\NullHost();
```

The only method implemented by hosts is `__toString`.  Domains will be normalized to lower case.


# Relative URIs

A relative URI is a URI with no scheme, the meaning being determined by context.  They can be used with the
`UriInterface::resolveRelative` method:

```php
namespace West\Uri;

$domain = new Host\Domain('example.com');
$uri = new Http(
    'https',
    $domain,
    'user',
    0,
    '/path/to/endpoint',
    'query=string',
    ''
);

$nullHost = new Host\NullHost();
$relativeUri = new RelativeUri(
    $nullHost,
    '',
    0,
    '../../another/endpoint',
    '',
    ''
);

// https://user@example.com/another/endpoint
echo (string) $uri->resolveRelative($relativeUri);
```

The base URI must not include a fragment. See [RFC 3986, Section 5.2](https://tools.ietf.org/html/rfc3986#section-5.2)
for details of the resolution algorithm.


# Getters and setters

Methods `getScheme`, `getUser` etc. are available on URI objects because the author favors this over other methods of
decomposing URI objects for the `resolveRelative` algorithm.  These getters return non-encoded data. A `withScheme`
method is provided for http(s) URIs but not in general for URI objects.
