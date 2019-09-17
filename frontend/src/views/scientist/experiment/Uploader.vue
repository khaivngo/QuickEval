<template>
  <div>
    <v-layout mb-5>
      <h2 class="display-1">
        Your Image Sets
      </h2>
    </v-layout>

    <v-layout justify-end mb-3>
      <v-btn color="success" class="ma-0" :to="'/scientist/image-sets/create'"> <!-- @click="createImageSet" :loading="creating" -->
        Create New
      </v-btn>
    </v-layout>

    <v-card>
      <v-container pl-5 pr-5 pt-2>
        <CreateImageSet/>
      </v-container>
    </v-card>
  </div>
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
