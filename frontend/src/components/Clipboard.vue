<template>
  <v-layout align-center >
    <!-- <input type="text" v-model="url"> -->
    <!-- <v-text-field v-model="url" label="Invite to experiment"></v-text-field> -->
    <div>{{ url }}</div>
    <v-btn
      type="button"
      v-clipboard:copy="url"
      v-clipboard:success="onCopy"
      v-clipboard:error="onError"
      small
      flat
      class="ml-0 mt-0 mb-0 mr-0 pa-0 text-none"
    >
      {{ label }}
    </v-btn>
  </v-layout>
</template>

<script>
import Vue from 'vue'
import VueClipboard from 'vue-clipboard2'

Vue.use(VueClipboard)

export default {
  props: {
    url: {
      type: String,
      default: ''
    }
  },
  data () {
    return {
      label: 'Copy'
    }
  },
  methods: {
    onCopy (e) {
      // alert('You just copied: ' + e.text)
      this.label = 'Copied!'
      window.setTimeout(() => { this.label = 'Copy' }, 3000)
    },

    onError (e) {
      // alert('Failed to copy texts')
    }
  }
}
</script>
