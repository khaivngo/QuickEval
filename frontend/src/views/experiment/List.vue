<template>
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
    <template v-slot:items="props">
      <td>
        <router-link :to="'/scientist/experiment/experiments/' + props.item.id">
          {{ props.item.title }}
        </router-link>
      </td>
      <td>
        <v-switch
          class="mt-0"
          v-model="props.item.is_public"
          color="success"
          @change="visibility(props.item)"
        ></v-switch>
      </td>
      <td align="right">
        <v-btn :to="'/scientist/experiment/edit/' + props.item.id" flat icon>
          <v-icon>edit</v-icon>
        </v-btn>

        <v-btn flat icon @click="destroy(props.item)">
          <v-icon>delete</v-icon>
        </v-btn>
      </td>
    </template>
  </v-data-table>
</template>

<script>
import EventBus from '@/eventBus'

export default {
  data () {
    return {
      headers: [
        { text: 'Name', value: 'name', align: 'left', sortable: true },
        { text: 'Public', value: 'public' },
        { text: '', value: 'edit', align: 'right' }
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
      }).catch(() => {
        //
      })
    },

    destroy (exp) {
      if (confirm('Do you want to delete the experiment?')) {
        this.$axios.delete('/experiment/' + exp.id).then(response => {
          console.log(response.data)

          // this.experiments.splice(exp, 1)

          // call notification
        }).catch(() => {
          //
        })
      }
    }
  }
}
</script>
