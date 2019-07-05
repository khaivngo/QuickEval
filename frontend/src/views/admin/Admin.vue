<template>
  <div>
    <v-container>
      <v-layout wrap justify-center>
        <v-flex shrink class="mr-5">
          <Navigation/>
        </v-flex>
        <v-flex grow xl5 md8 sm12 xs12>
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
import Navigation from '@/components/admin/Navigation'

export default {
  components: {
    Navigation
  }
}
</script>
