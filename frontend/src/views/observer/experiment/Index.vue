<template>
  <div>
    <template v-if="type === 1">
    </template>
  </div>
</template>

<script>
export default {
  data () {
    return {
      type: null
    }
  },
  created () {
    this.type = this.$route.params
  }
}
</script>
