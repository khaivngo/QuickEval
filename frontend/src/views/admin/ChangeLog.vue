<template>
  <div class="pl-12 pr-12 pb-12 pt-6 flex-grow-1">
    <div class="mb-8 mt-6" style="width: 800px;">
      <h2 class="display-1">
        Changelog
      </h2>

      <h2 class="text-h6 mt-12 first font-weight-bold">
        Version [1.0.0]
      </h2>

      <h2 class="text-h5 mt-6 mb-4">
        Added
      </h2>
      <ul style="line-height: 1.6em;">
        <li>
          It's now possible to hide the images in a experiment after a specified amount of time.
          You can set different times for different image sets in one experiment.
          (Find feature here: under experiment steps click "more" on the right-hand side of a image set)
        </li>
        <li>Category judgment: Keyboard shortcuts is now shown for the select list and next-button, in form of icons.</li>
        <li>
          Options for original/flip pair/randomize images and sets have been moved into "experiment steps"
          so that they can be controlled on a step by step basis, not just globally.
          <!-- <v-btn
            text
            class="text-none ma-0"
            color="primary"
            @click="showItem = !showItem"
          >
            Read more
          </v-btn>
          <v-expand-transition>
            <ul v-if="showItem">
              <li>
                To achieve this the stimuli queue had to be rewritten. The new way
                is better if we want to add other stimuli types (video for example)
                in the future, or allow for more ways of generating different queues for different observers.
                This rewrite also fixes a problem when you had
                where if you set "show original" to true and had two image sets where only the
                first one had
                a original image, the queue wouldn't know a new image set was loaded, and the original
                from the first
                set would still be shown alongside the second image set.
              </li>
            </ul>
          </v-expand-transition> -->
        </li>
        <li>
          Heatmap export for artifact markings.
        </li>
        <li>
          An email is automatically sent out when a new user is registered.<br>
          One confirmation mail is sent to the new user.
          One notification mail is sent to Marius.
          And one "accepted/rejected" mail is sent to the user that has been approved or rejected.
        </li>
        <li>
          Remember the observer's experiment progress if they hit the browsers "refresh" button, instead of loading a new stimuli queue.
        </li>
        <li>
          Cookie concent prompt for the user.
        </li>
        <li>
          Cookie policy page. Where the user can also find the option for retract their concent.
          https://quickeval.no/cookies
        </li>
        <li>
          This changelog page.
        </li>
      </ul>

      <h2 class="text-h5 mt-6 mb-4">
        Changes
      </h2>
      <ul>
        <li>
          In rank order: small thumbnail images has been droppped, keeping just the letter as the draggable elements.
          This was done to simplify amount of visual elements, as well as improve loading times.
        </li>
      </ul>

      <h2 class="text-h5 mt-6 mb-4">
        Fixed
      </h2>
      <ul>
        <li>Paired comparison now works with only two images.</li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      showItem: false
    }
  }
}
</script>

<style scoped lang="css">
  li {
    margin-bottom: 0.8em;
  }
</style>
