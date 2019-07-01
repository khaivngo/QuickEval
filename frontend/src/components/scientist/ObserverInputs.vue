<template>
  <v-container>
    <v-slide-x-transition group>
      <div
        v-for="(event, i) in timeline"
        :key="event.id"
        class="mb-3"
      >
        <v-layout justify-space-between>
          <v-flex xs12>
            <v-text-field
              v-if="event.type === 'category'"
              label="Write category"
              v-model="event.text"
              class="m-0"
            >
              <template v-slot:append-outer>
                <v-icon @click="remove(i)">delete</v-icon>
              </template>
            </v-text-field>

            <v-select
              v-if="event.type === 'historyCategory'"
              :items="items"
              label="Select category"
            >
              <template v-slot:append-outer>
                <v-icon @click="remove(i)">delete</v-icon>
              </template>
            </v-select>
          </v-flex>
        </v-layout>
      </div>
    </v-slide-x-transition>

    <v-layout class="mb-5" justify-center>
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
          <v-list-tile @click="add('category')">
            <v-list-tile-title right>
              <v-icon left small>create</v-icon>
              Create new
            </v-list-tile-title>
          </v-list-tile>
          <v-list-tile @click="add('historyCategory')">
            <v-list-tile-title right>
              <v-icon left small>add</v-icon>
              Add from history
            </v-list-tile-title>
          </v-list-tile>
        </v-list>
      </v-menu>
    </v-layout>
  </v-container>
</template>

<script>
export default {
  data: () => ({
    items: [],
    events: [],
    input: null,
    nonce: 0
  }),

  computed: {
    timeline () {
      return this.events.slice().reverse()
    }
  },

  created () {
    this.items.push('height')
    this.items.push('age')
  },

  methods: {
    add (type) {
      this.events.push({
        id: this.nonce++,
        text: this.input,
        type: type
      })

      this.input = null
    },

    remove (id) {
      var index = (this.events.length - id) - 1

      this.events.splice(index, 1)
    }
  }
}
</script>
