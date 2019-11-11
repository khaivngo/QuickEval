<template>
  <v-container mt-3 fluid style="max-width: 1400px;">
    <v-layout>
      <v-flex shrink mr-3 mt-5 mb-5>
        <Navigation/>
      </v-flex>
      <v-flex grow mt-2 ml-5 mr-5>
        <!-- <transition
          mode="out-in"
          appear
          name="custom-classes-transition"
          enter-active-class="animated slideInLeft"
        > -->
        <router-view/>
        <!-- </transition> -->
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
import Navigation from '@/components/admin/Navigation'

export default {
  components: {
    Navigation
  }
}
</script>
