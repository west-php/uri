<?php
/*
 * This file is part of the West\\Uri package
 *
 * (c) Chris Evans <c.m.evans@gmx.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace West\Uri\Host;

use West\Uri\Exception\InvalidArgumentException;

/**
 * @brief %Domain host implementation
 *
 * @see http://tools.ietf.org/html/rfc3986#section-3.2.2 URI specification: host
 *
 * @author Christopher Evans <c.m.evans@gmx.co.uk>
 * @date 09 April 2017
 */
final class Domain implements HostInterface
{
    /**
     * @brief Normalized domain string.
     *
     * @var string $domain
     */
    private $domain;

    /**
     * @brief Subdomain component regex.
     *
     * @var string $subDomainRegex
     */
    private static $subDomainRegex = '/^(?:(?:[a-z]|[a-z][a-z0-9-]*[a-z0-9])(?:\.|$))+/i';//'/^[a-z][a-z0-9-]*[a-z0-9]$/i';

    /**
     * @brief Domain constructor.
     *
     * @param string $domain
     */
    public function __construct($domain)
    {
        if (! $this->isValidDomain($domain)) {
            throw new InvalidArgumentException(sprintf('Invalid domain: %s', $domain));
        }

        $this->domain = $this->mapDomain($domain);
    }

    /**
     * @brief Validate a domain string.
     *
     * @param string $domain
     *
     * @return bool
     * @see https://tools.ietf.org/html/rfc1034#section-3.5
     */
    private function isValidDomain(string $domain): bool
    {
        return preg_match(self::$subDomainRegex, $domain);
    }

    /**
     * @brief Parse a domain string to standardized format.
     *
     * @details The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.2.2.
     *
     * @param string $domain
     *
     * @return string
     * @see https://tools.ietf.org/html/rfc3986#section-3.2.2
     */
    private function mapDomain(string $domain): string
    {
        return strtolower($domain);
    }

    /**
     * @see HostInterface
     */
    public function __toString(): string
    {
        return $this->domain;
    }
}
