<template>
  <div>
    <v-container mt-3>
      <v-layout>
        <v-flex shrink mr-3 mt-5 mb-5>
          <Navigation/>
        </v-flex>
        <v-flex grow mt-2 ml-5 mr-5>
          <!-- <TransitionPage> -->
            <router-view/>
          <!-- </TransitionPage> -->
        </v-flex>
      </v-layout>
    </v-container>
  </div>
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
