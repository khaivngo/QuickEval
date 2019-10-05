<template>
  <p class="subheading">
    Invite link:
    <a :href="$domain + '/observer/' + code">
      {{ $domain }}/experiment/{{ code }}
    </a>
  </p>
</template>

<script>
export default {
  props: {
    code: {
      type: Number,
      default: 0
    }
  }
}
</script>
