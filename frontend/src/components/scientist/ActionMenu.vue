<template>
  <v-menu offset-y left>
    <template v-slot:activator="{ on, attrs }">
      <v-btn
        icon
        v-bind="attrs"
        v-on="on"
        :loading="deleting"
      >
        <v-icon>mdi-dots-horizontal</v-icon>
      </v-btn>
    </template>

    <v-list>
      <v-list-item @click="deleteImage">
        <v-list-item-icon class="mr-4">
          <v-icon>mdi-delete</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title class="pr-6">Delete</v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </v-menu>
</template>

<script>
export default {
  props: {
    image: null,
    index: null
  },
  data () {
    return {
      deleting: false
    }
  },
  methods: {
    deleteImage () {
      if (confirm('Delete image?')) {
        this.deleting = true

        this.$axios.delete(`/picture/${this.image.id}`).then((response) => {
          this.deleting = false
          this.$emit('deleted', this.index)
        }).catch(() => {
          this.deleting = false
        })
      }
    }
  }
}
</script>
