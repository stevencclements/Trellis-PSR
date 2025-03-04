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

use Psr\Http\Message\ServerRequestInterface;

/**
 * Representation of an incoming, server-side HTTP request.
 * 
 * Per the HTTP specification, this interface includes properties for
 * 
 *      - Protocol version
 *      - HTTP method
 *      - URI
 *      - Headers
 *      - Message body
 * 
 * Additionally, it encapsulates all data as it has arrived at the
 * application from the CGI and/or PHP environment, including:
 * 
 *      - The values represented in `$_SERVER`.
 *      - Any cookies provided (generally with `$_COOKIE`).
 *      - Query string arguments (generally through `$_GET`, or parsed
 *        using `parse_str`).
 *      - Uploaded files, if any (as represented by `$_FILES`).
 *      - Deserialized body parameters (generally from `$_POST`).
 * 
 * `$_SERVER` values MUST be treated as immutable as they represent
 * application state at the time of the request; as such no methods
 * are provided to allow modification of those values. The other values
 * provide such methods, as they can be restored from $_SERVER or the
 * request body, and may need treatment during the application (for
 * example, body parameters may be deserialized based on content type).
 * 
 * Additionally, this interface recognizes the utility of introspecting a
 * request to derive and match additional parameters (for example, through
 * URI path matching, decrypting cookie values, deserializing non-form-encoded
 * body content, matching authorization headers to users, and so on). These
 * parameters are stored in an `attributes` property.
 * 
 * Requests are considered immutable. All methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 */
class ServerRequest extends Request implements ServerRequestInterface
{
    /**
     * @param       array               $serverParameters
     * 
     * Represents server values retrieved from the `$_SERVER` PHP
     * super global.
     */
    protected array $serverParameters;

    /**
     * @param       array               $queryParameters
     * 
     * Represents the query parameters retrieved from the `$_GET` PHP
     * super global.
     */
    protected array $queryParameters;

    /**
     * @param       mixed               $postParameters
     * 
     * Represents the post parameters retrieved from the `$_POST` PHP
     * super global.
     */
    protected mixed $postParameters;

    /**
     * @param       array               $cookies
     * 
     * Represents the cookies retrieved from the `$_COOKIE` PHP super
     * global.
     */
    protected array $cookies;

    /**
     * @param       array               $uploadedFiles
     * 
     * Represents the metadata for uploaded files retrieved from the
     * `$_FILES` super global.
     */
    protected array $uploadedFiles;

    /**
     * @param       mixed               $attributes
     * 
     * Represents the attributes specified in the request.
     */
    protected mixed $attributes;


    /**
     * Constructor
     * 
     * Create a new `ServerRequest` instance with the specified (or default)
     * class properties.
     */
    public function __construct(
        array $serverParameters,
        array $queryParameters,
        array $postParameters,
        array $cookies,
        array $uploadedFiles,
        array $attributes
    ) {

    }


    /**
     * getServerParams
     * 
     * Retrieve server parameters.
     * 
     * Retrieves data related to the incoming request environment, typically
     * derived from the PHP `$_SERVER` super global. The data IS NOT REQUIRED
     * to originate from `$_SERVER`.
     * 
     * @return      array               $serverParameters
     */
    public function getServerParams(): array
    {
        return [];
    }


    /**
     * getQueryParams
     * 
     * Retrieve query string arguments.
     * 
     * Retrieves the deserialized query string arguments, if any.
     * 
     * ————— ————— ————— ————— { !! NOTE !! } ————— ————— ————— —————
     * Query parameters may not be in sync with the URI or server parameters.
     * If you need to ensure you are only getting the original values, you may
     * need to parse the query string from `getUri->getQuery()` or from the
     * `QUERY_STRING` server parameter.
     * 
     * @return      array               $queryParameters
     */
    public function getQueryParams(): array
    {
        return [];
    }


    /**
     * getParsedBody
     * 
     * Retrieve any parameters provided in the request body.
     * 
     * If the request `Content-Type` is either `x-www-form-urlencoded` or
     * `multipart/form-data`, and the request method is `POST`, this method
     * MUST return the contents of `$_POST`.
     * 
     * Otherwise, this method may return any results of deserializing the
     * request body content; as parsing returns structured content, the
     * potential types MUST be arrays or objects only. A null value indicates
     * the absence of body content.
     * 
     * @return      mixed               $postParameters
     */
    public function getParsedBody(): mixed
    {
        return null;
    }


    /**
     * getCookieParams
     * 
     * Retrieves cookies sent by the client to the server.
     * 
     * The data MUST be compatible with the structure of the `$_COOKIE` super
     * global.
     * 
     * @return      array               $cookies
     */
    public function getCookieParams(): array
    {
        return [];
    }


    /**
     * getUploadedFiles
     * 
     * Retrieve normalized file upload data.
     * 
     * This method returns upload metadata in a normalized tree, with each leaf
     * an instance of `Psr\Http\Message\UploadedFileInterface`.
     * 
     * These values MAY be prepared from `$_FILES` or the message body during
     * instantiation, or MAY be injected through `withUploadedFiles`.
     * 
     * @return      array               $uploadedFiles
     */
    public function getUploadedFiles(): array
    {
        return [];
    }


    /**
     * getAttributes
     * 
     * Retrieve attributes derived from the request.
     * 
     * The request `attributes` may be used to allow injection of any
     * parameters derived from the request. For example, the results of path
     * match operations; the results of decrypting cookies; the results of 
     * deserializing non-form-encoded message bodies; and so on. Attributes
     * will be application and request specific, and CAN be mutable.
     * 
     * @return      mixed               $attributes
     */
    public function getAttributes(): array
    {
        return [];   
    }


    /**
     * getAttribute
     * 
     * Retrieves a single derived request attribute as described in
     * `getAttributes`. If the attribute has not been previously set,
     * returns the default value as provided.
     * 
     * This method obviates the need for a `hasAttribute` method, as
     * it allows specifying a default value to return if the attribute
     * is not found.
     * 
     * @see `getAttributes`
     * 
     * @param       string              $name
     * 
     * The attribute name.
     * 
     * @param       mixed               $default
     * 
     * Default value to return if the attribute does not exist.
     * 
     * @return      mixed
     */
    public function getAttribute(string $name, $default = null): mixed
    {
        return [];
    }


    /**
     * withQueryParams
     * 
     * Return an instance with the specified query string arguments.
     * 
     * These values SHOULD remain immutable over the course of the incoming
     * request. They MAY be injected during instantiation, such as from the PHP
     * super global, or MAY be derived from some other value such as the URI.
     * In cases where the arguments are parsed from the URI, the data MUST be
     * compatible with the return value of the PHP `parse_str` function for
     * purposes of how duplicate query parameters are handled, and how nested
     * sets are handled.
     * 
     * Setting query string arguments MUST NOT change the URI stored by the
     * request, nor the values in the server parameters.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated query string arguments.
     * 
     * @param       array               $queryParameters
     * 
     * An array of query string arguments, typically from `$_GET`.
     * 
     * @return      static
     */
    public function withQueryParams(array $query): static
    {
        return $this;
    }


    /**
     * withParsedBody
     * 
     * Return an instance with the specified body parameters.
     * 
     * These MAY be injected during instantiation.
     * 
     * If the request `Content-Type` is either `application/x-www-form-encoded`
     * or `multipart/form-data`, and the request method is `$_POST`, use this
     * method ONLY to inject the contents of `$_POST`.
     * 
     * The data is NOT REQUIRED to come from `$_POST`, but MUST be the results
     * of deserializing the request body content. Deserialization/parsing
     * returns structured data, and, as such, this method ONLY accepts arrays
     * or objects, or a null value if nothing was available to parse.
     * 
     * As an example, if content negotiation determines that the request data
     * is a JSON payload, this method could be used to create a request
     * instance with the deserialized parameters.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     * 
     * @param       mixed               $postParameters
     * 
     * The deserialized body data. This will typically be in an array or
     * an objects.
     * 
     * @return      static
     * 
     * @throws      \InvalidArgumentException
     * 
     * If an unsupported argument type is provided.
     */
    public function withParsedBody(mixed $data): static
    {
        return $this;
    }


    /**
     * withCookieParams
     * 
     * Return an instance with the specified cookies.
     * 
     * The data is NOT REQUIRED to come from the `$_COOKIE` super global,
     * but MUST be compatible with the structure of `$_COOKIE`. Typically,
     * this data will be injected at instantiation.
     * 
     * This method MUST NOT update the related Cookie header of the request
     * instance, nor related values in the server parameters.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated cookie values.
     * 
     * @param       array               $cookies
     * 
     * Array of key/value pairs representing cookies.
     * 
     * @return      static
     */
    public function withCookieParams(array $cookies): static
    {
        return $this;    
    }


    /**
     * withUploadedFiles
     * 
     * Create a new instance with the specified uploaded files.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     * 
     * @param       array               $uploadedFiles
     * 
     * An array tree of `UploadedFileInterface` instances.
     * 
     * @return      static
     * 
     * @throws      \InvalidArgumentException
     * 
     * If an invalid structure is provided.
     */
    public function withUploadedFiles(array $uploadedFiles): static
    {
        return $this;
    }


    /**
     * withAttribute
     * 
     * Return an instance with the specified derived request attribute.
     * 
     * This method allows setting a single derived request attribute as
     * described in `getAttributes`.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated attribute.
     * 
     * @see `getAttributes`
     * 
     * @param       string              $name
     * 
     * The attribute name.
     * 
     * @param       mixed               $value
     * 
     * The value of the attribute.
     * 
     * @return      static
     */
    public function withAttribute(string $name, mixed $value): static
    {
        return $this;
    }


    /**
     * withoutAttribute
     * 
     * Return an instance that removes the specified derived request attribute.
     * 
     * This method allows removing a single derived request attribute as
     * described in `getAttributes`.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the attribute.
     * 
     * @see `getAttributes`
     * 
     * @param       string              $name
     * 
     * The attribute name.
     * 
     * @return      static
     */
    public function withoutAttribute(string $name): static
    {
        return $this;
    }
}
