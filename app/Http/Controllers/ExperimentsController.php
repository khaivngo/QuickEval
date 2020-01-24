<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

use App\Experiment;
use App\ObserverMeta;
use App\ExperimentObserverMeta;
use App\Picture;

use App\Classes\Algorithms;

use App\Rules\AllowedTripletCount;

class ExperimentsController extends Controller
{
    public function index () {
      return Experiment::where('user_id', auth()->user()->id)
        ->orderBy('id', 'desc')
        ->get();
    }

    public function search ($term) {
      $search_term = '%'.$term.'%';

      return Experiment::where([
        ['is_public', 1],
        ['title', 'LIKE', $search_term]
      ])->get();
    }

    /**
     *
     */
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

      # category or triplet
      if ($experiment->experiment_type_id === 3 || $experiment->experiment_type_id === 5) {
        $experiment_categories = \App\ExperimentCategory::where('experiment_id', $request->id)->get();

        $all = [];
        foreach ($experiment_categories as $experiment_category) {
          array_push($all, $experiment_category->category);
        }

        $experiment['categories'] = $all;
      }

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

      $request->validate([
        'title'          => 'required|string',
        'experimentType' => 'required'
      ]);

      if ($request->experimentType == 3 || $request->experimentType == 5) // Category and Triplet
      {
        $request->validate([
          'categories' => 'required'
        ]);
      }

      if ($request->experimentType == 5) // Triplet
      {
        foreach ($request->sequences as $step)
        {
          if ($step['type'] === 'imageSet')
          {
            $images = Picture::where([
              ['picture_set_id', $step['value']],
              ['is_original', 0]
            ])->get();

            # generating triplets queue only work with a certain number of images
            $data = new Request(['imageCount' => $images->count()]);
            $this->validate($data, ['imageCount' => new AllowedTripletCount]);

            // Validator::make(
            //   ['num' => $images->count()],
            //   ['num' => new AllowedTripletCount]
            // );
          }
        }
      }


      $experiment = Experiment::create([
        'user_id'           => auth()->user()->id,
        'title'             => $request->title,
        'experiment_type_id'=> $request->experimentType,
        'picture_sequence_algorithm' => $request->algorithm,
        'delay'             => $request->delay,
        'stimuli_spacing'   => $request->stimuliSpacing,
        'short_description' => $request->shortDescription,
        'long_description'  => $request->longDescription,
        'is_public'         => $request->isPublic,
        'same_pair'         => $request->samePairTwice,
        'background_colour' => $request->bgColour,
        'show_original'     => $request->showOriginal,
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
              # random within image set OR within and between image sets
              if ($experiment->picture_sequence_algorithm === 1 || $experiment->picture_sequence_algorithm === 2)
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
      $images = Picture::where([
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
      $images = Picture::where([
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

      $images = Picture::where([
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

      // $request->session()->put('key', 'value');

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
        ->get();

      $all = [];
      $nounce = 0;
      foreach ($sequences as $key => $sequence)
      {
        # if picture sequence
        if ($sequence->picture_queue_id !== null)
        {
          # get all picture sequences
          $result = DB::table('experiment_sequences')
            ->join('picture_sequences', 'picture_sequences.picture_queue_id', '=', 'experiment_sequences.picture_queue_id')
            ->join('pictures', 'picture_sequences.picture_id', '=', 'pictures.id')
            ->where('experiment_sequences.id', $sequence->id)
            ->get(['picture_sequences.*', 'pictures.path', 'pictures.name', 'pictures.is_original', 'pictures.picture_set_id']);

          // future: if $experiment->experiment_algorithm = 1 or 2

          # shuffle the picture queue every time we fetch it,
          # to make sure every observer gets a different picture queue.
          if ($experiment->experiment_type_id == 1) {
            $Algorithms = new Algorithms;
            $result = $Algorithms->shuffle_the_cards($result);
          } else {
            $result = $result->shuffle();
          }

          # take the id of first image in the sequence, then the picture_set_id from that image, then find the orginal with that id
          $picture = Picture::where('id', $result[0]->picture_id)->first();
          $picture_set = PictureSet::where('id', $picture->picture_set_id)->first();
          $original = Picture::where([
            ['picture_set_id', $picture_set->id],
            ['is_original', 1]
          ])->first();
          $result[0]->original = $original;

          # shuffle the order of image sets, make sure order of instructions is not affected (only shuffle inbetween instructions)
          if ($experiment->picture_sequence_algorithm == 2) {
            $all[$nounce][$key] = [ 'picture_queue' => $result ];
            shuffle($all[$nounce]);
          } else {
            $all[] = [ 'picture_queue' => $result ];
          }
        }

        if ($sequence->instruction_id !== null)
        {
          # get all instruction belonging to experiment sequence
          $result = DB::table('experiment_sequences')
            ->join('instructions', 'instructions.id', '=', 'experiment_sequences.instruction_id')
            ->where('experiment_sequences.id', $sequence->id)
            ->get(); // ->first();

          if ($experiment->picture_sequence_algorithm == 2) {
            ++$nounce;
            $all[$nounce] = [ 'instructions' => $result ];
            ++$nounce;
          } else {
            $all[] = [ 'instructions' => $result ];
          }
        }
      }

      // $flattened = $flattened->shuffle();
      $collection = collect($all);

      # if rank order
      if ($experiment->experiment_type_id == 2) {
        # if random between image sets
        # flatten arrays by one level
        if ($experiment->picture_sequence_algorithm == 2) {
          $hello = [];
          foreach ($all as $one) {
            $key = key($one);
            if ($key == 'picture_queue'){
              foreach ($one as $b) {
                $hello[] = $b;
              }
            } else if ($key == 'instructions') {
              $hello[] = $one;
            }
          }
          $collection = collect($hello);
          return response($collection);
        }

        return response($collection);
      }

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
        'picture_sequence_algorithm' => $request->algorithm,
        'delay'             => $request->delay,
        'stimuli_spacing'   => $request->stimuliSpacing,
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
              # random within image set OR within and between image sets
              if ($experiment->picture_sequence_algorithm === 1 || $experiment->picture_sequence_algorithm === 2)
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

    /**
     * Remove the specified experiment from storage, if you are the rightful owner.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy (Experiment $experiment)
    {
      if ($experiment->user_id != auth()->user()->id) {
        return response('Unauthorized', 401);
      }

      $experiment->delete();

      return response('deleted_experiment', 200);
    }
}
