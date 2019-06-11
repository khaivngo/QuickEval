<template>
  <v-data-table
    :headers="headers"
    :items="experiments"
    item-key="title"
    class="elevation-1"
    hide-actions
  >
    <template v-slot:items="props">
      <td>
        <router-link :to="'/scientist/experiment/experiments/' + props.item.id">
          {{ props.item.title }}
        </router-link>
      </td>
      <td>{{ props.item.public ? 'yes' : 'no' }}</td>
      <td align="right">
        <v-btn :to="'/scientist/experiment/edit/' + props.item.id" flat icon>
          <v-icon>edit</v-icon>
        </v-btn>
      </td>
    </template>
  </v-data-table>
</template>

<script>
export default {
  data () {
    return {
      headers: [
        { text: 'Name', value: 'name', align: 'left', sortable: true },
        { text: 'Public', value: 'public' },
        { text: 'Action', value: 'edit', align: 'right' }
      ],

      experiments: [
        { id: 309, title: 'Experiment 1', public: true },
        { id: 23, title: 'Experiment 2', public: true },
        { id: 45, title: 'Experiment 3', public: false }
      ]
    }
  }
}
</script>
