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
      text
      class="ml-4 mt-0 mb-0 mr-0 pa-0"
    >
      <template v-if="label === 'Copy'">
        <v-icon class="mr-1" small>mdi-content-copy</v-icon> {{ label }}
      </template>
      <template v-if="label === 'Copied!'">
        <v-icon class="mr-1" small color="green">mdi-checkbox-marked-circle-outline</v-icon> <span style="color: green;">{{ label }}</span>
      </template>
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
