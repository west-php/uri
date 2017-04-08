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

use West\Uri\Host\HostInterface;

/**
 * @brief Relative URI implementation.
 *
 * @details Class encapsulating URIs with no scheme.
 *
 * Intended for use with the UriInterface::resolveRelative
 * function.
 *
 * @author Christopher Evans <c.m.evans@gmx.co.uk>
 * @see UriInterface
 * @see http://www.php-fig.org/psr/psr-3/
 * @date 26 March 2017
 */
final class RelativeUri extends Uri
{
    public function __construct(
        ?HostInterface $host,
        string $user,
        ?int $port,
        string $path,
        string $query,
        string $fragment
    ) {
        parent::__construct('', $host, $user, $port, $path, $query, $fragment);
    }
}
