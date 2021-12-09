<template>
  <div class="pl-12 pr-12 pb-12 pt-6 flex-grow-1">
    <div class="mb-8 mt-6">
      <h2 class="display-1">
        Dashboard
      </h2>
    </div>

    <v-row class="qe-border-bottom">
      <v-col cols="auto" class="qe-border-right">
        <h2 class="title pa-4" style="width: 180px;  text-align: right">
          Users
        </h2>
      </v-col>

      <v-col>
        <div class="pa-4">
          <div class="text-h5 font-weight-bold">{{ counts.users }}</div>
          <div>Scientists</div>
        </div>
      </v-col>
    </v-row>

    <v-row class="qe-border-bottom">
      <v-col cols="auto" class="qe-border-right">
        <h2 class="title pa-4" style="width: 180px;  text-align: right">
          Experiments
        </h2>
      </v-col>

      <v-col>
        <div class="d-flex">
          <div class="pa-4">
            <div class="text-h5 font-weight-bold">{{ counts.experiments }}</div>
            <div>Created</div>
          </div>
          <div class="pa-4">
            <div class="text-h5 font-weight-bold">{{ counts.experimentResults }}</div>
            <div>Completed</div>
          </div>
          <div class="pa-4">
            <div class="text-h5 font-weight-bold">{{ counts.resultsTotal }}</div>
            <div>Stimuli evaluated</div>

            <div class="d-flex">
              <div class="pa-4 pl-0">
                <div class="subtitle-1 font-weight-bold">{{ counts.resultsPaired }}</div>
                <div class="body-2">Paired</div>
              </div>
              <div class="pa-4">
                <div class="subtitle-1 font-weight-bold">{{ counts.resultsCategory }}</div>
                <div class="body-2">Category</div>
              </div>
              <div class="pa-4">
                <div class="subtitle-1 font-weight-bold">{{ counts.resultsMagnitude }}</div>
                <div class="body-2">Magnitude</div>
              </div>
              <div class="pa-4">
                <div class="subtitle-1 font-weight-bold">{{ counts.resultsTriplet }}</div>
                <div class="body-2">Triplet</div>
              </div>
              <div class="pa-4">
                <div class="subtitle-1 font-weight-bold">{{ counts.resultRankOrder }}</div>
                <div class="body-2">Rank order</div>
              </div>
            </div>
          </div>
        </div>
      </v-col>
    </v-row>

    <v-row class="qe-border-bottom">
      <v-col cols="auto" class="qe-border-right">
        <h2 class="title pa-4" style="width: 180px; text-align: right;">
          Stimuli
        </h2>
      </v-col>

      <v-col>
        <div class="pa-4">
          <div class="text-h5 font-weight-bold">{{ counts.pictures }}</div>
          <div>Images/videos</div>
        </div>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  data () {
    return {
      loading: false,

      counts: []
    }
  },
  created () {
    this.loading = true

    this.$axios.get(`/statistics-counts`).then(response => {
      // add a prop to keep track of potential loading state before making the object array reactive with vue
      this.counts = response.data

      this.loading = false
    })
  },
  methods: {
    // getUsersExperiments () {
    //   this.loading = true

    //   this.$axios.get('/experiments').then(response => {
    //     this.experiments = response.data

    //     this.loading = false
    //   }).catch(() => {
    //     this.loading = false
    //   })
    // }
  }
}
</script>

<style scoped>
  .qe-border-bottom {
    border-bottom: 1px solid #ddd;
  }
  .qe-border-right {
    border-right: 1px solid #ddd;
  }
</style>
