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

use West\Uri\Exception\InvalidArgumentException;
use West\Uri\Host\HostInterface;

/**
 * @brief HTTP URI implementation.
 *
 * @details Class encapsulating an http(s) URI.
 *
 * @author Christopher Evans <c.m.evans@gmx.co.uk>
 * @see UriInterface
 * @see https://tools.ietf.org/html/rfc7230#section-2.7
 * @date 26 March 2017
 */
final class Http extends Uri
{
    /**
     * Http constructor.
     *
     * @param string $scheme
     * @param HostInterface $host
     * @param string $user
     * @param int $port
     * @param string $path
     * @param string $query
     * @param string $fragment
     */
    public function __construct(
        string $scheme,
        HostInterface $host,
        string $user,
        int $port,
        string $path,
        string $query,
        string $fragment
    ) {
        if (! $this->isValidScheme($scheme)) {
            throw new InvalidArgumentException(sprintf('Invalid scheme %s', $scheme));
        }

        parent::__construct($scheme, $host, $user, $port, $path, $query, $fragment);
    }

    /**
     * @brief Validate http(s) URI scheme.
     *
     * @param string $scheme
     *
     * @return bool
     *
     * @see https://tools.ietf.org/html/rfc7230#section-2.7
     */
    private function isValidScheme(string $scheme): bool
    {
        switch (strtolower($scheme)) {
            case 'http':
            case 'https':
                return true;
        }

        return false;
    }

    /**
     * @brief Create a new instace with the given URI scheme.
     *
     * @param string $scheme
     *
     * @return UriInterface
     */
    public function withScheme(string $scheme): UriInterface
    {
        return new self(
            $scheme,
            $this->getHost(),
            $this->getUser(),
            $this->getPort(),
            $this->getPath(),
            $this->getQuery(),
            $this->getFragment()
        );
    }
}
