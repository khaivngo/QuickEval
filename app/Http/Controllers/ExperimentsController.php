<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

use App\Experiment;
use App\ObserverMeta;
use App\ExperimentObserverMeta;

class ExperimentsController extends Controller
{
    public function index () {
      return Experiment::where('user_id', auth()->user()->id)
        ->orderBy('id', 'desc')
        ->get();
    }

    public function is_owner (Request $request) {
      $experiment = Experiment::where([
        ['user_id', auth()->user()->id],
        ['id', $request->id]
      ])->first();

      $sequences = DB::table('experiment_queues')
        ->join('experiment_sequences', 'experiment_sequences.experiment_queue_id', '=', 'experiment_queues.id')
        ->leftJoin('picture_sets', 'experiment_sequences.picture_set_id', '=', 'picture_sets.id')
        ->leftJoin('instructions', 'experiment_sequences.instruction_id', '=', 'instructions.id')
        ->where('experiment_queues.experiment_id', $request->id)
        ->get();
      $experiment['sequences'] = $sequences;

      $metas = DB::table('experiment_observer_metas')
        ->join('observer_metas', 'observer_metas.id', '=', 'experiment_observer_metas.observer_meta_id')
        ->where('experiment_observer_metas.experiment_id', $request->id)
        ->get();
      $experiment['metas'] = $metas;

      // $concatenated = $experiment->concat($sequences)->concat($metas);
      // return $concatenated->all();

      // if ($experiment->experiment_type_id === 3 || $experiment->experiment_type_id === 5) {
      //   $experiment_categories = \App\ExperimentCategory::where('experiment_id', $request->id)->get();

      //   $all = [];
      //   foreach ($experiment_categories as $experiment_category) {
      //     array_push($all, $experiment_category->category);
      //   }
      // }


      return $experiment;
    }

    public function find (Request $request) {
      return Experiment::find($request->id);
    }

    public function find_public (Request $request) {
      return Experiment::where([
        ['id', $request->id],
        ['is_public', 1]
      ])->first();
    }

    public function all () {
      return Experiment::orderBy('id', 'asc')
        ->get();
    }

    public function all_public () {
      return Experiment::where('is_public', 1)
        ->orderBy('id', 'desc')
        ->get();
    }

    public function observer_metas (Request $request) {
      $metas = DB::table('experiment_observer_metas')
        ->join('observer_metas', 'observer_metas.id', '=', 'experiment_observer_metas.observer_meta_id')
        ->where('experiment_observer_metas.experiment_id', $request->id)
        ->get();

      // ExperimentObserverMetas::find($id)->observer_meta;

      return $metas;
    }

    public function store (Request $request) {
      # abort if not scientist or admin
      if (auth()->user()->role < 2) {
        return response()->json('Unauthorized', 401);
      }

      $data = $request->validate([
        'title'          => 'required|string',
        'experimentType' => 'required'
      ]);

      $experiment = Experiment::create([
        'user_id'           => auth()->user()->id,
        'title'             => $request->title,
        'experiment_type_id'=> $request->experimentType,
        'short_description' => $request->shortDescription,
        'long_description'  => $request->longDescription,
        'is_public'         => $request->isPublic,
        'same_pair'         => $request->samePairTwice,
        'background_colour' => $request->bgColour,
        'show_original'     => $request->show_original,
        'version'           => 1
      ]);

      if ($experiment->id > 0)
      {
        $experiment_queue = \App\ExperimentQueue::create([
          'experiment_id' => $experiment->id
        ]);

        if ($experiment_queue->id > 0)
        {
          foreach ($request->observerMetas as $meta)
          {
            if ($meta['type'] == 'meta') {
              $observerMeta = ObserverMeta::create([
                'user_id' => auth()->user()->id,
                'meta'    => $meta['value']
              ]);

              ExperimentObserverMeta::create([
                'experiment_id'    => $experiment->id,
                'observer_meta_id' => $observerMeta->id
              ]);
            }

            if ($meta['type'] == 'metaFromHistory') {
              ExperimentObserverMeta::create([
                'experiment_id'    => $experiment->id,
                'observer_meta_id' => $meta['value']
              ]);
            }
          }

          if ($experiment->experiment_type_id == 3 || $experiment->experiment_type_id == 5)
          {
            foreach ($request->categories as $category)
            {
              if ($category['type'] == 'category') {
                $cat = \App\Category::create([
                  'user_id' => auth()->user()->id,
                  'title'   => $category['value']
                ]);

                \App\ExperimentCategory::create([
                  'experiment_id' => $experiment->id,
                  'category_id'   => $cat->id
                ]);
              }

              if ($category['type'] == 'categoryFromHistory') {
                \App\ExperimentCategory::create([
                  'experiment_id' => $experiment->id,
                  'category_id'   => $category['value']
                ]);
              }
            }
          }


          foreach ($request->sequences as $step)
          {
            if ($step['type'] === 'imageSet') // TODO: check that the user owns the image set
            {
              # random within image set
              if ($request->algorithm === 1) // save algo in experiment table and use $experiment->algorithm instead?
              {
                if ($experiment->experiment_type_id == 1)
                {
                  $picture_queue_id = $this->random_paired_queue(
                    $step['value'],
                    $experiment->same_pair
                  );
                }
                else if ($experiment->experiment_type_id == 5)
                {
                  $picture_queue_id = $this->random_triplet_queue( $step['value'] );
                }
                else
                {
                  $picture_queue_id = $this->make_category_queue( $step['value'] );
                }

                $this->add_queue_to_experiment(
                  $experiment_queue->id,
                  $picture_queue_id,
                  null,
                  $step['value']
                );
              }
            }
            else if ($step['type'] === 'instruction')
            {
              $instruction = \App\Instruction::create([
                'user_id' => auth()->user()->id,
                'description' => $step['value']
              ]);

              $this->add_queue_to_experiment(
                $experiment_queue->id,
                null,
                $instruction->id,
                null
              );
            }
            else if ($step['type'] === 'instructionFromHistory')
            {
              // TODO: check that the user owns the instruction

              $this->add_queue_to_experiment(
                $experiment_queue->id,
                null,
                $step['value'],
                null
              );
            }
          }

          return response($experiment, 201);
        } else {
          return response('Experiment queue could not be created.', 404);
          // IF SOMETHING FAILS WE SHOULD DELETE THE EXPERIMENT
          // $experiment->delete();
        }
      } else {
        return response('Experiment could not be created.', 404);
      }
    }

    /**
     * 
     */
    protected function random_triplet_queue ($imageSetId)
    {
      $images = \App\Picture::where([
        ['picture_set_id', $imageSetId],
        ['is_original', 0]
      ])->get();

      $TripletComparison = new \App\Classes\TripletComparison();
      $triplets = $TripletComparison->make_queue($images);

      $picture_queue = \App\PictureQueue::create([
        'title' => NULL
      ]);

      # construct an array of all picture sequences
      $order = 0;
      $queries = [];
      foreach ($triplets as $triplet) {
        array_push(
          $queries,
          array('picture_order' => $order, 'picture_id' => $triplet[0], 'picture_queue_id' => $picture_queue->id),
          array('picture_order' => $order, 'picture_id' => $triplet[1], 'picture_queue_id' => $picture_queue->id),
          array('picture_order' => $order, 'picture_id' => $triplet[2], 'picture_queue_id' => $picture_queue->id)
        );

        $order++;
      }

      \App\PictureSequence::insert($queries);

      return $picture_queue->id;
    }

    /**
     * 
     */
    protected function random_paired_queue ($imageSetId, $twice)
    {
      $images = \App\Picture::where([
        ['picture_set_id', $imageSetId],
        ['is_original', 0]
      ])->get();

      $PairedComparison = new \App\Classes\PairedComparison();
      $pairs = $PairedComparison->make_queue($images, $twice);
      // $pairs = $this->make_queue($images, $twice);

      $picture_queue = \App\PictureQueue::create([
        'title' => NULL
      ]);

      # construct an array of all picture sequences
      $order = 0;
      $queries = [];
      foreach ($pairs as $pair) {
        array_push(
          $queries,
          array('picture_order' => $order, 'picture_id' => $pair[0], 'picture_queue_id' => $picture_queue->id),
          array('picture_order' => $order, 'picture_id' => $pair[1], 'picture_queue_id' => $picture_queue->id)
        );

        $order++;
      }

      \App\PictureSequence::insert($queries);

      return $picture_queue->id;
    }

    /**
     *
     */
    protected function make_category_queue ($image_set_id)
    {
      $picture_queue = \App\PictureQueue::create([
        'title' => NULL
      ]);

      $images = \App\Picture::where([
        ['picture_set_id', $image_set_id],
        ['is_original', 0]
      ])->get();

      $queries = [];
      foreach ($images as $image) {
        array_push(
          $queries,
          array('picture_order' => 0, 'picture_id' => $image['id'], 'picture_queue_id' => $picture_queue->id)
        );
      }

      \App\PictureSequence::insert($queries);

      return $picture_queue->id;
    }

    /**
     *
     */
    protected function add_queue_to_experiment ($experiment_queue_id, $picture_queue_id = null, $instruction_id = null, $picture_set_id = null)
    {
      $experiment_sequence = \App\ExperimentSequence::create([
        'experiment_queue_id' => $experiment_queue_id,
        'picture_queue_id'    => $picture_queue_id,
        'instruction_id'      => $instruction_id,
        'picture_set_id'      => $picture_set_id
      ]);

      return $experiment_sequence;
    }

    /**
     * Set whether the experiment should be visible to the public or not.
     */
    public function visibility (Request $request, Experiment $experiment)
    {
      if ($experiment->user_id !== auth()->user()->id) {
        return response()->json('Unauthorized', 401);
      }

      $data = $request->validate([
        'is_public' => 'required'
      ]);

      $experiment->update($data);

      return response($experiment, 200);
    }

    /**
     *
     */
    public function start (Experiment $experiment)
    {
      // $newOrExists = $this->start_results($id);

      $sequences = DB::table('experiment_queues')
        ->join('experiment_sequences', 'experiment_sequences.experiment_queue_id', '=', 'experiment_queues.id')
        ->where('experiment_queues.experiment_id', $experiment->id)
        ->get(); // Add this?: ORDER BY experiment_sequences.id ASC;

      $all = [];
      foreach ($sequences as $sequence)
      {
        if ($sequence->picture_queue_id !== null)
        {
          # get all picture sequences
          $result = DB::table('experiment_sequences')
            ->join('picture_sequences', 'picture_sequences.picture_queue_id', '=', 'experiment_sequences.picture_queue_id')
            ->where('experiment_sequences.id', $sequence->id)
            ->get();

          # algorithm that shuffle the picture queue every time we fetch it,
          # to make sure every observer gets a different queue.

          // TODO: if $experiment->experiment_algorithm = random

          if ($experiment->experiment_type_id == 1) {
            $Algorithms = new \App\Classes\Algorithms;
            $result = $Algorithms->shuffle_the_cards($result);
          } else {
            $result = $result->shuffle();
          }

          # add a property to the first object in the sequence,
          # this will be used by the frontend to understand when to check for a original image
          $result[0]->start = true;

          // take the picture_set_id of first picture in the sequence and find the original in that set,

          // take the id of first image in the sequence, then the picture_set_id from that image, then find the orginal with that id

          $picture = \App\Picture::where('id', $result[0]->picture_id)->first();
          $picture_set = \App\PictureSet::where('id', $picture->picture_set_id)->first();
          $original = \App\Picture::where([
            ['picture_set_id', $picture_set->id],
            ['is_original', 1]
          ])->first();

          $result[0]->original = $original;

          array_push($all, [ 'picture_queue' => $result ]);
        }

        if ($sequence->instruction_id !== null)
        {
          # get all instruction belonging to experiment sequence
          $result = DB::table('experiment_sequences')
            ->join('instructions', 'instructions.id', '=', 'experiment_sequences.instruction_id')
            ->where('experiment_sequences.id', $sequence->id)
            ->get(); // ->first();

          array_push($all, [ 'instructions' => $result ]);
        }
      }

      // return response($collection, 200);

      # shuffle the order of image sets
      // WARNING!!! THIS RANDOMIZES INSTRUCTIONS
      // if (we have only 1 instruction) {
        // if ($experiment->algorithm == 1) {
        //   $flattened = $flattened->shuffle(); // do we need to re-assign?
        // }
      // }


      // if picture_queue run until no more picture queue... shuffle that... run again

      // if ($experiment->algorithm == 2) {
      // $w = [];

      // foreach ($all as $one) {
      //   if ($one->picture_queue) {
          
      //   }
      // }
      // }
      

      $collection = collect($all);
      $flattened = $collection->flatten();

      return response($flattened->all());
    }

    /**
     *
     */
    public function start_results ($id)
    {
      // replace with?
      // Retrieve flight by name, or create it if it doesn't exist...
      // $flight = App\Flight::firstOrCreate(['name' => 'Flight 10']);

      $experimentResult = \App\ExperimentResult::where([
        ['experiment_id', (int)$id],
        ['user_id', auth()->user()->id]
      ])->get();

      if (count($experimentResult) > 0) {
        return response('exists', 200);
      } else {
        \App\ExperimentResult::create([
          'experiment_id' => (int)$id,
          'user_id' => auth()->user()->id
        ]);
        return response('new', 200);
      }
    }

    /**
     * Set the experiment status to completed.
     */
    public function completed (Request $request, \App\ExperimentResult $experiment) {
      if ($experimentResult->user_id !== auth()->user()->id) {
        return response()->json('Unauthorized', 401);
      }

      if ($experimentResult->experiment_id !== $request->experiment_id) {
        return response()->json('Unauthorized', 401);
      }

      $data = $request->validate([
        'completed' => 'required'
      ]);

      $experimentResult->update($data);

      return response($experimentResult, 200);
    }


    public function update (Request $request, Experiment $original_experiment) {
      # abort if not scientist or admin
      if (auth()->user()->role < 2) {
        return response()->json('Unauthorized', 401);
      }

      # abort if not owner of original experiment
      if ($original_experiment->user_id !== auth()->user()->id) {
        return response()->json('Unauthorized', 401);
      }

      $data = $request->validate([
        'title'          => 'required|string',
        'experimentType' => 'required'
      ]);

      if ($original_experiment->first_version_id) {
        $last_version = Experiment::where('first_version_id', $original_experiment->first_version_id)->latest()->first();
      } else {
        $last_version = Experiment::where('first_version_id', $original_experiment->id)->latest()->first();
      }

      # have we updated before?
      $version  = ($last_version) ? ++$last_version->version : 2;
      $original = ($original_experiment->first_version_id) ? $original_experiment->first_version_id : $original_experiment->id;

      $experiment = Experiment::create([
        'user_id'           => auth()->user()->id,
        'title'             => $request->title,
        'experiment_type_id'=> $request->experimentType,
        'short_description' => $request->shortDescription,
        'long_description'  => $request->longDescription,
        'is_public'         => $request->isPublic,
        'same_pair'         => $request->samePairTwice,
        'background_colour' => $request->bgColour,
        'show_original'     => $request->showOriginal,
        'first_version_id'  => $original,
        'version'           => $version
      ]);

      // TODO: abstract store function into another file/class

      if ($experiment->id > 0)
      {
        $experiment_queue = \App\ExperimentQueue::create([
          'experiment_id' => $experiment->id
        ]);

        if ($experiment_queue->id > 0)
        {
          foreach ($request->observerMetas as $meta)
          {
            if ($meta['type'] == 'meta') {
              $observerMeta = ObserverMeta::create([
                'user_id' => auth()->user()->id,
                'meta'    => $meta['value']
              ]);

              ExperimentObserverMeta::create([
                'experiment_id'    => $experiment->id,
                'observer_meta_id' => $observerMeta->id
              ]);
            }

            if ($meta['type'] == 'metaFromHistory') {
              ExperimentObserverMeta::create([
                'experiment_id'    => $experiment->id,
                'observer_meta_id' => $meta['value']
              ]);
            }
          }

          if ($experiment->experiment_type_id == 3 || $experiment->experiment_type_id == 5)
          {
            foreach ($request->categories as $category)
            {
              if ($category['type'] == 'category') {
                $cat = \App\Category::create([
                  'user_id' => auth()->user()->id,
                  'title'   => $category['value']
                ]);

                \App\ExperimentCategory::create([
                  'experiment_id' => $experiment->id,
                  'category_id'   => $cat->id
                ]);
              }

              if ($category['type'] == 'categoryFromHistory') {
                \App\ExperimentCategory::create([
                  'experiment_id' => $experiment->id,
                  'category_id'   => $category['value']
                ]);
              }
            }
          }


          foreach ($request->sequences as $step)
          {
            if ($step['type'] === 'imageSet') // TODO: check that the user owns the image set
            {
              # random within image set
              if ($request->algorithm === 1) // save algo in experiment table and use $experiment->algorithm instead?
              {
                if ($experiment->experiment_type_id == 1)
                {
                  $picture_queue_id = $this->random_paired_queue(
                    $step['value'],
                    $experiment->same_pair
                  );
                }
                else if ($experiment->experiment_type_id == 5)
                {
                  $picture_queue_id = $this->random_triplet_queue( $step['value'] );
                }
                else
                {
                  $picture_queue_id = $this->make_category_queue( $step['value'] );
                }

                $this->add_queue_to_experiment(
                  $experiment_queue->id,
                  $picture_queue_id,
                  null,
                  $step['value']
                );
              }
            }
            else if ($step['type'] === 'instruction')
            {
              $instruction = \App\Instruction::create([
                'user_id' => auth()->user()->id,
                'description' => $step['value']
              ]);

              $this->add_queue_to_experiment(
                $experiment_queue->id,
                null,
                $instruction->id,
                null
              );
            }
            else if ($step['type'] === 'instructionFromHistory')
            {
              // TODO: check that the user owns the instruction

              $this->add_queue_to_experiment(
                $experiment_queue->id,
                null,
                $step['value'],
                null
              );
            }
          }

          return response($experiment, 201);
        } else {
          return response('Experiment queue could not be created.', 404);
          // IF SOMETHING FAILS WE SHOULD DELETE THE EXPERIMENT
          // $experiment->delete();
        }
      } else {
        return response('Experiment could not be created.', 404);
      }

      // return response($experiment, 200);
    }

    public function destroy (Request $request, Experiment $experiment) {
      if ($experiment->user_id !== auth()->user()->id) {
        return response()->json('Unauthorized', 401);
      }

      $experiment->delete();

      return response('deleted_experiment', 200);
    }
}
