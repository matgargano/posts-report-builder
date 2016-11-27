<?php

use Cafemedia\Post;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PageTest extends TestCase
{


    use DatabaseTransactions;


    public function setUp(){
        parent::setUp();


    }
    /**
     * Test without any posts
     *
     * @return void
     */
    public function testBasicPageLoad()
    {
        $this->visit('/')

            ->see('Dashboard')
            ->dontSee('Delete All Posts');

    }

    public function testBasicPageLoadWithPosts()
    {

        factory(Post::class, 2)->create();
        $this->visit('/')

             ->see('Dashboard')
             ->see('Delete All Posts');

    }



}
