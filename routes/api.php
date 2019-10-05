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
    # user/auth
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    # experiments
    Route::get(     '/experiment/{id}/start',               'ExperimentsController@start');
    Route::get(     '/experiment/{id}/observer-metas',      'ExperimentsController@observer_metas');
    Route::get(     '/experiments',                         'ExperimentsController@index');
    Route::get(     '/experiments/all',                     'ExperimentsController@all');
    Route::get(     '/experiments/public',                  'ExperimentsController@all_public');
    Route::get(     '/experiment/{id}',                     'ExperimentsController@find');
    Route::post(    '/experiment/store',                    'ExperimentsController@store');
    Route::patch(   '/experiment/{experiment}/visibility',  'ExperimentsController@visibility');
    Route::patch(   '/experiment/{experiment}',             'ExperimentsController@update');
    Route::delete(  '/experiment/{experiment}',             'ExperimentsController@destroy');

    # picture sets
    Route::get(     '/picture-sets/original/{id}',  'PictureSetsController@original');
    Route::post(    '/imageSet',                    'PictureSetsController@store');
    Route::get(     '/image-sets',                  'PictureSetsController@index');
    Route::get(     '/picture-sets',                'PictureSetsController@all');
    Route::get(     '/picture-set/images/{id}',     'PictureSetsController@pictures');
    Route::delete(  '/picture-set/{id}',            'PictureSetsController@destroy');

    # pictures
    Route::delete(  '/picture/{id}', 'PicturesController@destroy');
    Route::get(     '/picture/{id}', 'PicturesController@index');

    # experiment results
    Route::get(     '/experiment/{id}/experiment-results',  'ExperimentResultsController@index');
    Route::get(     '/experiment-result/{id}',              'ExperimentResultsController@fetch');
    Route::post(    '/experiment-result/create',            'ExperimentResultsController@store');
    Route::delete(  '/experiment-result/{id}/wipe',         'ExperimentResultsController@destroy');

    # paired results
    Route::get( '/paired-result/{id}',          'PairedResultsController@index');
    // Route::get( '/paired-result/{id}/export',   'PairedResultsController@index_export');
    // Route::get( '/paired-result/export',        'PairedResultsController@export');
    Route::post('/paired-result',               'PairedResultsController@store');

    # instructions
    Route::get('/instructions', 'InstructionsController@index');

    # observer metas
    Route::get('/observer-metas', 'ObserverMetasController@index');

    # experiment observer metas
    Route::post('/experiment-observer-meta-result', 'ExperimentObserverMetaResultsController@store');

    # categories
    Route::get('/categories', 'CategoriesController@index');

    # experiment types
    Route::get('/experiment-types', 'ExperimentTypesController@all');

    # scientist requests
    Route::get( '/scientist-request',             'ScientistRequestsController@index' );
    Route::post('/scientist-request/{id}/accept', 'ScientistRequestsController@accept');
    Route::post('/scientist-request/{id}/reject', 'ScientistRequestsController@reject');

    # misc
    Route::post('/logout', 'AuthController@logout');
});

// TODO: move this inside... InvalidArgumentException: Route [login] not defined
Route::post('/file', 'PicturesController@store');

Route::get(    '/paired-result/{id}/export',     'PairedResultsController@export_observer');
Route::get(    '/{id}/paired-result/all/export', 'PairedResultsController@export_all');

Route::post('/register',    'AuthController@register');
Route::post('/anonymous',   'AuthController@anonymous');
Route::post('/login',       'AuthController@login');
