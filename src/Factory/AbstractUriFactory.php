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

use West\Uri\Host\Domain;
use West\Uri\Host\IPv4;
use West\Uri\UriInterface;

/**
 * @brief Absract RFC 3986 URI Factory.
 *
 * @see http://tools.ietf.org/html/rfc3986 URI specification
 *
 * @author Christopher Evans <c.m.evans@gmx.co.uk>
 * @date 09 April 2017
 */
abstract class AbstractUriFactory implements UriFactoryInterface
{
    /**
     * @brief Authority string regex
     *
     * @details
     * /^
     *   (?:
     *     (?P<user>.*?)@                   (?# user)
     *   )?
     *   (?:
     *     (?P<domain>[a-z][a-z0-9\+\.-]*)  (?# domain regex)
     *     |
     *     (?P<ipv4>\d{1,3}(\.\d{1,3}){3})  (?# ipv4 regex)
     *   )
     *   (?:
     *     :(?P<port>\d*)                   (?# port)
     *   )?
     * $/ix'
     *
     * @var string
     */
    private static $authorityRegex = '/^(?:(?P<user>.*?)@)?(?:(?P<domain>[a-z][a-z0-9\+\.-]*)|(?P<ipv4>\d{1,3}(\.\d{1,3}){3}))(?::(?P<port>\d+))?$/i';

    /**
     * @brief Break down URI into host, user, port, path, query
     * and fragment components.
     *
     * @param string $uriString URI string
     *
     * @return array URI components
     */
    protected function decomposeAndDecodeUri(string $uriString): array
    {
        // trim leading //
        $uriString = preg_replace('/^\/\//', '', $uriString);

        // trim fragment, query, path
        $fragment = mb_substr($this->trimAndDecode('#', $uriString), 1);
        $query = mb_substr($this->trimAndDecode('?', $uriString), 1);
        $path = $this->trimAndDecode('/', $uriString);

        // match authority component by regex
        $matches = [];
        preg_match(self::$authorityRegex, $uriString, $matches);

        // get user and port
        $user = empty($matches['user']) ? '' : $matches['user'];
        $port = empty($matches['port']) ? null : $matches['port'];

        // get host
        $host = null;
        if (! empty($matches['domain'])) {
            $host = new Domain($matches['domain']);
        } elseif (! empty($matches['ipv4'])) {
            $host = new IPv4($matches['ipv4']);
        }

        return [$host, $user, $port, $path, $query, $fragment];
    }

    /**
     * Chop a segment off the end of a string and URL decode the characters.
     *
     * @param string $character Character to truncate after
     * @param string $uriString String to trim
     *
     * @return string Trimmed string
     */
    private function trimAndDecode(string $character, string &$uriString): string
    {
        $trimmedString = '';
        $firstCharacterInstance = mb_strpos($uriString, $character);
        if ($firstCharacterInstance !== false) {
            if ($firstCharacterInstance + 1 < mb_strlen($uriString)) {
                $trimmedString = urldecode(
                    mb_substr($uriString, $firstCharacterInstance)
                );
            }

            $uriString = mb_substr($uriString, 0, $firstCharacterInstance);
        }

        return $trimmedString;
    }

    /**
     * @see UriFactoryInterface
     */
    public abstract function createFromString(string $uriString): UriInterface;
}
