<?php

namespace Cafemedia\Http\Controllers;

use Cafemedia\Convert\CSV;
use Cafemedia\Ingest\Upload;
use Cafemedia\Post;
use Illuminate\Http\Request;

/**
 * Handle CSV files for import
 * Class CSVController
 * @package Cafemedia\Http\Controllers
 */
class CSVController extends Controller
{


    /**
     * Store the CSV and redirect to the homepage
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $file = $request->file('csv-file');
        if ($file) {

            $allowedMimeTypes = array(
                'text/csv',
                'text/plain'
            );
            $uploadHandler    = new Upload($file, $request, new Post(), $allowedMimeTypes, new CSV());
            $result           = $uploadHandler->ingest();
            $class            = $result['success'] ? 'success' : 'error';
            $messagesWrapped  = $this->splitMessages((array)$result['messages']);
        } else {
            $class           = 'error';
            $messagesWrapped =  $this->splitMessages(['Please select a file to upload']);
        }
        $request->session()->flash($class, $messagesWrapped);

        return redirect('/');


    }

    /**
     * Delete all posts that are loaded in the system
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function deleteAll(Request $request)
    {
        Post::truncate();
        $request->session()->flash('success', 'Successfully deleted all records');

        return redirect('/');

    }


    /**
     * Helper method to split an array into single paragraph tags to return to the user via flash messaging
     * @param array $messages
     *
     * @return string
     */
    private function splitMessages(Array $messages)
    {
        $output = '';
        foreach ($messages as $message) {
            $output .= sprintf('<p>%s</p>', $message);
        }

        return $output;

    }
}
