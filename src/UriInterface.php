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
 * @brief Interface for an RFC 3986 URI.
 *
 * @see http://tools.ietf.org/html/rfc3986 URI specification
 *
 * @author Christopher Evans <c.m.evans@gmx.co.uk>
 * @date 26 March 2017
 */
interface UriInterface
{
    /**
     * @brief Retrieve the scheme component of the URI.
     *
     * @details If no scheme is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.1.
     *
     * The trailing ":" character is not part of the scheme and MUST NOT be
     * added.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     *
     * @return string The URI scheme.
     */
    public function getScheme(): string;

    /**
     * @brief Retrieve the authority component of the URI.
     *
     * @details If no authority information is present, this method MUST return an empty
     * string.
     *
     * The authority syntax of the URI is:
     *
     *   [user-info@]host[:port]
     *
     * The value returned MUST be percent-encoded.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     *
     * @return string The URI authority, in "[user-info@]host[:port]" format.
     */
    public function getAuthority(): string;

    /**
     * @brief Retrieve the user information component of the URI.
     *
     * @details If no user information is present, this method MUST return an empty
     * string.
     *
     * If a user is present in the URI, this will return that value.
     *
     * The trailing "@" character is not part of the user information and MUST
     * NOT be added.
     *
     * The value returned MUST not be percent-encoded.
     *
     * @return string The URI user information.
     */
    public function getUser(): string;

    /**
     * @brief Retrieve the host component of the URI.
     *
     * @details If no host is present, this method MUST return `null`.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     * @see Host::HostInterface
     *
     * @return HostInterface The URI host.
     */
    public function getHost(): HostInterface;

    /**
     * @brief Retrieve the port component of the URI.
     *
     * @details If a port is present, and it is non-standard for the current scheme,
     * this method MUST return it as an integer.
     *
     * If no port is present, this method MUST return
     * a null value.
     *
     * If no port is present, but a scheme is present, this method MAY return
     * the standard port for that scheme, but SHOULD return null.
     *
     * @return null|int The URI port.
     */
    public function getPort(): ?int;

    /**
     * @brief Retrieve the path component of the URI.
     *
     * @details The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * The value returned MUST not be percent-encoded.
     *
     * The value SHOULD be normalized if it is an abolute URI, per
     * RFC 3986 Section 5.2.4.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     *
     * @return string The URI path.
     */
    public function getPath(): string;

    /**
     * @brief Retrieve the query string of the URI.
     *
     * @details If no query string is present, this method MUST return an empty string.
     *
     * The leading "?" character is not part of the query and MUST NOT be
     * added.
     *
     * The value returned MUST not be percent-encoded.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     *
     * @return string The URI query string.
     */
    public function getQuery(): string;

    /**
     * @brief Retrieve the fragment component of the URI.
     *
     * @details If no fragment is present, this method MUST return an empty string.
     *
     * The leading "#" character is not part of the fragment and MUST NOT be
     * added.
     *
     * The value returned MUST not be percent-encoded.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     *
     * @return string The URI fragment.
     */
    public function getFragment(): string;

    /**
     * @brief Return the string representation as a URI reference.
     *
     * @details Depending on which components of the URI are present, the resulting
     * string is either a full URI or relative reference according to RFC 3986,
     * Section 4.1. The method concatenates the various components of the URI,
     * using the appropriate delimiters:
     *
     * - If a scheme is present, it MUST be suffixed by ":".
     * - If an authority is present, it MUST be prefixed by "//".
     * - The path can be concatenated without delimiters. But there are two
     *   cases where the path has to be adjusted to make the URI reference
     *   valid as PHP does not allow to throw an exception in __toString():
     *     - If the path is rootless and an authority is present, the path MUST
     *       be prefixed by "/".
     *     - If the path is starting with more than one "/" and no authority is
     *       present, the starting slashes MUST be reduced to one.
     * - If a query is present, it MUST be prefixed by "?".
     * - If a fragment is present, it MUST be prefixed by "#".
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * @brief Return true if and only if the URI is an absolute URI in
     * as per RFC 3986 Section 4.3.
     *
     * @details Equivalently, this method should return true if and
     * only if the URI has no fragment component.
     *
     * @return bool
     */
    public function isAbsoluteUri(): bool;

    /**
     * @brief Resolve a relative URI as per RFC 3986 Section 5.
     *
     * @details The result SHOULD be path-reduced as per RFC 3986
     * Section 5.2.4.
     *
     * @param UriInterface $relativeUri
     * @return UriInterface
     */
    public function resolveRelative(UriInterface $relativeUri): UriInterface;

    /**
     * @brief Return true if and only if the URI has an authority component
     * i.e if there is no port, user or host.
     *
     * @details For example this method will always return true for an http(s)
     * URI.
     *
     * @return bool
     */
    public function hasAuthority(): bool;
}
