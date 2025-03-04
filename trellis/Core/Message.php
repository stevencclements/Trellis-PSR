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

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Message
 * 
 * HTTP messages consist of requests from a client to a server and responses
 * from a server to a client. This interface defines the methods common to
 * each.
 * 
 * Messages are considered immutable; all methods that might change state
 * MUST be implemented in such a way as to retain the internal state of the
 * current Message and return an instance that contains the changed state.
 * 
 * @see http://www.ietf.org/rfc/rfc7230.txt
 * @see http://www.ietf.org/rfc/rfc7231.txt
 */
class Message implements MessageInterface {
    /**
     * @param       string              $protocolVersion
     * 
     * The HTTP protocol version number as a string, for example `1.1`, `2.0`,
     * and so on.
     */
    public string $protocolVersion;

    /** 
     * @param       array               $headers
     * 
     * An associative array representing the headers received as part of the
     * HTTP request.
     */
    public array $headers;
    
    /**
     * @param       Stream              $body
     * 
     * The content contained in the body of the HTTP message represented as an
     * instance of `Cts\Trellis\Core\Stream`. The `Stream` class initializes
     * an underlying resource, most commonly a file, to create a PHP stream.
     */
    public Stream $body;


    /**
     * Constructor
     * 
     * Create a new `Message` instance with the specified (or default) class
     * properties.
     */
    public function __construct(
        string $mode
    )
    {
        $versionNumber = explode('/', $_SERVER['SERVER_PROTOCOL']);

        $this->body = new Stream($mode);
        $this->headers = getallheaders();
        $this->protocolVersion = $versionNumber[1];
    }


    /**
     * hasHeader
     * 
     * Checks if a header exists by the given case-insensitive name.
     * 
     * @param       string              $headerName
     * 
     * Case-insensitive header field name.
     * 
     * @return      bool                $headerExists
     * 
     * Returns `true` if any header names match the given header name using a
     * case-insensitive string comparison. Returns `false` if no matching
     * header name is found in the message.
     */
    public function hasHeader(string $headerName): bool
    {
        $normalizedHeaderName = strtolower($headerName);

        foreach ($this->headers as $name => $values) {
            if (strtolower($name) === $normalizedHeaderName) {
                return true;
            }
        }

        return false;
    }


    /**
     * getProtocolVersion
     * 
     * Retrieves the HTTP protocol version as a string.
     * 
     * The string MUST contain only the HTTP version number (for example,
     * `1.1`, `2.0`, and so on).
     * 
     * @return      string              $protocolVersion
     * 
     * HTTP protocol version.
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }


    /**
     * getHeaders
     * 
     * Retrieves all message header values.
     * 
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     * 
     * Represent the headers as a string:
     * 
     *      foreach ($message->getHeaders() as $name => $values) {
     *          echo $name . ': ' . implode(', ', $values);
     *      }
     * 
     * Emit headers iteratively:
     * 
     *      foreach ($message->getHeaders() as $name->$values) {
     *          foreach ($values as $value) {
     *              header(sprintf('%s: %s', $name, $value), false);
     *          }
     *      }
     * 
     * While header names are case insensitive, `getHeaders` will preserve the
     * exact case in which headers were originally specified.
     * 
     * @return      array               $headers
     * 
     * Returns an associative array of message headers. Each key MUST be a
     * header name, and each value MUST be an array of strings for that header.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }


    /**
     * getHeader
     * 
     * Retrieves a message header value by the given case-insensitive name.
     * 
     * This method returns an array of all the header values of the given
     * case-insensitive header name.
     * 
     * If the header does not appear in the message, this method MUST return
     * an empty array.
     * 
     * @param       string              $headerName
     * 
     * Case-insensitive header field name.
     * 
     * @return      array               $headerValues
     * 
     * An array of string values as provided for the given header. If the
     * header does not appear in the message, this method MUST return an
     * empty array.
     */
    public function getHeader(string $headerName): array
    {
        $normalizedHeaderName = strtolower($headerName);

        foreach ($this->headers as $name => $values) {
            if (strtolower($name) === $normalizedHeaderName) {
                return $values;
            }
        }

        return [];
    }


    /**
     * getHeaderLine
     * 
     * Retrieves a comma-separated string of the values for a single header.
     * 
     * This method returns all of the header values of the given case-
     * insensitive header name as a string concatenated together using a
     * comma.
     * 
     * ————— ————— ————— ————— { !! NOTE !! } ————— ————— ————— —————
     * Not all header values may be appropriately represented using comma
     * concatenation. For such headers, use `getHeaders` instead and supply
     * your own delimiter when concatenating.
     * 
     * If the header does not appear in the message, this method MUST
     * return an empty string.
     * 
     * @param       string              $headerName
     * 
     * Case-insensitive header field name.
     * 
     * @return      string              $headerValues
     * 
     * A string of values as provided for the given header concatenated
     * together using a comma. If the header does not appear in the message,
     * this method MUST return an empty string.
     */
    public function getHeaderLine(string $headerName): string
    {
        $headerValues = $this->getHeader($headerName);

        if (empty($headerValues)) {
            return '';
        }

        return implode(', ', $headerValues);
    }


    /**
     * getBody
     * 
     * Gets the body of the message.
     * 
     * @return      Stream              $body
     * 
     * Returns the body as a `Stream` instance.
     */
    public function getBody(): Stream
    {
        return $this->body;
    }


    /**
     * withProtocolVersion
     * 
     * Return an instance with the specified HTTP protocol version.
     * 
     * The version string MUST contain only the HTTP version number (for
     * example, `1.1`, `2.0`, and so on).
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the Message, and MUST return an instance that has the
     * new protocol version.
     * 
     * @param       string              $protocolVersion
     * 
     * HTTP protocol version.
     * 
     * @return      static
     */
    public function withProtocolVersion(string $protocolVersion): static
    {
        $clone = clone $this;
        $clone->protocolVersion = $protocolVersion;

        return $clone;
    }


    /**
     * withHeaderLine
     * 
     * Return an instance with the provided value replacing the specified
     * header.
     * 
     * While header names are case-insensitive, the casing of headers will be
     * preserved by this function, and returned from `getHeaders`.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the Message, and must return an instance that has the
     * new and/or updated header and value.
     * 
     * @param       string              $headerName
     * 
     * Case-insensitive header field name.
     * 
     * @param       mixed               $headerValue(s)
     * 
     * Header value(s).
     * 
     * @return      static
     * 
     * @throws      \InvalidArgumentException
     * 
     * For invalid header names or values.
     */
    public function withHeader(string $headerName, mixed $headerValues): static
    {
        $clone = clone $this;

        $normalizedHeaderName = strtolower($headerName);
        $headerValues = is_array($headerValues) ? $headerValues : [$headerValues];
        
        $clone->headers[$normalizedHeaderName] = $headerValues;

        return $clone;
    }


    /**
     * withAddedHeader
     * 
     * Return an instance with the specified header appended with the given
     * value.
     * 
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the Message, and MUST return an instance that has the
     * new header and/or value.
     * 
     * @param       string              $headerName
     * 
     * Case-insensitive header field name to add.
     * 
     * @param       mixed               $headerValue(s)
     * 
     * Header value(s).
     * 
     * @return      static
     * 
     * @throws      \InvalidArgumentException
     * 
     * For invalid header names or values.
     */
    public function withAddedHeader(string $headerName, mixed $headerValues): static
    {
        $clone = clone $this;
        $normalizedHeaderName = strtolower($headerName);
        $headerValues = is_array($headerValues) ? $headerValues : [$headerValues];

        if (isset($clone->headers[$normalizedHeaderName])) {
            $clone->headers[$normalizedHeaderName] = array_merge($clone->headers[$normalizedHeaderName], $headerValues);
        } else {
            $clone->headers[$normalizedHeaderName] = $headerValues;
        }

        return $clone;
    }


    /**
     * withBody
     * 
     * Return an instance with the specified message body.
     * 
     * The body MUST be a `Stream` object.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the Message, and MUST return a new instance that has
     * the new body stream.
     * 
     * @param       Stream              $body
     * 
     * Body.
     * 
     * @return      static
     * 
     * @throws      \InvalidArgumentException
     * 
     * When the body is invalid.
     */
    public function withBody(StreamInterface $body): static
    {
        $clone = clone $this;

        if (!$body instanceof Stream) {
            throw new \InvalidArgumentException('Body must be an instance of StreamInterface.');
        }

        $clone->body = $body;
        return $clone;
    }


    /**
     * withoutHeader
     * 
     * Return an instance without the specified header.
     * 
     * Header resolution MUST be done case-insensitively.
     * 
     * This method MUST be implemented in such a way as to retain the
     * immutability of the Message, and must return an instance that removes
     * the named header.
     * 
     * @param       string              $headerName
     * 
     * Case-insensitive header field name to remove.
     * 
     * @return      static
     */
    public function withoutHeader(string $headerName): static
    {
        $clone = clone $this;
        $normalizedHeaderName = strtolower($headerName);

        if (isset($clone->headers[$normalizedHeaderName])) {
            unset($clone->headers[$normalizedHeaderName]);
        }

        return $clone;
    }
}
