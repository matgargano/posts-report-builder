<?php

namespace Cafemedia\Convert;

/**
 * Abstract class that exposes the ability to set, convert and return converted data
 * @package Cafemedia\Convert
 */
abstract class Converter
{
    protected $data;
    protected $convertedData;

    public function setData($data)
    {

        $this->data = $data;
    }

    abstract public function convert();

    abstract public function getConvertedData();
}