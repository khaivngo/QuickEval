<template>
  <div>
    <v-layout class="mb-8 mt-1">
      <v-btn
        color="info"
        @click="add('meta')"
      >
        <v-icon class="mr-2" :size="20">mdi-plus-circle-outline</v-icon> input field
      </v-btn>

      <v-btn @click="add('metaFromHistory')" icon>
        <v-icon>mdi-history</v-icon>
      </v-btn>
    </v-layout>

    <v-slide-x-transition group>
      <div
        v-for="(event, i) in timeline"
        :key="event.id"
        class="mb-3"
      >
        <v-layout justify-space-between>
          <v-flex xs12 class="mt-4">
            <v-text-field
              v-if="event.type === 'meta'"
              label="Input label"
              v-model="event.value"
              class="m-0"
              item-text="title"
              item-value="id"
              outlined
              dense
            >
              <template v-slot:append-outer>
                <v-icon @click="remove(i)">mdi-delete</v-icon>
              </template>
            </v-text-field>

            <v-select
              v-if="event.type === 'metaFromHistory'"
              :items="items"
              label="Select input label"
              v-model="event.value"
              item-text="meta"
              item-value="id"
              outlined
              dense
            >
              <template v-slot:append-outer>
                <v-icon @click="remove(i)">mdi-delete</v-icon>
              </template>
            </v-select>
          </v-flex>
        </v-layout>
      </div>
    </v-slide-x-transition>
  </div>
</template>

<script>
export default {
  props: {
    metas: {
      type: Array,
      default: function () {
        return []
      }
    }
  },

  watch: {
    metas: {
      immediate: true,
      handler (values) {
        values.forEach((item) => {
          this.events.push({
            id: this.nonce++,
            value: item.meta,
            type: 'meta'
          })

          // this.input = null
          this.$emit('added', this.events)
        })
      }
    }
  },

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
    if (this.$route.params.id) {
      this.$axios.get('/observer-metas')
        .then(json => { this.items = json.data })
        .catch(err => console.warn(err))
    }
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
