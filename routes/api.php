<?php

use Illuminate\Http\Request;


// Route::get('mailable', function () {

//     // $experiment_results = \App\ExperimentResult::find(8);
//     $observer_metas = \App\ExperimentResult::find(8)->observer_metas;


//     // $user = \App\User::find(1);

//     // return new \App\Mail\ScientistRequest($user);
//     // // return new \App\Mail\Receipt($user);
// });

Route::post('/paired-result/export',     'ResultPairsController@export'   );
Route::post('/triplet-result/export',    'ResultTripletsController@export'  );
Route::post('/rank-order-result/export', 'ResultRankOrdersController@export');
Route::post('/category-result/export',   'ResultCategoriesController@export' );

# observer metas export
Route::get( '/experiment-observer-meta-result/{experiment_id}/{user_id}/export','ExperimentObserverMetaResultsController@export_observer');
Route::get( '/experiment-observer-meta-result/{experiment_id}/export',          'ExperimentObserverMetaResultsController@export_all');

Route::get( '/experiment-observer-meta-result/find-or-fail/{experiment_id}', 'ExperimentObserverMetaResultsController@find_or_fail');


Route::post('/register',    'AuthController@register' );
Route::post('/anonymous',   'AuthController@anonymous');
Route::post('/login',       'AuthController@login'    );

# auth:api middleware gives access to user object
Route::middleware('auth:api')->group(function () {
    # user
    Route::get(  '/user', function (Request $request) { return $request->user(); });
    Route::patch('/user', 'UserController@update');

    # experiments
    Route::get(     '/experiment/{experiment}/start',       'ExperimentsController@start'           );
    Route::get(     '/experiment/{id}/observer-metas',      'ExperimentsController@observer_metas'  );
    Route::get(     '/experiments',                         'ExperimentsController@index'           );
    Route::get(     '/experiments/all',                     'ExperimentsController@all'             );
    Route::get(     '/experiments/public',                  'ExperimentsController@all_public'      );
    Route::get(     '/experiment/{id}',                     'ExperimentsController@find'            );
    Route::get(     '/experiment/{id}/owner',               'ExperimentsController@is_owner'        );
    Route::get(     '/experiment/{id}/public',              'ExperimentsController@find_public'     );
    Route::post(    '/experiment/store',                    'ExperimentsController@store'           );
    Route::patch(   '/experiment/{experiment}/visibility',  'ExperimentsController@visibility'      );
    
    Route::post(    '/experiment/{original_experiment}/update', 'ExperimentsController@update'      ); // we do not use patch there because we want to create a complete new experiment, and tag the old one with "version 1"
    Route::delete(  '/experiment/{experiment}',             'ExperimentsController@destroy'         );

    Route::get(     '/experiment/{term}/search/public',     'ExperimentsController@search'     );

    # picture sets
    Route::get(     '/picture-sets/original/{id}',  'PictureSetsController@original'    );
    Route::post(    '/imageSet',                    'PictureSetsController@store'       );
    Route::get(     '/image-sets',                  'PictureSetsController@index'       );
    Route::get(     '/picture-sets',                'PictureSetsController@index'       );
    Route::get(     '/picture-set/{id}',            'PictureSetsController@find'        );
    Route::delete(  '/picture-set/{id}',            'PictureSetsController@destroy'     );

    # pictures
    Route::delete(  '/picture/{id}', 'PicturesController@destroy');
    Route::get(     '/picture/{id}', 'PicturesController@index'  );

    # experiment results
    Route::get(     '/experiment/{id}/experiment-results',  'ExperimentResultsController@index'     );
    Route::get(     '/experiment-result/{id}',              'ExperimentResultsController@fetch'     );
    Route::post(    '/experiment-result/create',            'ExperimentResultsController@store'     );
    Route::patch(   '/experiment-result/{result}/completed','ExperimentResultsController@completed' );
    Route::delete(  '/experiment-result/{id}/wipe',         'ExperimentResultsController@destroy'   );

    # paired results
    Route::get( '/paired-result/{id}',            'ResultPairsController@index'      );
    Route::get( '/paired-result/{id}/statistics', 'ResultPairsController@statistics' );

    Route::post('/paired-result',     'ResultPairsController@store'  );
    Route::post('/category-result',   'ResultCategoriesController@store');
    Route::post('/triplet-result',    'ResultTripletsController@store' );
    Route::post('/rank-order-result', 'ResultRankOrdersController@store' );
    Route::get( '/rank-order-result/{id}/statistics', 'ResultRankOrdersController@statistics' );

    # instructions
    Route::get('/instructions', 'InstructionsController@index');

    # observer metas
    Route::get('/observer-metas', 'ObserverMetasController@index');

    # experiment sequences
    Route::get('/experiment-sequences/{id}', 'ExperimentSequencesController@index');

    # experiment observer metas
    Route::get('/experiment-observer-metas/{id}', 'ExperimentObserverMetasController@index');

    # experiment categories
    Route::get('/experiment/{experiment}/categories', 'ExperimentCategoriesController@index');
    // Route::get('/experiment-categories/{id}', 'ExperimentCategoriesController@index');

    # experiment observer meta results
    Route::post('/experiment-observer-meta-result',                          'ExperimentObserverMetaResultsController@store');
    Route::get( '/experiment-observer-meta-result/{experiment_id}/{user_id}','ExperimentObserverMetaResultsController@index');
    Route::get( '/experiment-observer-meta-result/{experiment_id}',          'ExperimentObserverMetaResultsController@index_all');

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
