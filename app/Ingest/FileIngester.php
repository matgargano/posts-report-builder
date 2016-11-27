<?php
namespace Cafemedia\Ingest;


/**
 * The base class for ingesting content from a file
 * Class FileIngester
 * @package Cafemedia\Ingest
 */
abstract class FileIngester extends Ingester
{


    const INVALID_MIME_TYPE_MESSAGE = 'Invalid file type, please upload a file with the proper mime type.';

    protected $allowedMimeTypes;
    protected $fileContents;
    protected $file;

    abstract protected function checkMimeType();

    abstract protected function getFileContents();




}