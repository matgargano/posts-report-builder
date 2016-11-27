<?php

namespace Cafemedia\Report;

/**
 * All reports should implement this interface
 * All reports must reigster in the PostReportServiceProvider::boot() method
 *
 * Interface Report
 * @package Cafemedia\Report
 */
interface Report {

    public static function get();
}