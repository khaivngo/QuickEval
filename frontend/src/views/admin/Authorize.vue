<template>
  <v-card>
    <v-container>
      <v-layout mb-5>
        <h1 class="headline">
          Scientist role requests
        </h1>
      </v-layout>

      <v-data-table
        :headers="headers"
        :items="requests"
        :hide-actions="true"
      >
        <template v-slot:items="props">
          <td>{{ props.item.name }}</td>
          <td>{{ props.item.email }}</td>
          <td>{{ props.item.created_at }}</td>
          <td class="text-xs-right">
            <v-btn
              :loading="loading"
              :disabled="loading"
              color="success"
              @click="approve(props.item.id)"
            >
              Approve
            </v-btn>

            <v-btn
              :loading="loading"
              :disabled="loading"
              color="error"
              @click="decline(props.item.id)"
            >
              Decline
            </v-btn>
          </td>
        </template>
      </v-data-table>
    </v-container>
  </v-card>
</template>

<script>
export default {
  data () {
    return {
      headers: [
        { text: 'Name', sortable: false, value: 'name' },
        { text: 'Email', sortable: false, value: 'email' },
        { text: 'When', sortable: false, value: 'when' },
        { text: '', sortable: false, value: 'action' }
      ],

      requests: [
        { name: 'Per Hansen', email: 'wadad', created_at: '22.343.12' },
        { name: 'Live Nelvik', email: 'wadad', created_at: '22.343.12' },
        { name: 'Ahmed Bah', email: 'wadad', created_at: '22.343.12' }
      ],

      loading: false
    }
  },

  methods: {
    approve () {
      //
    },

    decline () {
      //
    }
  }
}
</script>
