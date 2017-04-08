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

/**
 * @brief Interface for a URI host.
 *
 * @see http://tools.ietf.org/html/rfc3986#section-3.2.2 URI specification: host
 *
 * @author Christopher Evans <c.m.evans@gmx.co.uk>
 * @date 26 March 2017
 */
interface HostInterface
{
    /**
     * Return the string representation of the host.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.2.2.
     *
     * @return string
     */
    public function __toString(): string;
}
