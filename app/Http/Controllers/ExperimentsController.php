<?php

namespace App\Http\Controllers;

use App\Experiment;
use Illuminate\Http\Request;

use App\ObserverMeta;
use App\ExperimentObserverMeta;

class ExperimentsController extends Controller
{
    public function index () {
      return Experiment::where('user_id', auth()->user()->id)->get();
    }

    public function all () {
      return Experiment::orderBy('id', 'desc')->get();
    }

    public function find (Request $request) {
      return Experiment::find($request->id);
    }
    
    public function findIsOwner (Request $request) {
      return Experiment::where('user_id', auth()->user()->id)
        ->where('id', $request->id)
        ->get();
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
        'same_pair'         => $request->samePairTwice
      ]);

      if ($experiment->id > 0)
      {
        // foreach ($request->observerMetas as $meta)
        // {
        //   if ($meta['type'] == 'meta') {
        //     $observerMeta = ObserverMeta::create([
        //       'user_id' => auth()->user()->id,
        //       'meta'    => $meta['value']
        //     ]);

        //     ExperimentObserverMeta::create([
        //       'experiment_id'    => $experiment->id,
        //       'observer_meta_id' => $observerMeta->id
        //     ]);
        //   }

        //   if ($meta['type'] == 'metaFromHistory') {
        //     ExperimentObserverMeta::create([
        //       'experiment_id'    => $experiment->id,
        //       'observer_meta_id' => $meta['value']
        //     ]);
        //   }
        // }

        foreach ($request->sequences as $step)
        {
          if ($step['type'] === 'imageSet')
          {
            if ($request->algorithm === 'Random Queue') // save algo in experiment table and use $experiment->algorithm instead? 
            {
              if ($experiment->experiment_type_id == 1) // add triplet comparison as well
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
                $experiment->id,
                $picture_queue_id
              );
            }
          }
        //   else if ($step['type'] === 'instruction')
        //   {
        //     $experiment_queue_id = getExperimentQueueId();

        //     // "SELECT experimentqueue.id FROM experimentqueue " .
        //     // "JOIN experiment ON experimentqueue.experiment = experiment.id " .
        //     // "WHERE experiment.person = :person " .
        //     // "ORDER BY experimentqueue.id DESC"

        //     // get newest experiment queue for the user. DO WE NEED THIS?
        //     $experiment_queue_id = ExperimentQueue::where('user_id', auth()->user()->id)
        //       ->orderBy('id', 'DESC');

        //     $instruction = Instruction::create([
        //       'user_id' => auth()->user()->id,
        //       'description' => $request->description
        //     ]);

        //     ExperimentSequence::create([
        //       'experiment_queue_id' => $experiment_queue_id,
        //       'instruction_id' => $instruction->id
        //     ]);
        //   }
          else if ($step['type'] === 'instructionFromHistory')
          {
            $this->add_queue_to_experiment(
              $experiment->id,
              null,
              $step['value']
            );
            // $experiment_queue = \App\ExperimentQueue::create([
            //   'experiment_id' => $experiment_id
            // ]);

            // $experiment_sequence = \App\ExperimentSequence::create([
            //   'experiment_queue_id' => $experiment_queue->id,
            //   'picture_queue_id' => $picture_queue_id,
            //   'instruction_id' => $instruction_id
            // ]);
          }
        }


        // return response($experiment, 201);

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

      $pairs = $this->make_queue($images, $twice);

      $picture_queue = \App\PictureQueue::create([
        'title' => NULL
      ]);

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

    private function make_queue($images, $imagesShownRightAndLeft) {
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

    /**
     *
     */
    protected function add_queue_to_experiment ($experiment_id, $picture_queue_id = null, $instruction_id = null)
    {
      // $experiment_queue = \App\ExperimentQueue::where('experiment_id', $experiment_id)->first();

      $experiment_queue = \App\ExperimentQueue::create([
        'experiment_id' => $experiment_id
      ]);

      $experiment_sequence = \App\ExperimentSequence::create([
        'experiment_queue_id' => $experiment_queue->id,
        'picture_queue_id' => $picture_queue_id,
        'instruction_id' => $instruction_id
      ]);

      return $experiment_sequence;
    }

    /**
     *
     */
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
        'is_public' => 'required|boolean'
      ]);

      $experiment->update($data);

      return response($experiment, 200);

      // Experiment::where('user_id', auth()->user()->id)->update($data);
    }

    public function destroy (Request $request, Experiment $experiment) {
      if ($experiment->user_id !== auth()->user()->id) {
        return response()->json('Unauthorized', 401);
      }

      $experiment->delete();

      return response('Deleted experiment', 200);
    }

    public function destroyExperimentResults (Request $request, Experiment $experiment) {
      // $results = $request->results;

      $userResults = auth()->user()->results->map(function ($result) {
        return $result->id;
      });

      $valid = collect($results)->every(function ($value, $key) use ($userResultIds) {
        return $userResultIds->contains($value);
      });

      if (!$valid) {
        return response()->json('Unauthorized', 401);
      }

      Result::destroy($request->results);

      return response()->json('Deleted', 200);
    }
}
