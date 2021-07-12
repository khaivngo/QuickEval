<template>
  <v-autocomplete
    v-model="scientists"
    :items="suggestedUsers"
    :loading="isLoading"
    :search-input.sync="search"
    :loader-height="3"
    label="Invite collaborators"
    item-text="name"
    item-value="id"
    cache-items
    hide-no-data
    hide-selected
    no-filter
    return-object
    multiple
    outlined
  >
  <!-- auto-select-first -->
    <template v-slot:selection="data">
      <v-chip
        v-bind="data.attrs"
        :input-value="data.selected"
        close
        @click="data.select"
        @click:close="remove(data.item)"
      >
        <!-- <v-avatar left>
          <v-img :src="data.item.avatar"></v-img>
        </v-avatar> -->
        {{ data.item.name }}
      </v-chip>
    </template>
    <template v-slot:item="data">
      <!-- <template v-if="typeof data.item !== 'object'">
        <v-list-item-content v-text="data.item"></v-list-item-content>
      </template> -->
      <template>
        <!-- <v-list-item-avatar>
          <img :src="data.item.avatar">
        </v-list-item-avatar> -->

        <!-- v-show="data.item.hidden === false" -->
        <v-list-item-content>
          <v-list-item-title>{{ data.item.name }}</v-list-item-title>
          <v-list-item-subtitle>{{ data.item.email }}</v-list-item-subtitle>
        </v-list-item-content>
      </template>
    </template>
  </v-autocomplete>
</template>

<script>
export default {
  props: {
    collaborators: {
      type: Array,
      default: function () {
        return []
      }
    }
  },

  data () {
    return {
      scientists: [],
      savedScientists: [],
      isUpdating: false,
      isLoading: false,
      search: null,
      suggestedUsers: [],
      prevTerm: ''
    }
  },

  // computed () {
  //   collaborators () {
  //     return this.entries.map(entry => {
  //       const Description = entry.Description.length > this.descriptionLimit
  //         ? entry.Description.slice(0, this.descriptionLimit) + '...'
  //         : entry.Description

  //       return Object.assign({}, entry, { Description })
  //     })
  //   }
  // },

  watch: {
    collaborators: {
      immediate: true,
      handler (values) {
        let data = values.map(obj => ({ ...obj, hidden: false }))

        this.scientists = data
        this.suggestedUsers = data
        this.$emit('added', this.scientists)
      }
    },

    scientists (values) {
      this.$emit('added', this.scientists)
    },

    search (term) {
      if (term === null) return
      if (term === '') return
      // scientists have already been requested
      if (this.isLoading) return

      this.isLoading = true

      this.$axios.get(`/user/search/${term}`).then(response => {
        // this.suggestedUsers.forEach(obj => { obj.hidden = true })

        // this.suggestedUsers.forEach(item => {
        //   response.data.forEach(item2 => {
        //     if (item.id === item2.id) {
        //       item.hidden = false
        //     }
        //   })
        // })

        // hide what is not in the new array
        // find values that are in result1 but not in result2
        // var uniqueResultOne = result1.filter (function(obj) {
        //   return !result2.some(function (obj2) {
        //     return obj.value == obj2.value
        //   })
        // })

        // this.suggestedUsers.forEach(obj => { obj.hidden = false })

        // const results = arrayOne.filter(({ value: id1 }) => !arrayTwo.some(({ value: id2 }) => id2 === id1));
        // add a hidden prop to every item
        // let data = response.data.map(obj => ({ ...obj, hidden: false }))

        this.suggestedUsers = response.data
        // this.suggestedUsers.push(...response.data)
      }).catch((error) => {
        console.log(error)
      }).finally(() => (this.isLoading = false))
    }
  },

  methods: {
    remove (item) {
      const index = this.scientists.findIndex((user) => user.id === item.id)
      this.scientists.splice(index, 1)
    }
  }
}
</script>
