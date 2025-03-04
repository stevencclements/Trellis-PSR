<?php

/**
 * @package     Cts\Trellis
 * @author      Clementine Solutions
 * @copyright   Clementine Technology Solutions LLC. (dba. Clementine
 *              Solutions). All rights reserved.
 * 
 * @version     1.0.0
 * @since       1.0.0
 */

declare(strict_types=1);

namespace Cts\Trellis\Core;

use Psr\Http\Message\UriInterface;

/**
 * Uri
 * 
 * Value object representing a URI.
 * 
 * This interface is meant to represent URIs according to RFC 3986 and to
 * provide methods for most common operations. Additional functionality for
 * working with URIs can be provided on top of the interface or externally.
 * Its primary use is for HTTP requests, but may also be used in other
 * contexts.
 * 
 * Instances of this interface are considered immutable; all methods that
 * might change state MUST be implemented in such that they retain the internal
 * state of the current instance and return an instance that contains the
 * changed state.
 * 
 * Typically, the `Host` header will also be presented in the request message.
 * For server-side requests, the scheme will typically be discoverable in the
 * server parameters.
 * 
 * @see http://tools.ietf.org/html/rfc3986 (the URI specification)
 */
class Uri implements UriInterface
{
    /**
     * @param       string              $scheme
     * 
     * The scheme component of the URI, for example `HTTP` or `HTTPS`.
     */
    protected string $scheme;

    /**
     * @param       string              $authority
     * 
     * The authority component of the URI, for example
     * `[user-info@]host[:port]`.
     */
    protected string $authority = '';

    /**
     * @param       string              $userInfo
     * 
     * The URI user information, for example `username[:password]`.
     */
    protected string $userInfo = '';

    /**
     * @param       string              $host
     * 
     * The host component of the URI.
     */
    protected string $host;

    /**
     * @param       int                 $port
     * 
     * The port component of the URI, for example `80`.
     */
    protected int $port;

    /**
     * @param       string              $path
     * 
     * The path component of the URI.
     */
    protected string $path;

    /**
     * @param       string              $query
     * 
     * The query string of the URI.
     */
    protected string $query;

    /**
     * @param       string              $fragment
     * 
     * The fragment component of the URI.
     */
    protected string $fragment;


    /**
     * Constructor
     * 
     * Create a new `Uri` instance with the specified (or default) class
     * properties.
     */
    public function __construct(
        string $fragment = ''
    ) {
        $this->scheme = $_SERVER['REQUEST_SCHEME'];
        $this->host = $_SERVER['HTTP_HOST'];
        $this->port = intval($_SERVER['SERVER_PORT']);
        $this->path = strtok($_SERVER['REQUEST_URI'], '?');
        $this->query = $_SERVER['QUERY_STRING'];
        $this->fragment = $fragment;
    }


    /**
     * __toString
     * 
     * Return the string representation as a URI reference.
     * 
     * Depending on which components of the URI are present, the resulting
     * string is either a full URI or relative reference according to RFC
     * 3986 Section 4.1. This method concatenates the various components of
     * the URI, using appropriate delimiters:
     * 
     *      - If a scheme is present, it MUST be suffixed by `:`.
     *      - If an authority is present, it MUST be prefixed by `//`.
     *      - The path can be concatenated without delimiters. But there are
     *        two cases where the path has to be adjusted to make the URI
     *        reference valid as PHP does not allow exceptions in __toString
     *        methods:
     *          - If the path is rootless and an authority is present, the path
     *            MUST be prefixed by `/`.
     *          - If the path is starting with one or more `/` and no authority
     *            is present, the starting slashes MUST be reduced to one.
     *      - If a query is present, it MUST be prefixed by `?`.
     *      - If a fragment is present, is MUST be prefixed by `#`.
     * 
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * 
     * @return      string              $uri
     */
    public function __toString(): string
    {
        return '';
    }


    /**
     * getScheme
     * 
     * Retrieve the scheme component of the URI.
     * 
     * If no scheme is present, this method MUST return an empty string.
     * 
     * The value returned MUST be normalized to lowercase, per RFC 3986 section
     * 3.1.
     * 
     * The trailing `:` character is not part of the scheme and MUST NOT be
     * added.
     * 
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     * 
     * @return      string              $scheme
     * 
     * The URI scheme.
     */
    public function getScheme(): string
    {
        return strtolower($this->scheme);
    }


    /**
     * getAuthority
     * 
     * Retrieve the authority component of the URI.
     * 
     * If no authority information is present, this method MUST return an
     * empty string.
     * 
     * The authority syntax of the URI is:
     * 
     *      <pre>[user-info@]host[:port]</pre>
     * 
     * If the port component is not set or is the standard port for the current
     * scheme, it SHOULD NOT be included.
     * 
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     * 
     * @return      string              $authority
     * 
     * The URI authority, in the form `[user-info@]host[:port]`.
     */
    public function getAuthority(): string
    {
        $authority = $this->host;

        if (!empty($this->userInfo)) {
            $authority = $this->userInfo . '@' . $authority;
        }

        if (!empty($this->port) && $this->port !== $this->getStandardPort($this->scheme)) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }


    /**
     * getUserInfo
     * 
     * Retrieve the user information component of the URI.
     * 
     * If no user information is present, this method MUST return an empty
     * string.
     * 
     * If a user is present in the URI, this will return that value;
     * additionally, if the password is also present, it will be appended to
     * the user value, with a colon (`:`) separating their values.
     * 
     * The trailing `@` character is not part of the user information and MUST
     * NOT be added.
     * 
     * @return      string              $userInfo
     * 
     * The URI user information, in the form `username[:password]`.
     */
    public function getUserInfo(): string
    {
        return $this->userInfo;
    }


    /**
     * getHost
     * 
     * Retrieve the host component of the URI.
     * 
     * If no host is present, this method MUST return an empty string.
     * 
     * The value returned MUST be normalized to lowercase, per RFC 3986 Section
     * 3.2.2.
     * 
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     * 
     * @return      string              $host
     * 
     * The URI host.
     */
    public function getHost(): string
    {
        return strtolower($this->host);
    }


    /**
     * getPort
     * 
     * Retrieve the port component of the URI.
     * 
     * If a port is present, and it is non-standard for the current scheme,
     * this method MUST return it as an integer. If the port is the standard
     * port used with the current scheme, this method SHOULD return null.
     * 
     * If no port is present, and no scheme is present, this method MAY return
     * the standard port for that scheme, but SHOULD return null.
     * 
     * @return      ?int                $port
     * 
     * The URI port.
     */
    public function getPort(): ?int
    {
        return ($this->port !== $this->getStandardPort($this->scheme)) ? $this->port : null;
    }


    /**
     * getPath
     * 
     * Retrieve the path component of the URI.
     * 
     * The path can either be empty, absolute (starting with a slash), or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     * 
     * Normally, the empty path `` and absolute path `/` are considered equal
     * as defined in RFC 7230 Section 2.7.3. But this method MUST NOT
     * automatically do this normalization because in contexts with a trimmed
     * base path (for example, the front controller), this difference becomes
     * significant. It is the task of the user to handle both `` and `/`.
     * 
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine which characters to encode, refer to RFC
     * 3986, Sections 2 and 3.3.
     * 
     * As an example, if the value should include a slash (`/`) not intended as
     * a delimiter between path segments, that value MUST be passed in encoded
     * form (for example `%2F`) to the instance.
     * 
     * @see     https://tools.ietf.org/html/rfc3986#section-2
     * @see     https://tools.ietf.org/html/rfc3986#section-3.3
     * 
     * @return      string              $path
     * 
     * The URI path.
     */
    public function getPath(): string
    {
        return $this->path;
    }


    /**
     * getQuery
     * 
     * Retrieve the query string of the URI.
     * 
     * If no query string is present, this method MUST return an empty string.
     * 
     * The leading `?` character is not part of the query and MUST NOT be
     * added.
     * 
     * The value returned MUST be percent-encoded, but MUST NOT be double-
     * encode any characters. To determine which characters to encode, refer to
     * RFC 3986, Sections 2 and 3.4.
     * 
     * As an example, if a value in a key/pair of the query string should
     * include an ampersand (`&`) not intended as a delimiter between values,
     * that value MUST be passed in encoded form (for example `%26`) to the
     * instance.
     * 
     * @see     https://tools.ietf.org/html/rfc3986#section-2
     * @see     https://tools.ietf.org/html/rfc3986#section-3.4
     * 
     * @return      string              $query
     * 
     * The URI query string.
     */
    public function getQuery(): string
    {
        return $this->query;
    }


    /**
     * getFragment
     * 
     * Retrieve the fragment component of the URI.
     * 
     * If no fragment is present, this method MUST return an empty string.
     * 
     * The leading `#` character is not part of the fragment and MUST NOT be
     * added.
     * 
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine which characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.5.
     * 
     * @see     https://tools.ietf.org/html/rfc3986#section-2
     * @see     https://tools.ietf.org/html/rfc3986#section-3.5
     * 
     * @return      string              $fragment
     * 
     * The URI fragment.
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }


    /**
     * withScheme
     * 
     * Return an instance with the specified schema.
     * 
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified scheme.
     * 
     * Implementations MUST support the schemes `http` and `https` case
     * insensitively, and MAY accommodate other schemes if required.
     * 
     * An empty scheme is equivalent to removing the scheme.
     * 
     * @param       string              $scheme
     * 
     * The scheme to use with the new instance.
     * 
     * @return      static
     * 
     * @throws      \InvalidArgumentException
     * 
     * For invalid and unsupported schemes.
     */
    public function withScheme(string $scheme): static
    {
        if (!preg_match('/^[a-z][a-z0-9+\-.]*$/i', $scheme)) {
            throw new \InvalidArgumentException('Invalid scheme format');
        }

        $new = clone $this;
        $new->scheme = strtolower($scheme);

        return $new;
    }


    /**
     * withUserInfo
     * 
     * Return an instance with the specified user information.
     * 
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified user information.
     * 
     * The password is optional, but the user information MUST include the
     * user; an empty string for the user is equivalent to removing the user
     * information.
     * 
     * @param       string              $user
     * 
     * The user name to use for authority.
     * 
     * @param       string              $password
     * 
     * The password associated with the user.
     * 
     * @return      static
     */
    public function withUserInfo(string $user, ?string $password = null): static
    {
        $userInfo = $user;

        if ($password !== null) {
            $userInfo .= ':' . $password;
        }

        $new = clone $this;
        $new->userInfo = $userInfo;

        return $new;
    }


    /**
     * withHost
     * 
     * Return an instance with the specified host.
     * 
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified host.
     * 
     * An empty host value is equivalent to removing the host.
     * 
     * @param       string              $host
     * 
     * The hostname to use with the new instance.
     * 
     * @return      static
     * 
     * @throws      \InvalidArgumentException
     * 
     * For an invalid hostname.
     */
    public function withHost(string $host): static
    {
        if (empty($host)) {
            throw new \InvalidArgumentException('Host cannot be empty');
        }

        $new = clone $this;
        $new->host = strtolower($host);

        return $new;
    }


    /**
     * withPort
     * 
     * Return an instance with the specified port.
     * 
     * This method MUST retain the state of the current instance, and return an
     * instance that contains the specified port.
     * 
     * Implementations MUST raise an exception for ports outside the
     * established TCP and UDP port ranges.
     * 
     * A null value provided for the port is equivalent to removing the port
     * information.
     * 
     * @param       ?int                $port
     * 
     * The port to use with the new instance. A null value removes the port
     * information.
     * 
     * @return      static
     * 
     * @throws      \InvalidArgumentException
     * 
     * For invalid ports.
     */
    public function withPort(?int $port): static
    {
        if ($port !== null && ($port < 1 || $port > 65535)) {
            throw new \InvalidArgumentException('Invalid port range');
        }

        $new = clone $this;
        $new->port = $port ?? $this->getStandardPort($this->scheme);

        return $new;
    }


    /**
     * withPath
     * 
     * This method MUST retain the state of the current instance, and return an
     * instance that contains the specified path.
     * 
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     * 
     * If an HTTP path is intended to be a host-relative rather than path-
     * relative then it must begin with a slash (`/`). HTTP paths not starting
     * with a slash are assumed to be relative to some base path known to the
     * application or consumer.
     * 
     * Users can provide both encoded and decoded path characters.
     * Implementations ensure the correct encoding as outlined in `getPath`.
     * 
     * @param       string              $path
     * 
     * The path to use with the new instance.
     * 
     * @return      static
     * 
     * @throws      \InvalidArgumentException
     * 
     * For invalid paths.
     */
    public function withPath(string $path): static
    {
        if (str_contains($path, '?') || str_contains($path, '#')) {
            throw new \InvalidArgumentException('Path cannot contain query or fragment delimiters');
        }

        $new = clone $this;
        $new->path = $path;

        return $new;
    }


    /**
     * withQuery
     * 
     * Return an instance with the specified query string.
     * 
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified query string.
     * 
     * Users can provide both encoded and decoded query characters.
     * Implementations ensure the correct encoding as outlined in `getQuery`.
     * 
     * An empty query string value is equivalent to removing the query string.
     * 
     * @param       string              $query
     * 
     * The query string to use with the new instance.
     * 
     * @return      static
     * 
     * @throws      \InvalidArgumentException
     * 
     * For invalid query strings.
     */
    public function withQuery(string $query): static
    {
        if (str_contains($query, '#')) {
            throw new \InvalidArgumentException('Query string cannot contain a fragment delimiter');
        }

        $new = clone $this;
        $new->query = $query;

        return $new;
    }


    /**
     * withFragment
     * 
     * Return an instance with the specified URI fragment.
     * 
     * This method MUST retain the state of the current instance, and return an
     * instance that contains the specified URI fragment.
     * 
     * Users can provide both encoded and decoded fragment characters.
     * Implementations ensure the correct encoding as outlined in `getFragment`.
     * 
     * An empty fragment value is equivalent to removing the fragment.
     * 
     * @param       string              $fragment
     * 
     * The fragment to use with the new instance.
     * 
     * @return      static
     * 
     * A new instance with the specified fragments.
     */
    public function withFragment(string $fragment): static
    {
        $new = clone $this;
        $new->fragment = $fragment;

        return $new;
    }


    /**
     * getStandardPort [PRIVATE]
     * 
     * Get the standard port for the current scheme.
     * 
     * @param       int             $scheme
     * 
     * @return      ?int            $port
     */
    private function getStandardPort(string $scheme): ?int
    {
        return match ($scheme) {
            'http' => 80,
            'https' => 443,
            default => null,
        };
    }
}
