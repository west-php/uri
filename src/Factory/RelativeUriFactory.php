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

use West\Uri\RelativeUri;
use West\Uri\UriInterface;

/**
 * @brief Relative URI reference factory.
 *
 * @see http://tools.ietf.org/html/rfc3986#section-4.2 URI specification section 4.2
 *
 * @author Christopher Evans <c.m.evans@gmx.co.uk>
 * @date 09 April 2017
 */
final class RelativeUriFactory extends AbstractUriFactory
{
    /**
     * @see UriFactoryInterface
     */
    public function createFromString(string $uriString): UriInterface
    {
        return new RelativeUri(... $this->decomposeAndDecodeUri($uriString));
    }
}
