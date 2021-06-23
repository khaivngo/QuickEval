<template>
  <div class="pl-12 pr-12 pb-12 pt-6 flex-grow-1">
    <div class="mb-8 mt-6">
      <h2 class="display-1">
        Scientist role requests
      </h2>

      <v-data-table
        :loading="loadingRequests"
        :headers="headers"
        :items="requests"
        no-data-text="No pending requests"
        hide-default-footer
        class="mt-12"
      >
        <template v-slot:item.action="{ item }">
          <v-btn
            :loading="item.accepting"
            :disabled="item.accepting"
            color="success"
            class="mr-4"
            @click="accept(item)"
            small
          >
            Approve
          </v-btn>

          <v-btn
            :loading="item.rejecting"
            :disabled="item.rejecting"
            @click="reject(item)"
            small
            class="mr-0"
          >
            Decline
          </v-btn>
        </template>
      </v-data-table>
    </div>
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
        { text: 'Actions', sortable: false, value: 'action' }
      ],

      requests: [],

      loading: false,
      loadingRequests: false
    }
  },

  created () {
    this.loadingRequests = true

    this.$axios.get(`/scientist-request`).then(response => {
      response.data.forEach(request => {
        request.accepting = false
        request.rejecting = false
      })

      this.requests = response.data
    }).catch((error) => {
      console.log(error)
    }).finally(() => {
      this.loadingRequests = false
    })
  },

  methods: {
    accept (request) {
      request.accepting = true

      this.$axios.post(`/scientist-request/${request.id}/accept`).then(response => {
        console.log(response)
      }).catch((error) => {
        console.log(error)
      }).finally(() => {
        const itemToRemoveIndex = this.requests.findIndex(function (item) {
          return item.id === request.id
        })

        if (itemToRemoveIndex !== -1) {
          this.requests.splice(itemToRemoveIndex, 1)
        }

        EventBus.$emit('success', 'Request accepted successfully.')

        request.accepting = false
      })
    },

    reject (request) {
      request.rejecting = true

      this.$axios.post(`/scientist-request/${request.id}/reject`).then(response => {
        console.log(response)
      }).catch((error) => {
        console.log(error)
      }).finally(() => {
        const itemToRemoveIndex = this.requests.findIndex(function (item) {
          return item.id === request.id
        })

        if (itemToRemoveIndex !== -1) {
          this.requests.splice(itemToRemoveIndex, 1)
        }

        request.rejecting = false
      })
    }
  }
}
</script>
