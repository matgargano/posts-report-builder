<?php

namespace Cafemedia\Report;

use Illuminate\Support\Facades\DB;

/**
 * Posts that do not meet the top posts critera
 * @todo dervive this class in the future?
 * Class NotTopPosts
 * @package Cafemedia\Report
 */
class NotTopPosts implements Report
{


    protected static $complexity;

    public static function get($complexity = 'digest')
    {

        $select = ['id'];

        self::$complexity = $complexity;
        if ('complete' === self::$complexity) {
            $select = '*';
        }

        return DB::table('posts')
                 ->select($select)
                 ->where('privacy', '!=', 'public')
                 ->orWhere('comments', '<=', 10)
                 ->orWhere('views', '<=', 9000)
                 ->orWhereRaw('length(title) >= 40');

    }
}