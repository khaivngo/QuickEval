<?php

namespace App\Http\Controllers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use DB;

use App\Experiment;
use App\ExperimentResult;
use App\ObserverMeta;
use App\ExperimentObserverMeta;
use App\Picture;

use App\Classes\Algorithms;

use App\Rules\AllowedTripletCount;

class ExperimentsController extends Controller
{
    /**
     * Get all experiments owned by the user. With the count of total experiments results,
     * and completed results.
     */
    public function index ()
    {
      return Experiment
        ::where('user_id', auth()->user()->id)
        ->withCount('results')
        ->withCount(['results', 'results as completed_results_count' => function (Builder $query) {
          $query->where('completed', 1);
        }])
        ->orderBy('id', 'desc')
        ->get();
    }

    /**
     * Return experiments where the provided string matches experiment titles, and/or
     * experiments that belong to matching scientist name, and/or experiments of
     * searched experiment type.
     */
    public function search ($term)
    {
      if ($term == 'all') {
        return Experiment::with('user:id,name', 'observer_metas.observer_meta')
          ->where('is_public', 1)
          ->orderBy('id', 'desc')
          ->get();
      }

      return Experiment::with('user:id,name', 'observer_metas.observer_meta')
        ->where([
          ['is_public', 1],
          ['title', 'LIKE', '%'.$term.'%']
        ])
        // I need the experiment if any of its user's name matches the given input
        ->orWhereHas('user', function($q) use ($term) {
            return $q->where([
              ['is_public', 1],
              ['name', 'LIKE', '%'.$term.'%']
            ]);
        })
        // I need the experiment if any of its type's title matches the given input
        ->orWhereHas('type', function($q) use ($term) {
            return $q->where([
              ['is_public', 1],
              ['title', 'LIKE', '%'.$term.'%']
            ]);
        })
        ->orderBy('id', 'desc')
        ->get();
    }

    /**
     * Return experiment if user is owner. With experiment queues, experiment observer metas,
     * and experiment categories if category or triplet exmperiment type.
     */
    public function is_owner (Request $request) {
      $experiment = Experiment::where([
        ['user_id', auth()->user()->id],
        ['id', $request->id]
      ])
      ->withCount('results')
      ->withCount(['results', 'results as completed_results_count' => function (Builder $query) { $query->where('completed', 1); }])
      ->first();

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

    /**
     * Find the first experiment matching the provided ID. With the count of how many images
     * are in every image sequence of experiment sequences of type picture queue.
     */
    public function find (Request $request)
    {
      return Experiment::with(
        [
          'user:id,name',
          'observer_metas.observer_meta',
          // for every picture set experiment_sequence get the count of pictures used
          'sequences' => function ($query) {
            $query->where('picture_queue_id', '!=', null)->with(['picture_queue' => function ($query) {
              $query->withCount('picture_sequence');
            }]);
          }
        ]
      )->find($request->id);
    }

    /**
     * Find the first PUBLIC experiment that matches the provided ID.
     */
    public function find_public (Request $request)
    {
      return Experiment::where([
        ['id', $request->id],
        ['is_public', 1]
      ])
      ->with('user:id,name', 'observer_metas.observer_meta')
      ->first();
    }

    public function all ()
    {
      return Experiment::with('user:id,name')
        ->orderBy('id', 'asc')
        ->get();
    }

    public function all_public ()
    {
      return Experiment::where('is_public', 1)
        ->with('user:id,name', 'observer_metas.observer_meta')
        ->orderBy('id', 'desc')
        ->get();
    }

    public function observer_metas (Request $request)
    {
      $metas = DB::table('experiment_observer_metas')
        ->join('observer_metas', 'observer_metas.id', '=',
          'experiment_observer_metas.observer_meta_id')
        ->where('experiment_observer_metas.experiment_id', $request->id)
        ->get();

      // ExperimentObserverMetas::with('observer_metas')
      //     ->where('experiment_id', $request->id);

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

      // TODO: replace with slug
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

            # generating a triplets queue only work with a certain number of images
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
        'ishihara'          => $request->ishihara,
        'artifact_marking'  => $request->artifact_marking,
        'stimuli_spacing'   => $request->stimuliSpacing,
        'short_description' => $request->shortDescription,
        'long_description'  => $request->longDescription,
        'is_public'         => $request->isPublic,
        'same_pair'         => $request->samePairTwice,
        'background_colour' => $request->bgColour,
        'show_original'     => $request->showOriginal,
        'show_progress'     => $request->showProgress,
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
          // $experiment->type->slug === 'category' || $experiment->type->slug === 'triplet'
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

          // TODO: check that the user owns the image set
          // TODO: check that the user owns the instruction
          // foreach ($request->sequences as $group_key => list($step)) {

          foreach ($request->sequences as $group_key => $group) {
            foreach ($group as $step)
            {
              if ($step['type'] === 'imageSet')
              {
                # THIS CAN BE REMOVED random within image set OR within and between image sets
                // if ($experiment->picture_sequence_algorithm === 1 || $experiment->picture_sequence_algorithm === 2)
                // {
                // $experiment->type->slug === 'paired'
                if ($experiment->experiment_type_id == 1)
                {
                  $picture_queue_id = $this->random_paired_queue(
                    $step['value'],
                    $experiment->same_pair
                  );
                }
                // $experiment->type->slug === 'triplet'
                else if ($experiment->experiment_type_id == 5)
                {
                  $picture_queue_id = $this->random_triplet_queue( $step['value'] );
                }
                else
                {
                  $picture_queue_id = $this->make_category_queue( $step['value'] );
                }

                $this->add_experiment_sequence(
                  $experiment_queue->id,
                  $picture_queue_id,
                  null,
                  $step['value'],
                  $step['randomize'],
                  $step['original'],
                  $step['flipped'],
                  $group[0]['randomizeGroup']
                );
                // }
              }
              else if ($step['type'] === 'instruction')
              {
                $instruction = \App\Instruction::create([
                  'user_id' => auth()->user()->id,
                  'description' => $step['value']
                ]);

                $this->add_experiment_sequence(
                  $experiment_queue->id,
                  null,
                  $instruction->id
                );
              }
              else if ($step['type'] === 'instructionFromHistory')
              {
                $this->add_experiment_sequence(
                  $experiment_queue->id,
                  null,
                  $step['value']
                );
              }

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
     * Generate a randomized queue for images in a triplet experiment.
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
     * Generate a randomized queue for images in a paired experiment.
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
     * Generate a randomized queue for images in a category experiment.
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

    protected function add_experiment_sequence (
      $experiment_queue_id,
      $picture_queue_id = null,
      $instruction_id = null,
      $picture_set_id = null,
      $randomize = null,
      $original = null,
      $flipped = null,
      $randomize_group = null
    ) {
      $experiment_sequence = \App\ExperimentSequence::create([
        'experiment_queue_id' => $experiment_queue_id,
        'picture_queue_id'    => $picture_queue_id,
        'instruction_id'      => $instruction_id,
        'picture_set_id'      => $picture_set_id,
        'randomize'           => $randomize,
        'randomize_group'     => $randomize_group,
        'original'            => $original,
        'flipped'             => $flipped
      ]);

      return $experiment_sequence;
    }

    /**
     * Begin the experiment for a observer. Returns the whole stimuli queue
     * for the experiment.
     */
    public function start (Experiment $experiment)
    {
      $sequences = \App\ExperimentQueue::with(['experiment_sequences' => function ($query) {
          $query->with([
            'picture_set.pictures' => function ($query) { $query->where('is_original', 1); },
            'picture_queue.picture_sequence.picture',
            'instruction'
          ]);
        }])
        ->where('experiment_id', '=', $experiment->id)
        ->get();

      # Group adjucent sequence types:
      # [instruction, imageSet, imageSet] into -> [[instruction], [imageSet, imageSet]]
      $current_type = '';
      $nounce = 0;
      foreach ($sequences[0]->experiment_sequences as $key => $sequence)
      {
        if ($sequence->instruction_id !== null) {
          if ($current_type != 'instruction') {
            ++$nounce;
            $all[$nounce] = [$sequence];
            $current_type = 'instruction';
          } else {
            $all[$nounce][] = $sequence;
          }
        }

        if ($sequence->picture_queue_id !== null) {
          $sequence['stimuli'] = $sequence->picture_queue->picture_sequence;

          if ($experiment->experiment_type_id == 1) {
            $sequence['stimuli'] = $sequence->picture_queue->picture_sequence->groupBy('picture_order');
          }

          if ($sequence->randomize == 1) {
            if ($experiment->experiment_type_id == 1) {
              $sequence['stimuli'] = collect($sequence['stimuli'])->shuffle();
            } else {
              $sequence['stimuli'] = $sequence->picture_queue->picture_sequence->shuffle();
            }
          }

          if ($current_type != 'imageSet') {
            ++$nounce;
            $all[$nounce] = [$sequence];
            $current_type = 'imageSet';
          } else {
            $all[$nounce][] = $sequence;
          }
        }
      }

      # randomize order of sequence groups if randomize_group is set to 1 for the group
      $sequence_groups = collect($all);
      $sequence_groups->transform(function ($group, $key) {
        if ($group[0]->randomize_group === 1) {
          return collect($group)->shuffle();
        } else {
          return collect($group);
        }
      });

      return response($sequence_groups);
    }

    /**
     * Set the experiment status to completed.
     */
    public function completed (Request $request, ExperimentResult $experiment) {
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

      if ($request->amountObservers > 0)
      {
        if ($original_experiment->first_version_id) {
          $last_version = Experiment::where('first_version_id', $original_experiment->first_version_id)
            ->latest()
            ->first();
        } else {
          $last_version = Experiment::where('first_version_id', $original_experiment->id)
            ->latest()
            ->first();
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
          'ishihara'          => $request->ishihara,
          'artifact_marking'  => $request->artifact_marking,
          'stimuli_spacing'   => $request->stimuliSpacing,
          'short_description' => $request->shortDescription,
          'long_description'  => $request->longDescription,
          'is_public'         => $request->isPublic,
          'same_pair'         => $request->samePairTwice,
          'background_colour' => $request->bgColour,
          'show_original'     => $request->showOriginal,
          'show_progress'     => $request->showProgress,
          'first_version_id'  => $original,
          'version'           => $version
        ]);
      } else {
        $experiment = Experiment::create([
          'user_id'           => auth()->user()->id,
          'title'             => $request->title,
          'experiment_type_id'=> $request->experimentType,
          'picture_sequence_algorithm' => $request->algorithm,
          'delay'             => $request->delay,
          'ishihara'          => $request->ishihara,
          'artifact_marking'  => $request->artifact_marking,
          'stimuli_spacing'   => $request->stimuliSpacing,
          'short_description' => $request->shortDescription,
          'long_description'  => $request->longDescription,
          'is_public'         => $request->isPublic,
          'same_pair'         => $request->samePairTwice,
          'background_colour' => $request->bgColour,
          'show_original'     => $request->showOriginal,
          'show_progress'     => $request->showProgress,
          'version'           => 1
        ]);

        // Delete original experiment
        // TODO: delete everything related
        Experiment::destroy($original_experiment->id);
      }

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

                $this->add_experiment_sequence(
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

              $this->add_experiment_sequence(
                $experiment_queue->id,
                null,
                $instruction->id,
                null
              );
            }
            else if ($step['type'] === 'instructionFromHistory')
            {
              // TODO: check that the user owns the instruction

              $this->add_experiment_sequence(
                $experiment_queue->id,
                null,
                $step['value'],
                null
              );
            }
          }

          return response($experiment, 201);
        } else {
          return response('Something went wrong. Experiment could not be updated.', 404);
        }
      } else {
        return response('Something went wrong. Experiment could not be updated.', 404);
      }

      // return response($experiment, 200);
    }

    /**
     * Set whether the experiment should be visible to the public or not.
     */
    public function visibility (Request $request, Experiment $experiment)
    {
      // if ($experiment->user_id !== auth()->user()->id) {
      //   return response()->json('Unauthorized', 401);
      // }

      $data = $request->validate([
        'is_public' => 'required'
      ]);

      $experiment->update($data);

      return response($experiment, 200);
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

      return response($experiment, 200);
    }
}
