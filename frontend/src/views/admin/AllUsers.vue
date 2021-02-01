<template>
  <div class="pl-12 pr-12 pb-12 pt-6 flex-grow-1">
    <div class="mb-8 mt-6">
      <h2 class="display-1">
        All users
      </h2>

      <v-data-table
        :loading="loading"
        :headers="headers"
        :items="users"
        no-data-text="No active users"
        hide-default-footer
        class="mt-12"
      >
        <template v-slot:item.action="{ item }">
          <v-select
            style="width: 150px;"
            v-model="item.role"
            :items="[
              { id: 2, title: 'Scientist' },
              { id: 3, title: 'Admin' }
            ]"
            item-text="title"
            item-value="id"
            @change="changeRole(item)"
            :loading="item.loading"
            hide-details
            outlined
            dense
          ></v-select>
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
        { text: 'Role', sortable: false, value: 'action' }
      ],

      users: [],

      loading: false,
      loaders: []
    }
  },

  created () {
    this.loading = true

    this.$axios.get(`/user/all`).then(response => {
      // add a prop to keep track of potential loading state before making the object array reactive with vue
      response.data.forEach(function (element) {
        element.loading = false
      })
      this.users = response.data

      this.loading = false
    })
  },

  methods: {
    async changeRole (user) {
      user.loading = true

      await this.$axios.patch(`/user/role`, {
        id: user.id,
        role: user.role
      })

      EventBus.$emit('success', 'User role successfully changed.')
      user.loading = false
    }
  }
}
</script>
