<template>
  <div>
    <v-container mt-3>
      <v-layout wrap justify-center>
        <!-- <v-flex shrink class="mr-5"> -->
        <v-flex shrink mr-3>
          <Navigation/>
        </v-flex>
        <v-flex grow xl5 md8 sm12 xs12 mt-2>
            <!-- <transition
              mode="out-in"
              appear name="custom-classes-transition"
              enter-active-class="animated fadeIn"
            > -->
            <router-view/>
            <!-- </transition> -->
        </v-flex>
      </v-layout>
    </v-container>
  </div>
</template>

<script>
import Navigation from '@/components/scientist/Navigation'

export default {
  components: {
    Navigation
  },

  created () {
    // this.$axios.get('http://localhost:8080/api/images').then((response) => {
    //   console.log(response)
    // })
  }
}
</script>
