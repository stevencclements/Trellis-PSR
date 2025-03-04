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

use Psr\Http\Message\UploadedFileInterface;

/**
 * UploadedFile
 * 
 * Value object representing a file uploaded through an HTTP request.
 * 
 * Instances of this interface are considered immutable; all methods that might
 * change state MUST be implemented such that they retain the internal state of
 * the current instance and return an instance that contains the changed state.
 */
class UploadedFile implements UploadedFileInterface
{
    /**
     * @param       Stream              $fileStream
     * 
     * An instance of `Stream`, representing the uploaded file.
     */
    protected Stream $fileStream;

    /**
     * @param       ?int                $size
     * 
     * The size of the file in bytes if known, or null if unknown.
     */
    protected int $size;

    /**
     * @param       int                 $error
     * 
     * One of the PHP `UPLOAD_ERR_XXX` constants.
     */
    protected int $error;

    /**
     * @param      ?string              $fileName
     * 
     * The filename sent by the client.
     */
    protected string $fileName;

    /**
     * @param      ?string              $mediaType
     * 
     * The file media type sent by the client.
     */
    protected string $mediaType;


    /**
     * Constructor
     * 
     * Create a new `ServerRequest` instance with the specified (or default)
     * class properties.
     */
    public function __construct(
        Stream $fileStream,
        int $size,
        int $error,
        string $fileName,
        string $mediaType
    ) {
        if ($error !== UPLOAD_ERR_OK && $error !== UPLOAD_ERR_NO_FILE) {
            throw new \InvalidArgumentException("Invalid error code: {$error}");
        }
    }


    /**
     * getStream
     * 
     * Retrieve a stream representing the uploaded file.
     * 
     * This method MUST return a `StreamInterface` instance, representing the
     * uploaded file. The purpose of this method is to allow utilizing native
     * PHP stream functionality to manipulate the file upload, such as
     * `stream_copy_to_stream` (though the result will need to be decorated in
     * a native PHP stream wrapper to work with such functions).
     * 
     * If the `moveTo` method has been called previously, this method MUST
     * raise an exception.
     * 
     * @return      Stream              $stream
     * 
     * Stream representation of the uploaded file.
     * 
     * @throws      \RuntimeException
     * 
     * In cases where no stream is available or no stream can be created.
     */
    public function getStream(): Stream
    {
        if ($this->error !== UPLOAD_ERR_OK) {
            throw new \RuntimeException("Cannot retrieve stream due to error: {$this->error}");
        }

        return $this->fileStream;
    }


    /**
     * getSize
     * 
     * Retrieve the file size.
     * 
     * Implementations SHOULD return the value stored in the `size` key of the
     * file in the `$_FILES` array if available, as PHP calculates this based
     * on the actual size transmitted.
     * 
     * @return      ?int                $size
     * 
     * The file size in bytes or null if unknown.
     */
    public function getSize(): ?int
    {
        return $this->size;
    }


    /**
     * getError
     * 
     * Retrieve the error associated with the uploaded file.
     * 
     * The return value MUST be one of the PHP `UPLOAD_ERR_XXX` constants.
     * 
     * If the file was previously uploaded successfully, this method MUST
     * return `UPLOAD_ERR_OK`.
     * 
     * Implementations SHOULD return the value stored in the `error` key of the
     * file in the `$_FILES` array.
     * 
     * @see http://php.net/manual/en/features.file-upload.errors.php
     * 
     * @return      int                 $error
     * 
     * One of the PHP `UPLOAD_ERR_XXX` constants.
     */
    public function getError(): int
    {
        return $this->error;
    }

    
    /**
     * getClientFilename
     * 
     * Retrieve the filename sent by the client.
     * 
     * Do not trust the value returned by this method. A client could send a
     * malicious filename with the intention to corrupt or hack your
     * application.
     * 
     * Implementations SHOULD return the value stored in the `name` key of the
     * file in the `$_FILES` array.
     * 
     * @return      ?string             $filename
     * 
     * The filename sent by the client or null if none was provided.
     */
    public function getClientFilename(): ?string
    {
        return $this->fileName;
    }


    /**
     * getClientMediaType
     * 
     * Retrieve the media type sent by the client.
     * 
     * Do not trust the value returned by this method. A client could send a
     * malicious media type with the intention to corrupt or hack your
     * application.
     * 
     * Implementations SHOULD return the value stored in the `type` key of the
     * file in the `$_FILES` array.
     * 
     * @return      ?string             $mediaType
     * 
     * The media type sent by the client or null if none was provided.
     */
    public function getClientMediaType(): ?string
    {
        return $this->mediaType;
    }


    /**
     * moveTo
     * 
     * Use this method as an alternative to `move_uploaded_file`. This method
     * is guaranteed to work in both SAPI and non-SAPI environments.
     * Implementations must determine which environment they are in, and use
     * the appropriate method (`move_uploaded_file`, `rename`, or a stream
     * operation) to perform the operation.
     * 
     * `$targetPath` may be an absolute path or a relative path. If it is a
     * relative path, resolution should be the same as the PHP `rename`
     * function.
     * 
     * The original file or stream MUST be removed upon completion.
     * 
     * If this method is called more than once, any subsequent calls MUST raise
     * an exception.
     * 
     * When used in an SAPI environment where `$_FILES` is populated, when
     * writing files with `moveTo`, `is_uploaded_file`, and `move_uploaded_
     * file` SHOULD be used to ensure permissions and upload status are
     * verified correctly.
     * 
     * If you wish to move to a stream, use `getStream`, as SAPI operations
     * cannot guarantee writing to stream destinations.
     * 
     * @see http://php.net/is_uploaded_file
     * @see http://php.net/move_uploaded_file
     * 
     * @param       string              $targetPath
     * 
     * The path to which the uploaded file should be moved.
     * 
     * @throws      \InvalidArgumentException
     * 
     * If the `$targetPath` specified is invalid.
     * 
     * @throws      \RuntimeException
     * 
     * On the second or subsequent call call to the method.
     */
    public function moveTo(string $targetPath): void
    {
        if (empty($targetPath)) {
            throw new \InvalidArgumentException("Target path must not be empty");
        }

        if (!is_writable(dirname($targetPath))) {
            throw new \RuntimeException("The target directory is not writable: {$targetPath}");
        }

        $targetPath = realpath($targetPath) ?: $targetPath;

        if (is_uploaded_file($this->fileStream->getMetadata('uri'))) {
            if (!move_uploaded_file($this->fileStream->getMetadata('uri'), $targetPath)) {
                throw new \RuntimeException("Failed to move uploaded file to target path: {$targetPath}");
            }
        } else {
            $source = $this->fileStream;
            $destination = fopen($targetPath, 'wb');
            if ($destination === false) {
                throw new \RuntimeException("Failed to open target file for writing: {$targetPath}");
            }
            stream_copy_to_stream($source, $destination);
            fclose($destination);
        }
    }
/*—— ———— ———— ———— ———— ———— ———— ———— ———— ———— ———— ———— ———— ———— ———— ————|———— ———— ———— ———— ———— ———— ———— ————| */
}
