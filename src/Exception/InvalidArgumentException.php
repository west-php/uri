<?php
/*
 * This file is part of the West\\Uri package
 *
 * (c) Chris Evans <c.m.evans@gmx.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace West\Uri\Exception;

use InvalidArgumentException as RootInvalidArgumentException;

/**
 * @brief Invalid argument exception for West::Uri namespace
 *
 * @details This class is to allow West::Uri exceptions to be caught
 * and does not define any functionality beyond that of
 * %InvalidArgumentException.
 *
 * @author Christopher Evans <c.m.evans@gmx.co.uk>
 *
 * @see http://php.net/manual/en/class.invalidargumentexception.php %InvalidArgumentException
 *
 * @date 09 April 2017
 */
final class InvalidArgumentException extends RootInvalidArgumentException
{

}
