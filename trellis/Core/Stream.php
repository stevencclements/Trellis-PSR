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

use Psr\Http\Message\StreamInterface;

/**
 * Stream
 * 
 * Describes a data stream.
 * 
 * Typically, an instance will wrap a PHP stream; this interface provides a
 * wrapper around the most common operations, including serialization of the
 * entire stream to a string.
 */
class Stream implements StreamInterface
{
    /**
     * @param       mixed               $stream
     * 
     * Initializes an underlying resource, most commonly a file, to create a
     * PHP stream.
     */
    protected Stream $stream;

    /**
     * @param       string              $mode
     * 
     * Specifies read, write, and append permissions for the current `Stream`
     * instance.
     */
    protected string $mode;

    /**
     * @param       int                 $size
     * 
     * The size of the Stream in bytes if known.
     */
    protected int $size;

    /**
     * @param       array               $metadata
     * 
     * An associative array of metadata associated with the stream and its
     * underlying resources.
     */
    protected array $metadata;


    /**
     * Constructor
     * 
     * Create a new `Stream` instance with the specified (or default) class
     * properties.
     */
    public function __construct(
        mixed $stream,
        string $mode,
        int $size,
        array $metadata
    ) {
        
    }


    /**
     * __toString
     * 
     * Reads all data from the stream into a string, from the beginning to the
     * end.
     * 
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     * 
     * ————— ————— ————— ————— { !! WARNING !! } ————— ————— ————— —————
     * This method could attempt to load a large amount of data into memory.
     * 
     * This method MUST NOT raise an exception in order to conform with PHP
     * string casting operations.
     * 
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * 
     * @return      string              $stream
     */
    public function __toString(): string
    {
        return '';
    }


    /**
     * isSeekable
     * 
     * Returns whether or not the stream is seekable.
     * 
     * @return      bool
     */
    public function isSeekable(): bool
    {
        return false;
    }


    /**
     * isReadable
     * 
     * Returns whether or not the stream is readable.
     * 
     * @return      bool
     */
    public function isReadable(): bool
    {
        return false;
    }


    /**
     * isWritable
     * 
     * Returns whether or not the stream is writable.
     * 
     * @return      bool
     */
    public function isWritable(): bool
    {
        return false;
    }


    /**
     * getSize
     * 
     * Get the size of the stream if known.
     * 
     * @return      ?int                $size
     * 
     * Returns the size in bytes if known, or null if unknown.
     */
    public function getSize(): ?int
    {
        return null;
    }


    /**
     * getContents
     * 
     * Returns the remaining contents in a string.
     * 
     * @return      string              $contents
     * 
     * @throws      \RuntimeException
     * 
     * If unable to read the stream or if an error occurs while
     * reading.
     */
    public function getContents(): string
    {
        return '';
    }


    /**
     * getMetadata
     * 
     * Get stream metadata as an associative array or retrieve a
     * specific key.
     * 
     * The keys returned are identical to the keys returned from the
     * PHP `stream_get_meta_data` function.
     * 
     * @see http://php.net/manual/en/function.stream-get-meta-data.php
     * 
     * @param       string              $key
     * 
     * Specific metadata to retrieve.
     * 
     * @return      mixed               $metadata
     * 
     * Returns an associative array if no key is provided. Returns a
     * specific key value if a key is provided and the value is found,
     * or null if the key is not found.
     */
    public function getMetadata(?string $key = null): mixed
    {
        return null;
    }


    /**
     * tell
     * 
     * Returns the current position of the file read/write pointer.
     * 
     * @return      int                 $position
     * 
     * Position of the file pointer.
     * 
     * @throws      \RuntimeException
     * 
     * On error.
     */
    public function tell(): int
    {
        return 0;
    }


    /**
     * eof
     * 
     * Returns `true` if the stream is at the end of the stream.
     * 
     * @return      bool
     */
    public function eof(): bool
    {
        return false;
    }


    /**
     * seek
     * 
     * Seek to a position in the stream.
     * 
     * @see http://www.php.net/manual/en/function.fseek.php
     * 
     * @param       int                 $offset
     * 
     * Stream offset.
     * 
     * @param       int                 $whence
     * 
     * Specifies how the cursor position will be calculated based on the seek
     * offset. Valid values are identical to the built-in PHP $whence values
     * for `fseek`:
     * 
     *      -) `SEEK_SET`: Set position equal to offset bytes.
     *      -) `SEEK_CUR`: Set position to the current location plus the
     *          offset.
     *      -) `SEEK_END`: Set position to the end of the stream plus the
     *          offset.
     * 
     * @return      void
     * 
     * @throws      \RuntimeException
     * 
     * On failure.
     */
    public function seek(int $offset, int $whence = SEEK_SET): void
    {

    }


    /**
     * read
     * 
     * Read data from the stream.
     * 
     * @param       int                 $length
     * 
     * Read up to `$length` bytes from the object and return them. Fewer than
     * `$length` bytes may be returned if the underlying stream call returns
     * fewer bytes.
     * 
     * @return      string              $content
     * 
     * Returns data read from the stream, or an empty string if no bytes are
     * available.
     * 
     * @throws      \RuntimeException
     * 
     * If an errors occurs.
     */
    public function read(int $length): string
    {
        return '';
    }


    /**
     * write
     * 
     * Write data to the stream.
     * 
     * @param       string              $content
     * 
     * @return      int                 $writeSize
     * 
     * Returns the number of bytes written to the stream.
     * 
     * @throws      \RuntimeException
     * 
     * On failure.
     */
    public function write(string $content): int
    {
        return 0;
    }


    /**
     * rewind
     * 
     * Seek to the beginning of the stream.
     * 
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform `seek(0)`.
     * 
     * @see seek()
     * @see http://www.php.net/manual/en/function.fseek.php
     * 
     * @return      void
     * 
     * @throws      \RuntimeException
     * 
     * On failure.
     */
    public function rewind(): void
    {

    }


    /**
     * detach
     * 
     * Separates any underlying resources from the stream.
     * 
     * After the stream has been detached, the stream is in an unusable state.
     * 
     * @return      mixed               $resource
     * 
     * Underlying PHP stream, if any.
     */
    public function detach(): mixed
    {
        return null;
    }


    /**
     * close
     * 
     * Closes the stream and any underlying resources.
     * 
     * @return      void
     */
    public function close(): void
    {
        
    }
}
