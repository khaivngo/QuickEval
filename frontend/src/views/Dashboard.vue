<template>
  <v-card>
    <v-container>
      <v-layout>
        <stat label="visitors" class="mr-5">
          22
        </stat>
        <stat label="completed">
          20
        </stat>
      </v-layout>
    </v-container>
  </v-card>
</template>

<script>
import stat from '@/components/Stat'

export default {
  components: {
    stat
  }
}
</script>
