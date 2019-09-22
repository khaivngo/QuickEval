<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

# auth:api middleware gives access to user object
Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/experiments', 'ExperimentsController@index');
    Route::get('/experiments/all', 'ExperimentsController@all');
    Route::get('/experiments/public', 'ExperimentsController@all_public');
    Route::get('/experiment/{id}', 'ExperimentsController@find');
    Route::post('/experiment/store', 'ExperimentsController@store');
    Route::patch('/experiment/{experiment}', 'ExperimentsController@update');
    Route::patch('/experiment/{experiment}/visibility', 'ExperimentsController@visibility');
    Route::delete('/experiment/{experiment}', 'ExperimentsController@destroy');

    Route::post('/imageSet', 'PictureSetsController@store');
    Route::get('/image-sets', 'PictureSetsController@index');
    Route::get('/picture-sets', 'PictureSetsController@all');
    Route::get('/picture-set/images/{id}', 'PictureSetsController@pictures');
    Route::delete('/picture-set/{id}', 'PictureSetsController@destroy');

    Route::post('/experiment-observer-meta-result', 'ExperimentObserverMetaResultsController@store');
    Route::get('/experiment/{id}/observer-metas', 'ExperimentsController@observer_metas');

    Route::get('/experiment/{id}/experiment-results', 'ExperimentResultsController@index');

    Route::post('/experiment-result', 'ExperimentResultsController@store');
    Route::get('/experiment-result/{id}', 'ExperimentResultsController@fetch');

    Route::get('/results/{id}', 'ResultsController@index');


    Route::get('/picture-sets/original/{id}', 'PictureSetsController@original');
    Route::delete('/picture/{id}', 'PicturesController@destroy');

    Route::get('/instructions', 'InstructionsController@index');
    Route::get('/observer-metas', 'ObserverMetasController@index');

    Route::get('/picture/{id}', 'PicturesController@index');

    Route::get('/categories', 'CategoriesController@index');
    Route::get('/experiment-types', 'ExperimentTypesController@all');

    Route::get('/experiment/{id}/start', 'ExperimentsController@start');
    Route::post('/result', 'ResultsController@store');

    Route::post('/logout', 'AuthController@logout');

    Route::get('/scientist-request', 'ScientistRequestsController@index');
    Route::post('/scientist-request/{id}/accept', 'ScientistRequestsController@accept');
    Route::post('/scientist-request/{id}/reject', 'ScientistRequestsController@reject');
});

Route::post('/file', 'PicturesController@store');

Route::post('/register', 'AuthController@register');
Route::post('/anonymous', 'AuthController@anonymous');
Route::post('/login', 'AuthController@login');
