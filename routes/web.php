<?php

Route::get('/', 'Controller@index');

Route::resource('csv', 'CSVController', ['only' => ['store', 'delete']]);

/** "nonstandard" route to clear db */
Route::delete('/delete', 'CSVController@deleteAll')->name('csv.delete.all');


/** route for the reports */
Route::get('/download/{reportType}/{reportFormat?}/{complexity?}', 'DownloadController@show')->name('download');
