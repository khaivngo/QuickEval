<template>
  <!-- <p class="subheading"> -->
    <!-- <a :href="`${$DOMAIN}/observer/${experimentId}`"> -->
    <div style="display: flex; align-items: center;">
      {{ $DOMAIN }}/observer/{{ experimentId }}
      <!-- <v-icon class="ml-1">insert_link</v-icon> -->
    </div>
    <!-- </a> -->
  <!-- </p> -->
</template>

<script>
export default {
  props: {
    experimentId: {
      type: Number,
      default: null
    }
  }
}
</script>
