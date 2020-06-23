<template>
  <v-container>
    <v-layout class="mb-3 mt-1" justify-center>
      <v-menu open-on-hover bottom offset-y>
        <template v-slot:activator="{ on }">
          <v-btn
            color="info"
            v-on="on"
            class="text-none"
          >
            Add category <v-icon class="ml-2" :size="20">add</v-icon>
          </v-btn>
        </template>

        <v-list>
          <v-list-tile @click="add('category')">
            <v-list-tile-title right>
              <v-icon left small>mdi-pencil</v-icon>
              Create new
            </v-list-tile-title>
          </v-list-tile>
          <v-list-tile @click="add('categoryFromHistory')">
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
              v-if="event.type === 'category'"
              label="Input label"
              outlined
              dense
              v-model="event.value"
              class="m-0"
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
              label="Select input label"
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
  </v-container>
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
