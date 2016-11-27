<?php

namespace Cafemedia\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use League\Csv\Writer;

/**
 * Handles the downloading of reports
 * @todo abstract out the report types (CSV, JSON, future XML, YAML?) to their own dedicated methods
 * @todo abstract most of this logic and functionality into its own class, which we can call and implement in the future
 * @package Cafemedia\Http\Controllers
 */
class DownloadController extends Controller
{

    protected $reportType;
    protected $reportComplexity;

    public function show($reportType, Request $request, $reportFormat = 'csv', $reportComplexity = 'digest')
    {
        $this->reportType       = $reportType;
        $this->reportComplexity = $reportComplexity;
        $posts                  = $this->preparePosts();
        if ( ! $posts) {
            $request->session()->flash('error', 'Invalid Report');

            return redirect('/');
        }

        // we could break these (json and csv) out to their own inner methods
        if ('csv' === $reportFormat) {
            $csv = Writer::createFromFileObject(new \SplTempFileObject());

            $counter = 0;


            foreach ($posts as $post) {

                if ($counter === 0) {
                    $keys = array_keys((array)$post);
                    $csv->insertOne($keys);

                }

                $csv->insertOne((array)$post);
                $counter++;
            }

            return response()->make(rtrim($csv, "\n"))
                             ->header('Content-disposition',
                                 'attachment;filename="' . $this->reportType . '-' . time() . '.csv')
                             ->header('Content-Type', 'text/csv');


        } elseif ('json' === $reportFormat) {
            return response()->json($posts)
                             ->header('Content-disposition',
                                 'attachment;filename="' . $this->reportType . '-' . time() . '.json')
                             ->header('Content-Type', 'application/json');
        }

        // we've gotten this far, but nothing else can be done

        $request->session()->flash('error',
            sprintf('Invalid Report Type Request "%s"', htmlentities($reportFormat, ENT_QUOTES, 'UTF-8', false)));

        return redirect('/');


    }

    /**
     * Grab and prepare the posts for this report
     * @return bool|mixed
     */
    protected function preparePosts()
    {

        $posts = false;

        // lets get the service provider for the reports
        $reports      = App::make('report')->getReports();
        $reports_keys = array_keys($reports);

        // we've registered all of the reports in the PostReportServiceProvider::boot method
        // if this report is recognized it will use that report element's method that implements the
        // \Cafemedia\Report\Report interface

        if (in_array($this->reportType, $reports_keys)) {

            $method = $reports[$this->reportType]['callable'];


            // there are no buttons yet, but we can, using the download route, customize the complexity of the reports
            // bonus point #2

            $posts  = call_user_func($method, $this->reportComplexity);


            // if we are returned a query builder, let's get the posts, otherwise there was a raw query ran
            // so we have the results we need
            if (is_a($posts, \Illuminate\Database\Query\Builder::class)) {
                $posts = $posts->get();
            }

            if (array_key_exists('filter', $reports[$this->reportType])) {

                $posts = $posts->map($reports[$this->reportType]['filter']);

            }

        }

        return $posts;


    }


}
