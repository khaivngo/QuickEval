<template>
  <div>
    <v-layout class="mb-12 mt-8">
      <v-btn
        @click="add('category')"
        color="info"
      >
        <v-icon class="mr-2" :size="20">mdi-plus-circle-outline</v-icon> category
      </v-btn>

      <v-tooltip top>
        <template v-slot:activator="{ on }">
          <v-btn icon v-on="on">
            <v-icon @click="add('categoryFromHistory')">
              mdi-history
            </v-icon>
          </v-btn>
        </template>
        <div class="pl-1 pr-1 pt-2 pb-2 body-1">
          Add from history
        </div>
      </v-tooltip>
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
              v-if="event.type === 'category'"
              label="Write input title"
              outlined
              dense
              v-model="event.value"
              item-text="title"
              item-value="id"
            >
              <template v-slot:append-outer>
                <v-icon @click="remove(i)">mdi-delete</v-icon>
              </template>
            </v-text-field>

            <v-select
              v-if="event.type === 'categoryFromHistory'"
              :items="items"
              label="Select input title"
              v-model="event.value"
              item-text="title"
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
    categories: {
      type: Array,
      default: function () {
        return []
      }
    }
  },

  watch: {
    categories: {
      immediate: true,
      handler (values) {
        values.forEach((item) => {
          this.events.push({
            id: this.nonce++,
            value: item.title,
            type: 'category'
          })

          // this.input = null
          this.$emit('added', this.events)
        })
      }
    }
  },

  data: () => ({
    items: [], // existing categories
    events: [], // it all goes here
    input: null,
    nonce: 0
  }),

  computed: {
    timeline () {
      return this.events.slice().reverse()
    }
  },

  async created () {
    // populate the exisiting categories dropdowns
    const categories = await this.getCategories()
    this.items = categories.data
  },

  methods: {
    // Since axios returns a promise the async/await can be omitted for the getData function
    getCategories () {
      try {
        return this.$axios.get('/categories')
      } catch (error) {
        console.log(error)
        return null
      }
    },

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
