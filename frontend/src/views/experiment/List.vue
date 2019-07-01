<template>
  <v-data-table
    :headers="headers"
    :items="experiments"
    item-key="id"
    class="elevation-1"
    hide-actions
  >
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

      experiments: []
    }
  },

  created () {
    this.getUsersExperiments()
  },

  methods: {
    getUsersExperiments () {
      this.$axios.get('/experiments').then(response => {
        // console.log(response)
        this.experiments = response.data
      }).catch(() => {
        //
      })
    },

    visibility (exp) {
      this.$axios.patch('/experiment/' + exp.id + '/visibility', {
        is_public: exp.is_public
      }).then(response => {
        if (response.data.is_public) {
          EventBus.$emit('experimentCreated', 'Experiment is now visible to the public')
        } else {
          EventBus.$emit('experimentCreated', 'Experiment is now only visible to you')
        }
      }).catch(() => {
        //
      })
    },

    destroy (exp) {
      if (confirm('Do you want to delete the experiment?')) {
        this.$axios.delete('/experiment/' + exp.id).then(response => {
          console.log(response.data)

          this.experiments.splice(exp, 1)

          // call notification
        }).catch(() => {
          //
        })
      }
    }
  }
}
</script>
