<template>
  <div>
    <h2 class="display-2 font-weight-medium">
      <slot></slot>
    </h2>
    <h5 class="subheading text-uppercase">
      {{ label }}
    </h5>
  </div>
</template>

<script>
export default {
  props: {
    label: {
      type: String,
      default: ''
    }
  }
}
</script>
