<?php

namespace Cafemedia\Providers;

use Cafemedia\Report\NotTopPosts;
use Cafemedia\Report\TopPosts;
use Cafemedia\Report\Type;
use Illuminate\Support\ServiceProvider;

/**
 * This is where we can register and set all of our reports
 *
 * Class PostReportServiceProvider
 * @package Cafemedia\Providers
 */
class PostReportServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services. Register our reports.
     *
     * @return void
     */
    public function boot()
    {

        // register all of our reports


        $this->app['report']->addReportType(
            'Top Posts', 'topPosts', '\Cafemedia\Report\TopPosts::get', '\Cafemedia\Report\TopPosts::filter'
        );

        $this->app['report']->addReportType(
            'Not Top Posts', 'notTopPosts', '\Cafemedia\Report\NotTopPosts::get'
        );

        $this->app['report']->addReportType(
            'Daily Top Posts', 'dailyTopPosts', '\Cafemedia\Report\DailyTopPosts::get'
        );
    }

    /**
     * Register the Post Report application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('report', function () {
            return new Type();
        });
    }
}
