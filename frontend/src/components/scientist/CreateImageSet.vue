<template>
  <div>
    <v-layout>
      <v-text-field
        v-model="imageSet.name"
        label="Name"
      ></v-text-field>
    </v-layout>

    <v-layout mt-2>
      <v-text-field
        v-model="imageSet.description"
        label="Description"
      ></v-text-field>
    </v-layout>

    <v-layout mt-5>
      <v-flex>
        <Uppy/>
        <!-- v-show="imageSet.name !== null && imageSet.name !== ''" -->
      </v-flex>
    </v-layout>

    <!-- <v-layout justify-end>
      <v-btn color="success" depressed @click="createImageSet">
        Create
      </v-btn>
    </v-layout> -->
  </div>
</template>

<script>
import Uppy from '@/components/scientist/Uppy'

export default {
  components: {
    Uppy
  },

  data () {
    return {
      imageSet: {
        name: null,
        description: null
      }
    }
  },

  methods: {
    createImageSet () {
      this.$axios.post('/scientist/createNewImageSet.php',
        {
          name: this.imageSet.name,
          text: this.imageSet.description
        }
      ).then((response) => {
        console.log(response)

        // if success upload images
      })
    }
  }
}
</script>
