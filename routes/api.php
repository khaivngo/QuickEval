<?php

use Illuminate\Http\Request;

use App\Http\Controllers\UserController;
use App\Http\Controllers\ExperimentsController;
use App\Http\Controllers\PictureSetsController;
use App\Http\Controllers\ExperimentResultsController;
use App\Http\Controllers\ExperimentTypesController;
use App\Http\Controllers\ObserverMetasController;
use App\Http\Controllers\ExperimentSequencesController;
use App\Http\Controllers\ExperimentCategoriesController;
use App\Http\Controllers\ExperimentObserverMetasController;
use App\Http\Controllers\PicturesController;
use App\Http\Controllers\ResultPairsController;
use App\Http\Controllers\ResultCategoriesController;
use App\Http\Controllers\ResultRankOrdersController;
use App\Http\Controllers\ResultTripletsController;
use App\Http\Controllers\ResultObserverMetasController;
use App\Http\Controllers\ResultImageArtifactsController;
use App\Http\Controllers\InstructionsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ScientistRequestsController;
use App\Http\Controllers\AuthController;

// Route::get('mailable', function () {
//     // $experiment_results = \App\ExperimentResult::find(8);
//     $observer_metas = \App\ExperimentResult::find(8)->observer_metas;

//     // $user = \App\User::find(1);

//     // return new \App\Mail\ScientistRequest($user);
//     // // return new \App\Mail\Receipt($user);
// });

Route::post('/register',    [AuthController::class, 'register']    );
Route::post('/anonymous',   [AuthController::class, 'anonymous']   );
Route::post('/login',       [AuthController::class, 'login']       );


// TODO: these should be possible to add inside auth:api middleware
Route::post('/result-paired/export',     [ResultPairsController::class, 'export']       );
Route::post('/result-triplet/export',    [ResultTripletsController::class, 'export']    );
Route::post('/result-rank-order/export', [ResultRankOrdersController::class, 'export']  );
Route::post('/result-category/export',   [ResultCategoriesController::class, 'export']  );


# Note: auth:api middleware gives access to the user object
Route::middleware('auth:api')->group(function () {
    # user
    Route::get(  '/user',     [UserController::class, 'you']        );
    Route::patch('/user',     [UserController::class, 'update']     );
    Route::patch('/user/role',[UserController::class, 'updateRole'] );
    Route::get(  '/user/all', [UserController::class, 'index']      );

    # experiments
    Route::get(   '/experiment/{id}/observer-metas',       [ExperimentsController::class, 'observer_metas'] );
    Route::get(   '/experiments',                          [ExperimentsController::class, 'index']          );
    Route::get(   '/experiment/public',                    [ExperimentsController::class, 'all_public']     );
    Route::get(   '/experiment/{id}',                      [ExperimentsController::class, 'find']           );
    Route::get(   '/experiment/{id}/owner',                [ExperimentsController::class, 'is_owner']       );
    Route::get(   '/experiment/{experiment}/start',        [ExperimentsController::class, 'start']          );
    Route::get(   '/experiment/{id}/public',               [ExperimentsController::class, 'find_public']    );
    Route::post(  '/experiment/store',                     [ExperimentsController::class, 'store']          );
    Route::patch( '/experiment/{experiment}/visibility',   [ExperimentsController::class, 'visibility']     );
    // we do not use patch on this "update" because we want to create a complete new experiment,
    // and tag the old one with "version 1"
    Route::post(  '/experiment/{original_experiment}/update', [ExperimentsController::class, 'update']      );
    Route::delete('/experiment/{experiment}',                 [ExperimentsController::class, 'destroy']     );
    Route::get(   '/experiment/{term}/search/public',         [ExperimentsController::class, 'search']      );

    # experiment results
    Route::get   ('/experiment-result/{id}',              [ExperimentResultsController::class, 'fetch']     );
    Route::post  ('/experiment-result/create',            [ExperimentResultsController::class, 'store']     );
    Route::patch ('/experiment-result/{result}/update',   [ExperimentResultsController::class, 'update']    );
    Route::patch ('/experiment-result/{result}/completed',[ExperimentResultsController::class, 'completed'] );
    Route::delete('/experiment-result',                   [ExperimentResultsController::class, 'destroy']   );

    # experiment types
    Route::get('/experiment-types', [ExperimentTypesController::class, 'all']);

    # observer metas
    Route::get('/observer-metas', [ObserverMetasController::class, 'index']);

    # experiment sequences
    Route::get('/experiment-sequences/{id}', [ExperimentSequencesController::class, 'index']);

    # experiment categories
    Route::get('/experiment/{experiment}/categories', [ExperimentCategoriesController::class, 'index']);

    # experiment observer metas
    Route::get('/experiment-observer-metas/{id}', [ExperimentObserverMetasController::class, 'index']);

    # picture sets
    Route::post(  '/picture-set',                 [PictureSetsController::class, 'store']   );
    Route::get(   '/picture-set',                 [PictureSetsController::class, 'index']   );
    Route::get(   '/picture-set/{id}',            [PictureSetsController::class, 'find']    );
    Route::patch( '/picture-set/{picture_set}',   [PictureSetsController::class, 'update']  );
    Route::delete('/picture-set/{id}',            [PictureSetsController::class, 'destroy'] );

    # pictures
    Route::delete('/picture/{id}', [PicturesController::class, 'destroy']   );
    Route::get(   '/picture/{id}', [PicturesController::class, 'index']     );

    # paired results
    Route::post('/result-pairs/{id}/statistics', [ResultPairsController::class, 'statistics']   );
    Route::post('/result-pairs',                 [ResultPairsController::class, 'store']        );

    # category results
    Route::post('/result-categories/{id}/statistics', [ResultCategoriesController::class, 'statistics']  );
    Route::post('/result-categories',                 [ResultCategoriesController::class, 'store']       );

    # rank order results
    Route::post('/result-rank-orders/{id}/statistics', [ResultRankOrdersController::class, 'statistics'] );
    Route::post('/result-rank-orders',                 [ResultRankOrdersController::class, 'store']      );

    # triplet results
    Route::post('/result-triplets', [ResultTripletsController::class, 'store']);

    # result observer metas
    Route::post('/result-observer-metas', [ResultObserverMetasController::class, 'store']);

    # result image artifacts
    Route::get('/result-image-artifacts/{id}', [ResultImageArtifactsController::class, 'index']);

    # instructions
    Route::get('/instructions', [InstructionsController::class, 'index']);

    # categories
    Route::get('/categories', [CategoriesController::class, 'index']);

    # scientist requests
    Route::get( '/scientist-request',             [ScientistRequestsController::class, 'index']   );
    Route::post('/scientist-request/{id}/accept', [ScientistRequestsController::class, 'accept']  );
    Route::post('/scientist-request/{id}/reject', [ScientistRequestsController::class, 'reject']  );

    # misc
    Route::post('/logout', [AuthController::class, 'logout']);
});

// TODO: move this inside... InvalidArgumentException: Route [login] not defined
Route::post('/file', [PicturesController::class, 'store']);
