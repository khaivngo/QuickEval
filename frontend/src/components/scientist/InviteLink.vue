<template>
  <p class="subheading">
    Invite link:
    <a :href="$domain + '/observer/' + code">
      {{ $domain }}/observer/{{ code }}
    </a>
  </p>
</template>

<script>
export default {
  props: {
    code: {
      type: String,
      default: ''
    }
  }
}
</script>
