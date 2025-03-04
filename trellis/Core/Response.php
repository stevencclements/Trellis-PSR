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

use Psr\Http\Message\ResponseInterface;

/**
 * Response
 * 
 * Representation of an outgoing, server-side response.
 * 
 * Per the HTTP specification, this interface includes properties for each of
 * the following:
 * 
 *      - Protocol version
 *      - Status code
 *      - Reason phrase
 *      - Headers
 *      - Message body
 * 
 * Responses are considered immutable. All methods that might change state
 * MUST be implemented such that they retain the internal state of the
 * current message and return an instance that contains the changed state.
 */
class Response extends Message implements ResponseInterface
{
    /**
     * @param       int                 $statusCode
     * 
     * Represents the status code returned by the server in response to an
     * attempt to satisfy the request.
     */
    protected int $statusCode;

    /**
     * @param       string              $reasonPhrase
     * 
     * The content or error message returned by the server in response to an
     * attempt to satisfy the request.
     */
    protected string $reasonPhrase;


    /**
     * Constructor
     * 
     * Create a new `Response` instance with the specified (or default) class
     * properties.
     */
    public function __construct(
        int $statusCode,
        string $reasonPhrase
    ) {
        
    }


    /**
     * getStatusCode
     * 
     * Gets the response status code.
     * 
     * The status code is a three digit integer result code of the server's
     * attempt to understand and satisfy the request.
     * 
     * @return      int                 $statusCode
     */
    public function getStatusCode(): int
    {
        return 0;
    }


    /**
     * getReasonPhrase
     * 
     * Gets the response reason phrase associated with the status code.
     * 
     * Because a reason phrase is not a required element in a response status
     * line, the reason phrase value MAY be empty. Implementations MAY choose
     * to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's status
     * code.
     * 
     * @see http://tools.ietf.org/html/rfc7231#section-6
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * 
     * @return      string              $reasonPhrase
     * 
     * Must return an empty string if no reason phrase exists.
     */
    public function getReasonPhrase(): string
    {
        return '';
    }


    /**
     * withStatusCode
     * 
     * Return an instance with the specified status code and, optionally,
     * reason phrase.
     * 
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated status and reason phrase.
     * 
     * @see http://tools.ietf.org/html/rfc7231#section-6
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * 
     * @param       int                 $statusCode
     * 
     * The three digit integer result code to set.
     * 
     * @param       string              $reasonPhrase
     * 
     * The reason phrase to use with the provided status code; if none is
     * provided, implementations MAY use the defaults as suggested in the HTTP
     * specification.
     * 
     * @return      static
     * 
     * @throws      \InvalidArgumentException
     * 
     * For invalid status code arguments.
     */
    public function withStatus(int $code, string $reasonPhrase = ''): static
    {
        return $this;
    }
}
