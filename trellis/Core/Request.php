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

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Request
 * 
 * Representation of an outgoing, client-side request.
 * 
 * Per the HTTP specification, this interface includes properties for each
 * of the following:
 * 
 *      - Protocol version
 *      - HTTP method
 *      - URI
 *      - Headers
 *      - Message body
 * 
 * During construction, implementations MUST attempt to set the `Host`
 * header from a provided URI if no `Host` header is provided.
 * 
 * Requests are considered immutable. All methods that might change state
 * MUST be implemented in such a way as to retain the internal state of the
 * current message and return an instance that contains the changed state.
 */
class Request extends Message implements RequestInterface
{
    /**
     * @param       string              $requestTarget
     * 
     * The HTTP target of the request, for example `origin-form`,
     * `absolute-form`, `authority-form`, or `asterisk-form`.
     */
    protected string $requestTarget;

    /**
     * @param       string              $method
     * 
     * The HTTP method of the request, for example `GET`, `POST,` `PATCH`,
     * `PUT`, `DELETE`, and so on.
     */
    protected string $method;

    /**
     * @param       Uri                 $uri
     * 
     * The uniform resource identifier (URI) of the request represented as an
     * instance of `Uri`.
     */
    protected Uri $uri;


    /**
     * Constructor
     * 
     * Create a new `Request` instance with the specified (or default) class
     * properties.
     */
    public function __construct(
        string $requestTarget,
        string $method,
        Uri $uri
    ) {

    }


    /**
     * getRequestTarget
     * 
     * Retrieves the request target of the message as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see `withRequestTarget`).
     * 
     * In most cases, this will be the origin-form of the composed URI, unless
     * a value was provided to the concrete implementation (see
     * `withRequestTarget` below).
     * 
     * If no URI is available, and no request target has been specifically
     * provided, this method MUST return the string `/`.
     * 
     * @return      string              $requestTarget
     * 
     * The target specified by the request.
     */
    public function getRequestTarget(): string
    {
        return '';
    }


    /**
     * getMethod
     * 
     * Retrieves the HTTP method of the request.
     * 
     * @return      string              $method
     */
    public function getMethod(): string
    {
        return '';
    }


    /**
     * getUri
     * 
     * Retrieves the URI instance.
     * 
     * This method MUST return a Uri instance.
     * 
     * @see http://tools.ietf.org/html/rfc3986#section-4.3
     * 
     * @return      Uri                 $uri
     * 
     * Returns a UriInterface instance representing the URI of the request.
     */
    public function getUri(): Uri
    {
        return $this->uri;
    }


    /**
     * withRequestTarget
     * 
     * Return an instance with the specified request target.
     * 
     * If the request needs a non-origin form request-target, for example for
     * specifying an absolute-form, authority-form, or asterisk-form, this
     * method may be used to create an instance with the specified request
     * target, verbatim.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request target.
     * 
     * @see http://tools.ietf.org/html/rfc7230#section-5.3
     * 
     * @param       mixed               $requestTarget
     * 
     * @return      static
     */
    public function withRequestTarget(string $requestTarget): static
    {
        return $this;
    }


    /**
     * withMethod
     * 
     * Return an instance with the provided HTTP method.
     * 
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request method.
     * 
     * @param       string              $method
     * 
     * Case-sensitive HTTP method.
     * 
     * @return      static
     * 
     * @throws      \InvalidArgumentException
     */
    public function withMethod(string $method): static
    {
        return $this;
    }


    /**
     * withUri
     * 
     * Returns an instance with the provided URI.
     * 
     * This method MUST update the `Host` header of the returned request by
     * default if the URI contains a host component. If the URI does not
     * contain a `Host` component, any preexisting `Host` header MUST be
     * carried over to the returned request.
     * 
     * You can opt-in to preserving the original state of the `Host` header by
     * setting `$preserveHost` to `true`. When `$preserveHost` is set to `true`,
     * this method interacts with the `Host` header in the following ways:
     * 
     *      - If the `Host` header is missing or empty, and the new URI
     *        contains a `Host` component, this method MUST update the
     *        `Host` header in the returned request.
     *      - If the `Host` header is missing or empty, and the new URI does
     *        not contain a `Host` component, this method MUST NOT update the
     *        `Host` header in the returned request.
     *      - If a `Host` header is present and not empty, this method MUST NOT
     *        update the `Host` header in the returned request.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new UriInterface instance.
     * 
     * @see http://tools.ietf.org/html/rfc3986#section-4.3
     * 
     * @param       Uri             $uri
     * 
     * New request URI to use.
     * 
     * @param       bool            $preserveHost
     * 
     * Preserve the original state of the `Host` header.
     * 
     * @return      static
     */
    public function withUri(UriInterface $uri, bool $preserveHost = false): static
    {
        return $this;
    }
}
