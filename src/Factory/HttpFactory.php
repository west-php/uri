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

final class HttpFactory extends AbstractUriFactory
{
    private static $schemeRegex = '/^(https?):/i';

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
