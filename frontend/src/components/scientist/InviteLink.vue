<template>
  <!-- <p class="subheading"> -->
    <!-- <a :href="`${$domain}/observer/${experimentId}`"> -->
    <div>
      {{ $domain }}/observer/{{ experimentId }}
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
