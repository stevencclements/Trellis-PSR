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
     * @param       string              $content
     * 
     * The content returned by the server in response to an attempt to
     * satisfy the request.
     */
    protected string $content;

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
     * The error message returned by the server in response to an attempt to
     * satisfy the request.
     */
    protected string $reasonPhrase;


    /**
     * Constructor
     * 
     * Create a new `Response` instance with the specified (or default) class
     * properties.
     */
    public function __construct(
        string $content,
        int $statusCode = 200,
        string $reasonPhrase = ''
    ) {
        parent::__construct('w+');

        $this->statusCode = $statusCode;
        $this->reasonPhrase = $reasonPhrase;

        $this->body->write($content);
        $this->body->rewind();
    }


    /**
     * render
     * 
     * Render content from the stream body to the client.
     * 
     * @return      void
     */
    public function render()
    {
        http_response_code($this->statusCode);

        echo $this->body->getContents();
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
        return $this->statusCode;
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
        if ($this->reasonPhrase !== '') {
            return $this->reasonPhrase;
        }

        // Default reason phrases based on RFC 7231/IANA
        $defaultPhrases = [
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            502 => 'Bad Gateway',
        ];

        return $defaultPhrases[$this->statusCode] ?? '';
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
    public function withStatus(int $statusCode, string $reasonPhrase = ''): static
    {
        if ($statusCode < 100 || $statusCode > 599) {
            throw new \InvalidArgumentException('Invalid HTTP status code.');
        }

        $clone = clone $this;
        $clone->statusCode = $statusCode;
        $clone->reasonPhrase = $reasonPhrase;

        return $clone;
    }
}
