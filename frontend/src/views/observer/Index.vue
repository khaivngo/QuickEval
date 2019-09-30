<template>
  <div>
    <v-container>
      <v-layout justify-center>
        <v-flex xs12 sm12 md10 xl8>
          <ExperimentSelector/>
        </v-flex>
      </v-layout>
    </v-container>
  </div>
</template>

<script>
import ExperimentSelector from '@/components/observer/ExperimentSelector'

export default {
  components: {
    ExperimentSelector
  }
}
</script>
