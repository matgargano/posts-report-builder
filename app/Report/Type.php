<?php

namespace Cafemedia\Report;


/**
 * A simple class to used with the PostReportServiceProvider that allows us to register our reports
 * @package Cafemedia\Report
 */
class Type
{


    private $reports = [];

    public function addReportType($label, $slug, callable $method, $filter = null)
    {


        if ($filter && ! is_callable($filter)) {
            throw new \Exception(sprintf('Filter method is not callable for report %s', $label));
        }
        $this->reports[$slug] = array(
            'label'    => $label,
            'callable' => $method
        );
        if ($filter) {
            $this->reports[$slug] = array_merge($this->reports[$slug], array('filter' => $filter));
        }

    }

    /**
     * Return the report object, used to verify that a report exists when a request comes in
     * @return array
     */
    public function getReports()
    {
        return $this->reports;
    }

}