<template>
  <div>
    <v-layout mb-5 mt-5>
      <h2 class="display-1">
        Scientist role requests
      </h2>
    </v-layout>

    <v-card>
      <!-- <v-container> -->
      <v-data-table
        :loading="loadingRequests"
        :headers="headers"
        :items="requests"
        :hide-actions="true"
        no-data-text=""
      >
        <template v-slot:no-data>
          <div class="caption text-xs-center" v-if="loadingRequests === false">
            Currently no requests pending.
          </div>
        </template>
        <template v-slot:items="props">
          <td>{{ props.item.name }}</td>
          <td>{{ props.item.email }}</td>
          <td>{{ props.item.institution }}</td>
          <td>{{ props.item.nationality }}</td>
          <!-- <td>{{ props.item.created_at }}</td> -->
          <td class="text-xs-right">
            <v-btn
              :loading="loading"
              :disabled="loading"
              color="success"
              @click="accept(props.item.id, props.index)"
              small
            >
              Approve
            </v-btn>

            <v-btn
              :loading="loading"
              :disabled="loading"
              @click="reject(props.item.id, props.index)"
              small
              class="mr-0"
            >
              Decline
            </v-btn>
          </td>
        </template>
      </v-data-table>
      <!-- </v-container> -->
    </v-card>
  </div>
</template>

<script>
import EventBus from '@/eventBus'

export default {
  data () {
    return {
      headers: [
        { text: 'Name', sortable: false, value: 'name' },
        { text: 'Email', sortable: false, value: 'email' },
        { text: 'Institution', sortable: false, value: 'institution' },
        { text: 'Nationality', sortable: false, value: 'nationality' },
        // { text: 'When', sortable: false, value: 'when' },
        { text: 'Actions', sortable: false, value: 'action', align: 'right' }
      ],

      requests: [],

      loading: false,
      loadingRequests: false
    }
  },

  created () {
    this.loadingRequests = true
    this.$axios.get(`/scientist-request`).then(response => {
      this.requests = response.data
      this.loadingRequests = false
    })
  },

  methods: {
    async accept (id, index) {
      await this.$axios.post(`/scientist-request/${id}/accept`)
      this.requests.splice(index, 1)
      EventBus.$emit('success', 'Request accepted successfully.')
    },

    async reject (id, index) {
      await this.$axios.post(`/scientist-request/${id}/reject`)
      this.requests.splice(index, 1)
    }
  }
}
</script>
