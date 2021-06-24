<template>
  <div class="pl-12 pr-12 pb-12 pt-6 flex-grow-1">
    <div class="mb-8 mt-6" style="width: 800px;">
      <h2 class="display-1">
        Changelog
      </h2>

      <h2 class="text-h6 mt-12 mb-4 first font-weight-bold">
        23. jun. 2021
      </h2>
      <v-simple-table>
        <template v-slot:default>
          <tbody>
            <tr>
              <!-- <td class="pa-0">
                <v-chip small color="default" class="qe-chip">scientist</v-chip>
              </td> -->
              <td>
                <v-chip outlined small color="success" class="qe-chip">added</v-chip>
              </td>
              <td class="pt-3 pb-3">
                User can delete their account. Under account settings.
              </td>
            </tr>
            <tr>
              <!-- <td class="pa-0">
                <v-chip small color="default" class="qe-chip">scientist</v-chip>
              </td> -->
              <td>
                <v-chip outlined small color="success" class="qe-chip">added</v-chip>
              </td>
              <td class="pt-3 pb-3">
                Settings for switching between standard and Montag confidence interval when calculating statistics.
              </td>
            </tr>
            <tr>
              <!-- <td class="pa-0">
                <v-chip small color="default" class="qe-chip">observer</v-chip>
              </td> -->
              <td>
                <v-chip outlined small color="success" class="qe-chip">added</v-chip>
              </td>
              <td class="pt-3 pb-3">
                Handle cases where the observer goes next in a experiment but their internet is out.
              </td>
            </tr>
            <tr>
              <!-- <td class="pa-0">
                <v-chip small color="default" class="qe-chip">observer</v-chip>
              </td> -->
              <td>
                <v-chip outlined small color="primary" class="qe-chip">changed</v-chip>
              </td>
              <td class="pt-3 pb-3">
                Remove the "next"-button in paired experiments, and instead proceed to the next step automatically when
                selecting a radio input. Add keyboard icons as well.
              </td>
            </tr>
            <tr>
              <!-- <td class="pa-0">
                <v-chip small color="default" class="qe-chip">admin</v-chip>
              </td> -->
              <td>
                <v-chip outlined small color="primary" class="qe-chip">changed</v-chip>
              </td>
              <td class="pt-3 pb-3">
                Use a date format in the changelog, instead of version numbering. For simplicity. Since we don't have a proper release
                schedule. Layout clean up.
              </td>
            </tr>
            <tr>
              <!-- <td class="pa-0">
                <v-chip small color="default" class="qe-chip">admin</v-chip>
              </td> -->
              <td>
                <v-chip outlined small color="primary" class="qe-chip">changed</v-chip>
              </td>
              <td class="pt-3 pb-3">
                Moved changelog fram admin panel to scientist panel.
              </td>
            </tr>
            <tr>
              <!-- <td class="pa-0">
                <v-chip small color="default" class="qe-chip">scientist</v-chip>
              </td> -->
              <td>
                <v-chip outlined small color="primary" class="qe-chip">changed</v-chip>
              </td>
              <td class="pt-3 pb-3">
                Ishihara test page: Move step counter next to "next"-button so it is more visible. And set "quit"-button to a
                fixed viewport position, keeping it in view even if user has to scroll.
              </td>
            </tr>
            <tr>
              <!-- <td class="pa-0">
                <v-chip small color="default" class="qe-chip">admin</v-chip>
              </td> -->
              <td>
                <v-chip outlined small color="error" class="qe-chip">fixed</v-chip>
              </td>
              <td class="pt-3 pb-3">
                Remove loading spinner even if confirmation email script fails. And make a loading prop for each button,
                instead of a global loading prop.
              </td>
            </tr>
            <tr>
              <!-- <td class="pa-0">
                <v-chip small color="default" class="qe-chip">scientist</v-chip>
              </td> -->
              <td>
                <v-chip outlined small color="error" class="qe-chip">fixed</v-chip>
              </td>
              <td class="pt-3 pb-3">
                When editing experiment: The checkboxes next to the image sets will not show as checked when they in reality aren't.
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>

      <h2 class="text-h6 mt-12 mb-4 first font-weight-bold">
        11. jun. 2021
      </h2>
      <v-simple-table>
        <template v-slot:default>
          <tbody>
            <tr>
              <td>
                <v-chip outlined small color="success" class="qe-chip">added</v-chip>
              </td>
              <td class="pt-3 pb-3">
                Randomize left/right image position in experiments.
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>

      <h2 class="text-h6 mt-12 mb-4 first font-weight-bold">
        11. apr. 2021
      </h2>
      <v-simple-table>
        <template v-slot:default>
          <tbody>
            <tr>
              <td>
                <v-chip outlined small color="primary" class="qe-chip">changed</v-chip>
              </td>
              <td class="pt-3 pb-3">
                Calculate whether the experiment was finished or not based on whether the number of comparisons for each session matches
                the experiments total (instead of marking the experiment as completed on the last step).
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>

      <h2 class="text-h6 mt-12 mb-4 first font-weight-bold">
        13. may 2021
      </h2>
      <v-simple-table>
        <template v-slot:default>
          <tbody>
            <tr>
              <td>
                <v-chip outlined small color="success" class="qe-chip">added</v-chip>
              </td>
              <td class="pt-3 pb-3">
                Remove the "next"-button in paired comparison. And show keyboard shortcut icons next to radio buttons.
              </td>
            </tr>
            <tr>
              <td>
                <v-chip outlined small color="success" class="qe-chip">added</v-chip>
              </td>
              <td class="pt-3 pb-3">
                Buttons for calculating confidence intervals the Montag way or a standard way.
              </td>
            </tr>
            <tr>
              <td>
                <v-chip outlined small color="success" class="qe-chip">added</v-chip>
              </td>
              <td class="pt-3 pb-3">
                It's now possible to hide the images in a experiment after a specified amount of time.
                You can set different times for different image sets in one experiment.
                (Find feature here: under experiment steps click "more" on the right-hand side of a image set)
              </td>
            </tr>
            <tr>
              <td>
                <v-chip outlined small color="success" class="qe-chip">added</v-chip>
              </td>
              <td class="pt-3 pb-3">
                Category judgment: Keyboard shortcuts is now shown for the select list and next-button, in form of icons.
              </td>
            </tr>
            <tr>
              <td>
                <v-chip outlined small color="success" class="qe-chip">added</v-chip>
              </td>
              <td class="pt-3 pb-3">
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
              </td>
            </tr>
            <tr>
              <td>
                <v-chip outlined small color="success" class="qe-chip">added</v-chip>
              </td>
              <td class="pt-3 pb-3">
                Heatmap export for artifact markings.
              </td>
            </tr>
            <tr>
              <td>
                <v-chip outlined small color="success" class="qe-chip">added</v-chip>
              </td>
              <td class="pt-3 pb-3">
                An email is automatically sent out when a new user is registered.<br>
                One confirmation mail is sent to the new user.
                One notification mail is sent to Marius.
                And one "accepted/rejected" mail is sent to the user that has been approved or rejected.
              </td>
            </tr>
            <tr>
              <td>
                <v-chip outlined small color="success" class="qe-chip">added</v-chip>
              </td>
              <td class="pt-3 pb-3">
                Remember the observer's experiment progress if they hit the browsers "refresh" button, instead of loading a new stimuli queue.
              </td>
            </tr>
            <tr>
              <td>
                <v-chip outlined small color="success" class="qe-chip">added</v-chip>
              </td>
              <td class="pt-3 pb-3">
                Cookie concent prompt for the user.
              </td>
            </tr>
            <tr>
              <td>
                <v-chip outlined small color="success" class="qe-chip">added</v-chip>
              </td>
              <td class="pt-3 pb-3">
                Cookie policy page. Where the user can also find the option for retract their concent.
                https://quickeval.no/cookies
              </td>
            </tr>
            <tr>
              <td>
                <v-chip outlined small color="success" class="qe-chip">added</v-chip>
              </td>
              <td class="pt-3 pb-3">
                Changelog page.
              </td>
            </tr>
            <tr>
              <td>
                <v-chip outlined small color="primary" class="qe-chip">changed</v-chip>
              </td>
              <td class="pt-3 pb-3">
                In rank order: small thumbnail images has been droppped, keeping just the letter as the draggable elements.
                This was done to simplify amount of visual elements, as well as improve loading times.
              </td>
            </tr>
            <tr>
              <td>
                <v-chip outlined small color="error" class="qe-chip">fixed</v-chip>
              </td>
              <td class="pt-3 pb-3">
                Paired comparison now works with only two images.
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>

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

  .qe-list {
    line-height: 1.6em;
  }

  .qe-chip {
    height: 20px;
    padding-left: 8px;
    padding-right: 8px;
  }

  .qe-date-label {
    color: #999;
  }
</style>
