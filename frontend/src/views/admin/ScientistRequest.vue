<template>
  <div>
    <v-layout mb-5>
      <h2 class="display-1">
        Scientist role requests
      </h2>
    </v-layout>

    <v-card>
      <!-- <v-container> -->
      <v-data-table
        :headers="headers"
        :items="requests"
        :hide-actions="true"
        no-data-text=""
      >
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
              @click="accept(props.item.id)"
              small
            >
              Approve
            </v-btn>

            <v-btn
              :loading="loading"
              :disabled="loading"
              @click="reject(props.item.id)"
              small
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

      loading: false
    }
  },

  created () {
    this.$axios.get(`/scientist-request`).then(response => {
      this.requests = response.data
    })
  },

  methods: {
    accept (id) {
      this.$axios.post(`/scientist-request/${id}/accept`).then(response => {
        console.log(response)
      })
    },

    reject (id) {
      this.$axios.post(`/scientist-request/${id}/reject`).then(response => {
        console.log(response)
      })
    }
  }
}
</script>
