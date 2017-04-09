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

use West\Uri\Exception\InvalidArgumentException;
use West\Uri\Http;
use West\Uri\UriInterface;

/**
 * @brief Http(s) URI Factory.
 *
 * @see http://tools.ietf.org/html/rfc3986 URI specification
 * @see https://tools.ietf.org/html/rfc7230#section-2.7 http(s) URI specification
 *
 * @author Christopher Evans <c.m.evans@gmx.co.uk>
 * @date 09 April 2017
 */
final class HttpFactory extends AbstractUriFactory
{
    /**
     * @brief Http(s) URI scheme component regex
     *
     * @var string
     */
    private static $schemeRegex = '/^(https?):/i';

    /**
     * @see UriFactoryInterface
     */
    public function createFromString(string $uriString): UriInterface
    {
        // trim scheme
        $matches = [];
        if (! preg_match(self::$schemeRegex, $uriString, $matches)) {
            throw new InvalidArgumentException(sprintf('Invalid http(s) URI: %s', $uriString));
        }

        $scheme = $matches[1];
        $uriString = preg_replace(self::$schemeRegex, '', $uriString);

        return new Http(
            $scheme,
            ... $this->decomposeAndDecodeUri($uriString)
        );
    }
}
