<template>
  <div class="mt-12">
    <h2 class="mb-4 mt-12">
      <!-- Heatmap -->
      Artifact markings
    </h2>

    <v-select
      v-model="selectedImage"
      :items="titles"
      item-text="name"
      item-value="id"
      return-object
      placeholder="select image"
      outlined
      dense
      hide-details
      width="400"
      style="width: 400px;"
    ></v-select>

    <v-btn
      @click="exportHeatmap()"
      :loading="exporting"
      :disabled="selectedImage === null || savedShapes.length === 0 || exporting"
      color="primary"
      class="mt-4"
    >
      Export Heatmap
    </v-btn>
  </div>
</template>

<script>
export default {
  watch: {
    selectedImage () {
      this.imageSelected()
    }
  },

  data () {
    return {
      artifacts: [],
      data: [],  // the matrix in a 2D array format
      maxVal: 0, // the matrix element with highest value
      CSV: '',   // the matrix in CSV format (String)

      image: {
        width: 100,
        height: 100
      },
      savedShapes: [],
      selectedImage: null,
      titles: [],
      exporting: false
    }
  },

  async created () {
    let artifacts = await this.$axios.get('/result-image-artifacts/' + this.$route.params.id)
    this.artifacts = artifacts.data

    let entries = Object.values(artifacts.data)
    entries.forEach((image) => {
      this.titles.push({
        id: image[0].picture.id,
        name: image[0].picture.name,
        path: image[0].picture.path
      })
    })
  },

  methods: {
    /**
     * When selected image has changed we calculate a matrix of
     * all artifact marks for that image.
     */
    imageSelected () {
      this.exporting = true
      this.savedShapes = []

      this.artifacts[this.selectedImage.id].forEach((artifact) => {
        this.savedShapes.push({
          fill: JSON.parse(artifact.selected_area),
          annotation: '',
          eyeVisible: true,
          hidden: false
        })
      })

      let baseImage = new Image()
      baseImage.src = this.$UPLOADS_FOLDER + this.selectedImage.path
      let vm = this
      baseImage.onload = function () {
        // imageCtx.drawImage(baseImage, 0, 0);
        vm.image.width = this.width
        vm.image.height = this.height

        // window.setTimeout(() => {
        vm.createMatrix()

        this.exporting = false
        // }, 1000)
      }
    },

    /**
     * Create a matrix of the experiment image with marked points as data.
     */
    createMatrix () {
      var shapesArray = []

      // for visible shapes convert [{ x: 3, y: 7 }, { x: 3, y: 8 }] into [[3, 7], [3, 8]]
      for (var i = 0; i < this.savedShapes.length; i++) {
        // the shape is active
        if (this.savedShapes[i].eyeVisible && !this.savedShapes[i].hidden) {
          // loop all coordinates for this shape
          for (var j = 0; j < this.savedShapes[i].fill.length; j++) {
            // add shape coordinate to array
            shapesArray.push([
              this.savedShapes[i].fill[j].x,
              this.savedShapes[i].fill[j].y
            ])
          }
        }
      }

      // Create matrix with the same dimensions as the image
      // (one array for each row, and one array object for each pixel in the image):
      // [
      //   [ { val: 0 }, { val: 0 }, { val: 0 } ],
      //   [ { val: 0 }, { val: 0 }, { val: 0 } ]
      // ]
      var matrix = []
      for (var b = 0; b < this.image.width; b++) {
        matrix[b] = []
        for (var f = 0; f < this.image.height; f++) {
          matrix[b][f] = { val: 0 }
        }
      }

      // Set marking values to matrix elements:
      for (var g = 0; g < shapesArray.length; g++) {
        matrix[ shapesArray[g][0] ][ shapesArray[g][1] ].val++
      }

      this.data = matrix
      this.exporting = false
    },

    /**
     * Calculate the matrix element with the highest value.
     */
    calcMaxValue () {
      var maxVal = 0

      // Get max intersection value:
      for (var i = 0; i < this.data.length; i++) {
        for (var j = 0; j < this.data[i].length; j++) {
          if (i === 0) {
            maxVal = this.data[i][j].val
          } else {
            if (this.data[i][j].val > maxVal) {
              maxVal = this.data[i][j].val
            }
          }
        }
      }

      this.maxVal = maxVal
    },

    /**
     * Convert the matrix to CSV format.
     */
    generateCSV () {
      var matrixText = ''

      for (var i = 0; i < this.image.width; i++) {
        for (var j = 0; j < this.image.height; j++) {
          matrixText += this.data[i][j].val

          if (j + 1 < this.image.height) {
            matrixText += ','
          }
        }
        matrixText += '\n'
      }

      this.CSV = matrixText
    },

    exportHeatmap () {
      this.exporting = true

      this.generateCSV()

      const url = window.URL.createObjectURL(new Blob([this.CSV]))
      const link = document.createElement('a')
      link.href = url
      link.setAttribute(
        'download',
        `${this.selectedImage.name.substring(0, 50)}-heatmap-matrix.csv`
      )
      document.body.appendChild(link)
      link.click()

      this.exporting = false
    }
  }
}
</script>
