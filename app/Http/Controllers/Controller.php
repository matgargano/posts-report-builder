<?php

namespace Cafemedia\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

/**
 * The homepage controller
 * @todo abstract  the registration of different report types (json, csv vs. digest, complete) to own service providers
 * @package Cafemedia\Http\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {

        $context = [];

        $context['reportTypes'] = ['csv', 'json'];
        $context['postCount'] = DB::table('posts')->count();
        $context['reports'] = App::make('report')->getReports();

        return view('upload.main', $context);
    }
}
