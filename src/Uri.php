<?php
/*
 * This file is part of the West\\Uri package
 *
 * (c) Chris Evans <c.m.evans@gmx.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace West\Uri;

use West\Uri\Exception\DomainException;
use West\Uri\Exception\InvalidArgumentException;
use West\Uri\Exception\LogicException;
use West\Uri\Host\HostInterface;

/**
 * @brief RFC 3986 URI Implementation.
 *
 * @see http://tools.ietf.org/html/rfc3986 URI specification
 *
 * @author Christopher Evans <c.m.evans@gmx.co.uk>
 * @date 26 March 2017
 */
abstract class Uri implements UriInterface
{
    /**
     * @brief Scheme
     *
     * @var $scheme string
     */
    private $scheme;

    /**
     * User string
     *
     * @var $user string
     */
    private $user;

    /**
     * @brief Host
     *
     * @var $host HostInterface
     */
    private $host;

    /**
     * Port
     *
     * @var $port int|null
     */
    private $port;

    /**
     * Path
     *
     * @var $path string
     */
    private $path;

    /**
     * Query string
     *
     * @var $query string
     */
    private $query;

    /**
     * Fragment
     *
     * @var $fragment string Fragment
     */
    private $fragment;

    /**
     * @brief Regex matching characters that require encoding
     * in a user string
     *
     * @var $userRegex string
     */
    private static $userRegex = '/[^a-z0-9\._~!\$&\'\(\)\*\+,;=:-]+/i';

    /**
     * @brief Regex matching characters that require encoding
     * in a path string
     *
     * @var $pathSegmentRegex string
     */
    private static $pathSegmentRegex = '/[^a-z0-9\._~!\$&\'\(\)\*\+,;=:@-]+/i';

    /**
     * @brief Regex matching characters that require encoding
     * in a query string or URI fragment
     *
     * @var $queryFragmentRegex string
     */
    private static $queryFragmentRegex = '/[^a-z0-9\._~!\$&\'\(\)\*\+,;=:@\/\?-]+/i';

    /**
     * Uri constructor.
     *
     * @param string $scheme
     * @param null|HostInterface $host
     * @param string $user
     * @param int|null $port
     * @param string $path
     * @param string $query
     * @param string $fragment
     */
    public function __construct(
        string $scheme,
        ?HostInterface $host,
        string $user,
        ?int $port,
        string $path,
        string $query,
        string $fragment
    ) {
        if (! $this->isValidUser($user)) {
            throw new InvalidArgumentException(sprintf('Invalid user %s', $user));
        }
        if (! $this->isValidPort($port)) {
            throw new InvalidArgumentException(sprintf('Invalid port: %s', $port));
        }
        if (! $this->isValidPath($path, $host || $user || $port !== null)) {
            throw new InvalidArgumentException(sprintf('Invalid path: %s', $path));
        }

        $this->scheme = $this->mapScheme($scheme);
        $this->host = $host;
        $this->user = $user;
        $this->port = $port;
        $this->path = $this->reducePath($path);
        $this->query = $query;
        $this->fragment = $fragment;
    }


    /**
     * @see UriInterface::getScheme
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @see UriInterface::getAuthority
     */
    public function getAuthority(): string
    {
        $user = $this->encodeUser();
        $port = $this->port;

        if ($user !== '') {
            $user = $user . '@';
        }

        if ($port !== null) {
            $port = ':' . $port;
        }

        return $user . $this->host . $port;
    }

    /**
     * @see UriInterface::getUser
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @see UriInterface::getHost
     */
    public function getHost(): HostInterface
    {
        return $this->host;
    }

    /**
     * @see UriInterface::getPort
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * @see UriInterface::getPath
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @see UriInterface::getQuery
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @see UriInterface::getFragment
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * @see UriInterface::__toString
     */
    public function __toString(): string
    {
        $scheme = $this->scheme;
        if ($scheme !== '') {
            $scheme = $scheme . ':';
        }

        $authority = $this->getAuthority();
        if ($authority !== '') {
            $authority = '//' . $authority;
        }

        $path = $this->encodePath();

        $query = $this->encodeQuery();
        if ($query !== '') {
            $query = '?' . $query;
        }

        $fragment = $this->encodeFragment();
        if ($fragment !== '') {
            $fragment = '#' . $fragment;
        }

        return $scheme . $authority . $path . $query . $fragment;
    }

    /**
     * @brief Validate port
     *
     * @param int|null $port
     *
     * @return bool
     */
    private function isValidPort(?int $port)
    {
        return $port === null || $port >= 0 && ($port < 1 << 16);
    }

    /**
     * @brief Encode URI fragment
     *
     * @return string
     */
    private function encodeFragment(): string
    {
        return preg_replace_callback(
            self::$queryFragmentRegex,
            [$this, 'encodeMatchArray'],
            $this->fragment
        );
    }

    /**
     * @brief Encode URI query string
     *
     * @return string
     */
    private function encodeQuery(): string
    {
        return preg_replace_callback(
            self::$queryFragmentRegex,
            [$this, 'encodeMatchArray'],
            $this->query
        );
    }

    /**
     * @brief Reduce an abdolute path as per RFC 3986
     * Section 5.2.4
     *
     * @param string $path
     *
     * @return string
     */
    private function reducePath(string $path): string
    {
        if (mb_substr($path, 0, 1) !== '/') {
            // could do some reduction here but
            // the algorithm below affects semantics
            // of relative URIs
            return $path;
        }

        $segments = [];
        $count = 0;
        while ($path !== '') {
            $path = preg_replace('/^(\.\/|\.\.\/)/', '', $path, 1, $count);
            if ($count > 0) {
                continue;
            }

            $path = preg_replace('/^(\/\.$|\/\.\/)/', '', $path, 1, $count);
            if ($count > 0) {
                $path = '/' . $path;

                continue;
            }

            $path = preg_replace('/^(\/\.\.$|\/\.\.\/)/', '', $path, 1, $count);
            if ($count > 0) {
                $path = '/' . $path;

                if (count($segments) > 0) {
                    array_pop($segments);
                }

                continue;
            }

            if ($path === '.' || $path === '..') {
                $path = '';

                continue;
            }

            $secondSlash = mb_strpos($path, '/', 1);
            if ($secondSlash === false) {
                $segments[] = $path;

                $path = '';
            } else {
                $segments[] = mb_substr($path, 0, $secondSlash);

                $path = mb_substr($path, $secondSlash);
            }
        }

        return implode('', $segments);
    }

    /**
     * @brief Validate URI path based on authority component
     *
     * @param string $path
     * @param bool $hasAuthority
     *
     * @return bool
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     */
    private function isValidPath(string $path, bool $hasAuthority): bool
    {
        if ($hasAuthority) {
            // if we have authority component
            // must be a path-abempty
            return $path === '' || substr_compare($path, '/', 0, 1) === 0;
        }

        // if we have no authority component
        // must not start with '//'
        if (mb_strlen($path) < 2) {
            return true;
        }

        return substr_compare($path, '//', 0, 2) !== 0;
    }

    /**
     * @brief Encode URI path
     *
     * @return string
     */
    private function encodePath(): string
    {
        $pathSegments = explode('/', $this->path);
        $encodedSegments = [];
        foreach ($pathSegments as $pathSegment) {
            $encodedSegments[] = preg_replace_callback(
                self::$pathSegmentRegex,
                [$this, 'encodeMatchArray'],
                $pathSegment
            );
        }

        return implode('/', $encodedSegments);
    }

    /**
     * @brief Validate user string
     *
     * @param string $user
     *
     * @return bool
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2.1
     */
    private function isValidUser(string $user): bool
    {
        return mb_strpos($user, ':') === false;
    }

    /**
     * @brief Encode user string
     *
     * @return string
     */
    private function encodeUser(): string
    {
        return preg_replace_callback(
            self::$userRegex,
            [$this, 'encodeMatchArray'],
            $this->user
        );
    }

    /**
     * @brief Generic function for URL encoding matches found with
     * preg_replace_callback
     *
     * @param array $matches
     *
     * @return string
     */
    public function encodeMatchArray(array $matches)
    {
        return urlencode($matches[0]);
    }

    /**
     * @brief Map scheme to lower case.
     *
     * @param string $scheme
     *
     * @return string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     */
    private function mapScheme(string $scheme)
    {
        return strtolower($scheme);
    }

    /**
     * @see UriInterface::isAbsoluteUri
     */
    public function isAbsoluteUri(): bool
    {
        return $this->fragment === '';
    }

    /**
     * @see UriInterface::resolveRelative
     */
    public function resolveRelative(UriInterface $relativeUri): UriInterface
    {
        if (! $this->isAbsoluteUri()) {
            throw new DomainException('Base URI must be an absolute URI');
        }

        if ($relativeUri->getScheme()) {
            $scheme = $relativeUri->getScheme();
            $user = $relativeUri->getUser();
            $host = $relativeUri->getHost();
            $port = $relativeUri->getPort();
            $path = $relativeUri->getPath();
            $query = $relativeUri->getQuery();
        } else {
            if ($relativeUri->hasAuthority()) {
                $user = $relativeUri->getUser();
                $host = $relativeUri->getHost();
                $port = $relativeUri->getPort();
                $path = $relativeUri->getPath();
                $query = $relativeUri->getQuery();
            } else {
                $relativePath = $relativeUri->getPath();
                if ($relativePath === '') {
                    $path = $relativeUri->getPath();
                    if ($relativeUri->getQuery()) {
                        $query = $relativeUri->getQuery();
                    } else {
                        $query = $this->getQuery();
                    }
                } else {
                    if (substr_compare($relativePath, '/', 0, 1) === 0) {
                        $path = $relativeUri->getPath();
                    } else {
                        $path = $this->mergePath($relativePath);
                    }
                    $query = $relativeUri->getQuery();
                }
                $user = $this->getUser();
                $host = $this->getHost();
                $port = $this->getPort();
            }
            $scheme = $this->getScheme();
        }
        $fragment = $relativeUri->getFragment();

        return new static($scheme, $host, $user, $port, $path, $query, $fragment);
    }

    /**
     * @brief Merge a relative URI to base URI.
     *
     * @param string $relativePath
     *
     * @return string
     *
     * @see https://tools.ietf.org/html/rfc3986#section-5.2.3
     */
    private function mergePath(string $relativePath): string
    {
        if ($this->hasAuthority() && $this->path === '') {
            return '/' . $relativePath;
        }

        $lastSlash = strrpos($this->path, '/');
        if ($lastSlash === false) {
            throw new LogicException('An error occurred in the resolution algorithm');
        }

        if ($lastSlash === strlen($this->path) - 1) {
            return $this->path . $relativePath;
        }

        return substr($this->path, 0, $lastSlash - strlen($this->path) + 1) . $relativePath;
    }

    /**
     * @see UriInterface::hasAuthority
     */
    public function hasAuthority(): bool
    {
        return $this->host || $this->user || $this->port !== null;
    }
}
