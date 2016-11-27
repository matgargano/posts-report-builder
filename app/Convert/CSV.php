<?php

namespace Cafemedia\Convert;

use League\Csv\Reader;

/**
 * Wrap CSV ingestion in a class that extends our base Converter abstract class
 * @package Cafemedia\Convert
 */
class CSV extends Converter
{

    public function convert()
    {
        $reader = Reader::createFromString($this->data);
        $offset = 0;
        $this->convertedData= $reader->fetchAssoc($offset);
        return true;
    }

    public function getConvertedData() {
        return $this->convertedData;
    }


}