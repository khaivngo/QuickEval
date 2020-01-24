<template>
  <v-container mt-3 fluid style="max-width: 1400px;">
    <v-layout justify-center>
      <v-flex shrink mr-3 mt-5 mb-5>
        <Navigation/>
      </v-flex>
      <v-flex mt-2 ml-5 mr-5>
        <!-- <TransitionPage> -->
        <router-view/>
        <!-- </TransitionPage> -->
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
import Navigation from '@/components/scientist/Navigation'
// import TransitionPage from '@/components/TransitionPage'

export default {
  components: {
    Navigation
    // ,
    // TransitionPage
  }
}
</script>
