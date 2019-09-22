<?php

namespace App\Http\Controllers;

use App\Experiment;
use Illuminate\Http\Request;
use PDO;
use DB;

use App\ObserverMeta;
use App\ExperimentObserverMeta;

class ExperimentsController extends Controller
{
    public function index () {
      return Experiment::where('user_id', auth()->user()->id)
        ->get();
    }

    public function all () {
      return Experiment::orderBy('id', 'desc')
        ->get();
    }

    public function all_public () {
      return Experiment::where('is_public', 1)->get();
        // ->orderBy('id', 'desc')
        // ->get();
    }

    public function find (Request $request) {
      return Experiment::find($request->id);
    }

    public function observer_metas (Request $request) {
      $metas = DB::table('experiment_observer_metas')
        ->join('observer_metas', 'observer_metas.id', '=', 'experiment_observer_metas.observer_meta_id')
        ->where('experiment_observer_metas.experiment_id', $request->id)
        ->get();

      return $metas;
    }

    public function is_owner (Request $request) {
      return Experiment::where([
        ['user_id', auth()->user()->id],
        ['id', $request->id]
      ])->get();
    }

    public function store (Request $request) {
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
        'show_original'     => $request->showOriginal
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

          foreach ($request->sequences as $step)
          {
            if ($step['type'] === 'imageSet')
            {
              if ($request->algorithm === 'Random Queue') // save algo in experiment table and use $experiment->algorithm instead?
              {
                if ($experiment->experiment_type_id == 1)
                {
                  $picture_queue_id = $this->random_queue(
                    $step['type'],
                    $experiment->same_pair,
                    $step['value']
                  );
                }
                else # Category Rating
                {
                  $picture_queue_id = $this->make_category_queue( $step['value'] );
                }

                $this->add_queue_to_experiment(
                  $experiment_queue->id,
                  $picture_queue_id,
                  null
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
                $instruction->id
              );
            }
            else if ($step['type'] === 'instructionFromHistory')
            {
              // TODO: check that the user owns the instruction

              $this->add_queue_to_experiment(
                $experiment_queue->id,
                null,
                $step['value']
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
    protected function random_queue ($type, $twice, $imageSetId) {
      $images = \App\Picture::where([
        ['picture_set_id', $imageSetId],
        ['is_original', 0]
      ])->get();

      // $algorithm = new \App\Classes\Algorithms();
      // $algorithm->make_queue($images, $twice);
      $pairs = $this->make_queue($images, $twice);

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

    protected function make_category_queue ($image_set_id) {
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
    protected function add_queue_to_experiment ($experiment_queue_id, $picture_queue_id = null, $instruction_id = null)
    {
      // $experiment_queue = \App\ExperimentQueue::where('experiment_id', $experiment_id)->first();

      // $experiment_queue = \App\ExperimentQueue::create([
      //   'experiment_id' => $experiment_id
      // ]);

      $experiment_sequence = \App\ExperimentSequence::create([
        'experiment_queue_id' => $experiment_queue_id,
        'picture_queue_id'    => $picture_queue_id,
        'instruction_id'      => $instruction_id
      ]);

      return $experiment_sequence;
    }

    public function update (Request $request, Experiment $experiment) {
      if ($experiment->user_id !== auth()->user()->id) {
        return response()->json('Unauthorized', 401);
      }

      $data = $request->validate([
        'title' => 'required|string',
        'experiment_type' => 'required'
      ]);

      $experiment->update($data);

      return response($experiment, 200);
    }

    /**
     * Set whether the experiment should be visible to the public or not.
     */
    public function visibility (Request $request, Experiment $experiment) {
      if ($experiment->user_id !== auth()->user()->id) {
        return response()->json('Unauthorized', 401);
      }

      $data = $request->validate([
        'is_public' => 'required'
      ]);

      $experiment->update($data);

      return response($experiment, 200);
    }

    public function destroy (Request $request, Experiment $experiment) {
      if ($experiment->user_id !== auth()->user()->id) {
        return response()->json('Unauthorized', 401);
      }

      $experiment->delete();

      return response('deleted_experiment', 200);
    }

    /**
     *
     */
    public function start ($id)
    {
      // $newOrExists = $this->start_results($id);

      $sequences = DB::table('experiment_queues')
        ->join('experiment_sequences', 'experiment_sequences.experiment_queue_id', '=', 'experiment_queues.id')
        ->where('experiment_queues.experiment_id', $id)
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
          $Algorithms = new \App\Classes\Algorithms;
          $result = $Algorithms->shuffle_the_cards($result);

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

      $collection = collect($all);
      $flattened = $collection->flatten();

      return response($flattened->all());
    }

    /**
     *
     */
    public function start_results ($id) {
      $experimentResult = \App\ExperimentResult::where([
        ['experiment_id', (int)$id],
        ['user_id', auth()->user()->id]
      ])->get();

      if (count($experimentResult) > 0) {
        return "exists";
      } else {
        \App\ExperimentResult::create([
          'experiment_id' => (int)$id,
          'user_id' => auth()->user()->id
        ]);
        return "new";
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

    /**
     * Algorithm for generating a image experiment queue.
     */
    private function make_queue ($images, $imagesShownRightAndLeft) {
      $pairs = [];
      $index = 1;
      $arrIndex = 0;

      foreach ($images as $image) {
        for ($i = $index; $i < count($images); $i++ ) {
          $pairs[$arrIndex][0] = $image['id'];
          $pairs[$arrIndex][1] = $images[$i]['id'];
          if ($imagesShownRightAndLeft == 1) {
            $arrIndex++;
            $pairs[$arrIndex][0] = $images[$i]['id'];
            $pairs[$arrIndex][1] = $image['id'];
          }
          $arrIndex++;
        }
        $index++;
      }
      shuffle($pairs); # https://www.php.net/manual/en/function.shuffle.php

      return $pairs;
    }
}
