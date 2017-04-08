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

final class IPv4 implements HostInterface
{
    /** @var string $address IPv4 Address */
    private $address;

    /** @var string $subDomainRegex Domain component regex */
    private static $decOctetRegex = '/^(?:(?:[0-9]|[0-9][0-9]|[0-1][0-9][0-9]|2[0-4][0-9]|25[0-5])(?:\.|$)){4,4}/';

    /**
     * IPv4 constructor.
     *
     * @param string $address
     */
    public function __construct($address)
    {
        if (! $this->isValidAddress($address)) {
            throw new InvalidArgumentException(sprintf('Invalid address: %s', $address));
        }

        $this->address = $this->mapAddress($address);
    }

    /**
     * Validate a domain string
     *
     * @param string $address IPv4 address
     *
     * @return bool
     * @see https://tools.ietf.org/html/rfc1034#section-3.5
     */
    private function isValidAddress(string $address): bool
    {
        return preg_match(self::$decOctetRegex, $address);
    }

    /**
     * Map address to canonical format
     *
     * @param string $address IPv4 address
     *
     * @return string
     */
    private function mapAddress(string $address): string
    {
        return preg_replace('/(^|\.)0+([^\.])/', '\1\2', $address);
    }

    /**
     * @see https://github.com/west-php/west-uri/blob/master/src/Host/Domain.php
     */
    public function __toString(): string
    {
        return $this->address;
    }
}
