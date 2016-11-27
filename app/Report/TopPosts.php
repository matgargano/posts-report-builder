<?php

namespace Cafemedia\Report;

use Illuminate\Support\Facades\DB;

/**
 * Top posts query:
 *
 * Rules:
 * The post must be public
 * The post must have over 10 comments and over 9000 views
 * The post title must be under 40 characters
 * Class TopPosts
 *
 * @package Cafemedia\Report
 */
class TopPosts implements Report
{

    protected static $complexity;

    /**
     * Get the reports
     * @todo abstract out the types and complexities
     * @param string $complexity
     *
     * @return mixed
     */
    public static function get($complexity = 'digest')
    {

        $select = ['title', 'id'];

        self::$complexity = $complexity;
        if ('complete' === self::$complexity) {
            $select = '*';
        }

        return DB::table('posts')
                 ->select($select)
                 ->where([
                     ['privacy', '=', 'public'],
                     ['comments', '>', 10],
                     ['views', '>', 9000],

                 ])
                 ->groupBy('id')
                 ->havingRaw('length(title) < 40');


    }

    /**
     * We need the title to select based on the title, however the digest (simple) report should not contain this
     * So let's strip it out
     *
     * @param $item
     *
     * @return mixed
     */
    public static function filter($item)
    {

        if ('digest' === self::$complexity) {
            unset($item->title);
        }

        return $item;
    }


}