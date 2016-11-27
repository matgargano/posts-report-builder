<?php

namespace Cafemedia\Ingest;

use Cafemedia\Convert\Converter;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;


/**
 * Class Upload handles uploaded content
 * @package Cafemedia\Ingest
 */
class Upload extends FileIngester
{


    const FALLBACK_ERROR_MESSAGE = 'There was an issue uploading the file, please try again.';

    private $request;

    private $converter;
    private $model;


    /**
     * Upload constructor.
     *
     * @param UploadedFile $file the file that have been uploaded
     * @param Request $request the request that it came from
     * @param Model $model the model the content should be using
     * @param array $allowedMimeTypes allowed/expected mime types
     * @param Converter $converter a converter abstract class to encourage polymorphism
     */
    public function __construct(
        UploadedFile $file,
        Request $request,
        Model $model,
        Array $allowedMimeTypes,
        Converter $converter /* can this be an optional property? will we ever not need to convert? */
    )
    {


        $this->file    = $file;
        $this->request = $request;

        $this->converter        = $converter;
        $this->allowedMimeTypes = $allowedMimeTypes;
        $this->model            = $model;

    }


    /**
     * We need to process data for this upload class, let's handle it here
     *
     * @param $datum
     *
     * @return mixed
     */
    protected function filter($datum)
    {

        // days of the week and  and dates not matching up, so lets strip off the days of the week
        $dateTimeStamp = strtotime(substr($datum['timestamp'], 4));

        //lets format the timestmap for mysql
        $mySQLFormattedTimeStamp = date( 'Y-m-d H:i:s', $dateTimeStamp );

        $datum['timestamp'] = $mySQLFormattedTimeStamp;

        return $datum;

    }

    /**
     * The ingestion action
     * @return array
     */
    public function ingest()
    {

        if ( ! $this->validate()) {
            return $this->returnObject;
        }

        $this->convertData();


        $exceptionMessages = [];
        foreach ($this->dataConverted as $datum) {

            //@todo check if the # of items matches up to the # of columns in table, check for wrong data, etc
            $datum = $this->filter($datum);
            try {

                call_user_func(array($this->model, 'insert'), $datum);


            } catch (QueryException $ex) {
                $exceptionMessages[] = $ex->getMessage();
                //@todo custom message?
            } catch (\Exception $ex) {
                $exceptionMessages[] = $ex->getMessage();
            }
        }
        if ($exceptionMessages) {
            $this->returnObject['messages'] = $exceptionMessages;

            return $this->returnObject;
        }
        $this->returnObject['success']  = true;
        $this->returnObject['messages'] = [self::SUCCESS_MESSAGE];

        return $this->returnObject;

    }

    /**
     * Simple check of the mime type
     * @todo add argument for fileMimeTypes and add this to the FileIngester class?
     * @return bool
     */
    protected function checkMimeType()
    {


        if (in_array($this->file->getMimeType(), $this->allowedMimeTypes)) {
            return true;
        }

        return false;

    }

    /**
     * Get the contents of the file otherwise return false
     * @todo should this throw an exception instead of returning false?
     *
     * @return bool|mixed
     */
    protected function getFileContents()
    {
        try {
            $contents = File::get($this->file);
        } catch (FileNotFoundException $exception) {
            return false;
        }

        return $contents;
    }

    /**
     * Convert the data using a polymorphic converter object
     * @todo is a converter absolutely necessary?
     */
    protected function convertData()
    {

        /** @var Converter $converter */
        $this->converter->setData($this->fileContents);
        $this->converter->convert();
        $this->dataConverted = $this->converter->getConvertedData();
    }


    /**
     * A simple interface which encapsulates all of the validation methods needed
     * @return bool
     */
    protected function validate()
    {
        if ( ! $this->checkMimeType()) {
            $this->returnObject['messages'] = [self::INVALID_MIME_TYPE_MESSAGE];

            return false;
        }

        $this->fileContents = $this->getFileContents();
        if ( ! $this->fileContents) {
            $this->returnObject['messages'] = [self::FALLBACK_ERROR_MESSAGE];

            return false;
        }

        return true;
    }
}