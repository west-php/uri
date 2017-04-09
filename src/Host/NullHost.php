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
 * @brief %Null object for HostInterface
 *
 * @author Christopher Evans <c.m.evans@gmx.co.uk>
 * @date 09 April 2017
 */
final class NullHost implements HostInterface
{
    /**
     * @see HostInterface
     */
    public function __toString(): string
    {
        return '';
    }
}
