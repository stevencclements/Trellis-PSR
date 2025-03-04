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
    protected mixed $stream;

    /**
     * @param       string              $mode
     * 
     * Specifies read, write, and append permissions for the current `Stream`
     * instance.
     */
    protected string $mode;

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
     * 
     * @throws      \RuntimeException
     * 
     * If the specified resource is invalid or the stream could not be
     * opened.
     */
    public function __construct(
        string $mode,
        string $stream = 'php://temp'
    ) {
        $this->mode = $mode;
        $this->stream = fopen($stream, $mode);

        if ($this->stream === false) {
            throw new \RuntimeException("Unable to open a stream using the $stream resource.", 500);
        }

        $this->metadata = stream_get_meta_data($this->stream);
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
        if (!$this->isSeekable() || !$this->isReadable()) {
            return '';
        }

        try {
            $this->rewind();
            return stream_get_contents($this->stream);
        } catch (\RuntimeException) {
            return '';
        }
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
        return $this->metadata['seekable'] ?? false;
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
        $readableModes = ['r', 'r+', 'w+', 'a+', 'x+', 'c+'];

        return in_array(strtolower($this->mode), $readableModes, true);
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
        $writableModes = ['w', 'w+', 'r+', 'a', 'a+', 'x', 'x+', 'c', 'c+'];

        return in_array(strtolower($this->mode), $writableModes, true);
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
        $stats = fstat($this->stream);

        return $stats['size'] ?? null;
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
        if (!$this->isReadable()) {
            throw new \RuntimeException('Stream is not readable.');
        }

        $content = stream_get_contents($this->stream);

        if ($content === false) {
            throw new \RuntimeException('Failed to get contents from the stream.');
        }

        return $content;
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
        if ($key === null) {
            return $this->metadata;
        }

        return $this->metadata[$key] ?? null;
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
        $position = ftell($this->stream);

        if ($position === false) {
            throw new \RuntimeException('Unable to get the current position of the pointer.', 500);
        }

        return $position;
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
        return feof($this->stream);
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
        if (!$this->isSeekable() || fseek($this->stream, $offset, $whence) === -1) {
            throw new \RuntimeException('Failed to seek in the stream.');
        }
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
        if (!$this->isReadable() || $length <= 0) {
            throw new \RuntimeException('Stream is not readable or length is invalid.');
        }

        $data = fread($this->stream, $length);
        if ($data === false) {
            throw new \RuntimeException('Failed to read from the stream.');
        }

        return $data;
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
        if (!$this->isWritable()) {
            throw new \RuntimeException('Stream is not writable.');
        }

        $writeSize = fwrite($this->stream, $content);
        if ($writeSize === false) {
            throw new \RuntimeException('Failed to write to the stream.');
        }

        return $writeSize;
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
        $this->seek(0);
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
        $resource = $this->stream;
        $this->stream = null;
        return $resource;
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
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
    }
}
