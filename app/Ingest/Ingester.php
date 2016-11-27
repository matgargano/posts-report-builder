<?php

namespace Cafemedia\Ingest;

/**
 * * The base class for ingesting content
 * Class Ingester
 * @package Cafemedia\Ingest
 */
abstract class Ingester
{


    const SUCCESS_MESSAGE = 'Ingestion successful executed';

    protected $dataConverted;
    protected $returnObject = [
        'success'  => false,
        'messages' => null

    ];

    abstract public function ingest();
    abstract protected function convertData();


}