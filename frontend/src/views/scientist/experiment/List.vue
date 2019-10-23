<template>
  <div>
    <v-layout justify-space-between mb-5>
      <h2 class="display-1">
        Your Experiments
      </h2>

      <!-- <v-btn color="success" class="ma-0">
        Create new
      </v-btn> -->
    </v-layout>

    <v-layout justify-end mb-3>
      <v-btn color="success" class="ma-0" :to="'/scientist/experiments/create'" exact>
        Create new
      </v-btn>
    </v-layout>

    <v-data-table
      :headers="headers"
      :items="experiments"
      no-data-text=""
      item-key="id"
      class="elevation-1"
      hide-actions
      :loading="loading"
    >
      <v-progress-linear v-slot:progress color="blue" indeterminate></v-progress-linear>
      <template v-slot:no-data>
        <div class="caption text-xs-center" v-if="loading === false">
          You have no experiments.
        </div>
      </template>
      <template v-slot:items="props">
        <td align="left">
          <div class="qe-experiment-link-container">
            <!-- <v-icon left>show_chart</v-icon> -->
            <!-- <v-icon left>bar_chart</v-icon> -->

            <router-link :to="`/scientist/experiments/view/${props.item.id}`">
              {{ props.item.title }}
            </router-link>
          </div>
        </td>
        <td align="right" class="public-switch text-xs-right">
          <v-layout justify-center>
            <v-switch
              class="ma-0 pa-0"
              v-model="props.item.is_public"
              color="success"
              @change="visibility(props.item)"
            ></v-switch>
          </v-layout>
        </td>
        <td align="left">
          <!-- <div class="qe-experiment-link-container"> -->
          <InviteLink :experiment-id="props.item.id"/>
          <!-- </div> -->
        </td>
        <td align="right">
          <!-- <v-btn :to="'/scientist/experiment/edit/' + props.item.id" flat icon>
            <v-icon>bar_chart</v-icon>
          </v-btn> -->

          <v-tooltip top>
            <template v-slot:activator="{ on }">
              <v-btn :to="'/scientist/experiments/edit/' + props.item.id" flat icon v-on="on">
                <v-icon>edit</v-icon>
              </v-btn>
            </template>
            <span>Edit</span>
          </v-tooltip>

          <v-tooltip top>
            <template v-slot:activator="{ on }">
              <v-btn flat icon @click="destroy(props.item, props.index)" v-on="on">
                <v-icon>delete</v-icon>
              </v-btn>
            </template>
            <span>Delete</span>
          </v-tooltip>
        </td>
      </template>
    </v-data-table>
  </div>
</template>

<script>
import EventBus from '@/eventBus'
import InviteLink from '@/components/scientist/InviteLink'

export default {
  components: {
    InviteLink
  },

  data () {
    return {
      headers: [
        { text: 'Title', value: 'name', align: 'left', sortable: false },
        { text: 'Visible to the public', value: 'public', sortable: false },
        { text: 'Shareable link', value: 'invite', sortable: false },
        { text: 'Actions', value: 'edit', align: 'right', sortable: false }
      ],

      experiments: [],

      loading: false
    }
  },

  created () {
    this.getUsersExperiments()
  },

  methods: {
    getUsersExperiments () {
      this.loading = true

      this.$axios.get('/experiments').then(response => {
        this.experiments = response.data

        this.loading = false
      }).catch(() => {
        this.loading = false
      })
    },

    visibility (exp) {
      this.$axios.patch('/experiment/' + exp.id + '/visibility', {
        is_public: exp.is_public
      }).then(response => {
        if (response.data.is_public) {
          EventBus.$emit('success', 'Experiment is now visible to the public')
        } else {
          EventBus.$emit('success', 'Experiment is now only visible to you')
        }
      })
    },

    destroy (exp, arrayIndex) {
      if (confirm('Do you want to delete the experiment?')) {
        this.$axios.delete(`/experiment/${exp.id}`).then(response => {
          if (response.data === 'deleted_experiment') {
            this.experiments.splice(arrayIndex, 1)

            EventBus.$emit('success', 'Experiment has been deleted successfully')
          } else {
            EventBus.$emit('error', 'Could not delete experiment')
          }
        })
      }
    }
  }
}
</script>

<style lang="css">
  /* NOTICE: the 'scoped' slot is omitted because it makes overriding Vuetify styles possible (for whatever reason) */

  /* Vuetify overriding styles, remove spacing beneath the public/hidden switches */
  .public-switch .v-input__slot {
    margin-bottom: 0 !important;
  }

  .public-switch .v-input .v-input__control .v-messages {
    display: none !important;
    height: 0px !important;
  }

  /*  */
  .qe-experiment-link-container {
    display: flex;
    align-items: center;
    height: 100%;
  }
</style>
