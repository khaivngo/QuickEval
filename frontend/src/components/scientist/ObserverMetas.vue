<template>
  <v-container>
    <v-layout class="mb-3 mt-1" justify-center>
      <v-menu open-on-hover bottom offset-y>
        <template v-slot:activator="{ on }">
          <v-btn
            depressed
            v-on="on"
            class="text-none"
          >
            Add input field <v-icon class="ml-2" :size="20">add</v-icon>
          </v-btn>
        </template>

        <v-list>
          <v-list-tile @click="add('meta')">
            <v-list-tile-title right>
              <v-icon left small>create</v-icon>
              Create new
            </v-list-tile-title>
          </v-list-tile>
          <v-list-tile @click="add('metaFromHistory')">
            <v-list-tile-title right>
              <v-icon left small>add</v-icon>
              Add from history
            </v-list-tile-title>
          </v-list-tile>
        </v-list>
      </v-menu>
    </v-layout>

    <v-slide-x-transition group>
      <div
        v-for="(event, i) in timeline"
        :key="event.id"
        class="mb-3"
      >
        <v-layout justify-space-between>
          <v-flex xs12>
            <v-text-field
              v-if="event.type === 'meta'"
              label="Write input label"
              v-model="event.value"
              class="m-0"
              item-text="title"
              item-value="id"
            >
              <template v-slot:append-outer>
                <v-icon @click="remove(i)">delete</v-icon>
              </template>
            </v-text-field>

            <v-select
              v-if="event.type === 'metaFromHistory'"
              :items="items"
              label="Select input"
              v-model="event.value"
              item-text="meta"
              item-value="id"
            >
              <template v-slot:append-outer>
                <v-icon @click="remove(i)">delete</v-icon>
              </template>
            </v-select>
          </v-flex>
        </v-layout>
      </div>
    </v-slide-x-transition>
  </v-container>
</template>

<script>
export default {
  data: () => ({
    items: [], // existing observer metas
    events: [], // it all goes here
    input: null,
    nonce: 0
  }),

  computed: {
    timeline () {
      return this.events.slice().reverse()
    }
  },

  created () {
    this.$axios.get('/observer-metas')
      .then(json => { this.items = json.data })
      .catch(err => console.warn(err))
  },

  methods: {
    add (type) {
      this.events.push({
        id: this.nonce++,
        value: this.input,
        type: type
      })

      this.input = null

      this.$emit('added', this.events)
    },

    remove (id) {
      var index = (this.events.length - id) - 1

      this.events.splice(index, 1)
    }
  }
}
</script>
