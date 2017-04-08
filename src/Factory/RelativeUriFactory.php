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

final class RelativeUriFactory extends AbstractUriFactory
{
    public function createFromString(string $uriString): UriInterface
    {
        return new RelativeUri(... $this->decomposeAndDecodeUri($uriString));
    }
}
