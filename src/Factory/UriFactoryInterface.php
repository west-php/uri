<?php
/*
 * This file is part of the West\\Uri package
 *
 * (c) Chris Evans <c.m.evans@gmx.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace West\Uri\Factory;

use West\Uri\UriInterface;

/**
 * @brief Interface for an RFC 3986 URI factory.
 *
 * @see http://tools.ietf.org/html/rfc3986 URI specification
 *
 * @author Christopher Evans <c.m.evans@gmx.co.uk>
 * @date 09 April 2017
 */
interface UriFactoryInterface
{
    /**
     * @brief Build a URI object from a string.
     *
     * @param string $uriString URI string
     *
     * @return UriInterface URI object
     */
    public function createFromString(string $uriString): UriInterface;
}
