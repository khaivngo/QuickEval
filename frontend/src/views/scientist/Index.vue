<template>
  <v-row class="fill-height pa-0" no-gutters >
    <v-col cols="auto">
      <Navigation style="margin-top: 64px;"/>
    </v-col>
    <v-col>
      <router-view/>
    </v-col>
  </v-row>
</template>

<script>
import Navigation from '@/components/scientist/Navigation'

export default {
  components: {
    Navigation
  }
}
</script>
