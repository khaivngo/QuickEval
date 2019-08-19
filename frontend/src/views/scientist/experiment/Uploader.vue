<template>
  <v-card>
    <v-card-title class="justify-center pt-5 pb-0">
      <h2 class="mb-4">Manage Image Set</h2>
    </v-card-title>

    <v-container pl-5 pr-5 pb-5 pt-2>
      <CreateImageSet/>
    </v-container>
  </v-card>
</template>

<script>
import CreateImageSet from '@/components/scientist/CreateImageSet'

export default {
  components: {
    CreateImageSet
  },

  data () {
    return {
      //
    }
  },

  methods: {
    //
  }
}
</script>
