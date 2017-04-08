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
 * @Revs({1, 8, 64, 4096})
 * @Iterations(10)
 */
class DomainBench
{
    public function benchConstruct()
    {
        new Domain('www.example.com');
    }
}
