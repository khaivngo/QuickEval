<template>
  <div class="pl-12 pr-12 pb-12 pt-6 flex-grow-1">
    <div class="mb-8 mt-6">
      <h2 class="display-1">
        Dashboard
      </h2>
    </div>

    <!-- <div class="d-flex"> -->
    <h3 class="text-h6 mt-12">Overview</h3>
    <v-data-table
      :loading="loading"
      :headers="headers"
      :items="experiments"
      no-data-text="No experiments created"
      class="mt-4"
      hide-default-footer
      :items-per-page="200"
    ></v-data-table>
    <!-- </div> -->

    <!-- <table class="table bordered hovered">
      <thead>
        <tr>
          <th class="overflow-wrap">Experiment Title</th>
          <th class="overflow-wrap">Visitors</th>
          <th class="overflow-wrap">Visitors Completed</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(experiment, i) in experiments" :key="i">
          <td class="overflow-wrap">{{ experiment.title }}</td>
          <td>{{ experiment.results_count }}</td>
          <td>{{ experiment.completed_results_count }}</td>
        </tr>
      </tbody>
    </table> -->
  </div>
</template>

<script>
export default {
  data () {
    return {
      headers: [
        { text: 'Experiment Title', sortable: false, value: 'title' },
        { text: 'Visitors', sortable: true, value: 'results_count' },
        { text: 'Completed', sortable: true, value: 'completed_results_count' }
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
    }
  }
}
</script>

<style scoped lang="css">
  .table td, .table tr {
    padding: 5px 5px;
  }
  .table tr {
    border: 1px solid #ddd;
  }
  .table.bordered {
    border: 1px solid rgba(0,0,0,0.12);
    border-left: 0;
  }
  .table {
    width: 100%;
    margin-bottom: 14pt;
  }
  .table {
    max-width: 100%;
    background-color: #ffffff;
    border-collapse: collapse;
    border-spacing: 0;
  }
  .table.bordered td, .table.bordered th {
    border-left: 1px solid rgba(0,0,0,0.12);
    border-bottom: 1px solid rgba(0,0,0,0.12);
    padding: 10px;
    max-width: 50px;
  }
  .table.bordered tr:hover {
    background: #eee;
  }
  .overflow-wrap {
    overflow-wrap: break-word;
  }
</style>
