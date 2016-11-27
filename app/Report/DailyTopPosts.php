<?php

namespace Cafemedia\Report;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;


/**
 * Since this is a derived report (uses calculations) there is no different complexity level for this
 *
 * This report returns the top posts by day
 *
 * Class DailyTopPosts
 * @package Cafemedia\Report
 */
class DailyTopPosts implements Report
{


    public static function get()
    {


        return DB::select('SELECT a.* FROM posts a INNER JOIN ( SELECT DATE(timestamp) date, MAX(likes) max_likes FROM posts GROUP BY DATE(timestamp)) b ON DATE(a.timestamp) = b.date AND a.likes = b.max_likes');


    }

}