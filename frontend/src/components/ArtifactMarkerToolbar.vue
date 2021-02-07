<template>
  <div class="pa-2 d-flex align-center"> <!-- style="background: #707070;" -->
    <v-tooltip bottom>
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          small fab
          :color="(mode === 'setModeToPanning') ? 'primary' : 'default'"
          @click="panningMode"
          v-on="on"
          v-bind="attrs"
          class="mr-2"
        >
          <!-- title="Make image movable" -->
          <v-icon>mdi-drag</v-icon>
        </v-btn>
      </template>
      <span>Enable movable images</span>
    </v-tooltip>

    <v-tooltip bottom>
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          small fab
          v-on="on"
          v-bind="attrs"
          :color="(mode === 'setModeToDrawing') ? 'primary' : 'default'"
          @click="drawingMode"
          class="mr-2"
        >
          <!-- title="Enable drawing tool" -->
          <!-- <v-icon>mdi-draw</v-icon> -->
          <v-icon>mdi-fountain-pen-tip</v-icon>
        </v-btn>
      </template>
      <span>Enable drawing tool</span>
    </v-tooltip>

    <div v-show="mode === 'setModeToDrawing' || mode === 'setEraseActiveTool' || mode === 'undo'">
      <v-btn
        x-small fab
        :color="(mode === 'setEraseActiveTool') ? 'primary' : 'default'"
        @click="toggleDrawingMode"
        title="Erase tool"
        class="mr-2"
      >
        <v-icon>mdi-scissors-cutting</v-icon>
      </v-btn>

      <v-btn x-small fab @click="mode = 'undo'" title="Undo last drawing">
        <v-icon>mdi-undo</v-icon>
      </v-btn>
    </div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      mode: 'setModeToPanning'
    }
  },
  created () {
    this.$emit('changed', 'setModeToPanning')
  },
  watch: {
    mode () {
      this.$emit('changed', this.mode)
    }
  },
  methods: {
    panningMode () {
      this.mode = 'setModeToPanning'
      this.$emit('changed', 'setModeToPanning')
    },
    drawingMode () {
      this.mode = 'setModeToDrawing'
      this.$emit('changed', 'setModeToDrawing')
      this.$emit('changed', 'setMarkingActiveTool')
      // this.mode = 'setMarkingActiveTool'
    },
    toggleDrawingMode () {
      if (this.mode === 'setEraseActiveTool') {
        this.mode = 'setModeToDrawing'
        this.$emit('changed', 'setModeToDrawing')
        this.$emit('changed', 'setMarkingActiveTool')
      } else {
        this.mode = 'setEraseActiveTool'
        this.$emit('changed', 'setModeToDrawing')
        this.$emit('changed', 'setEraseActiveTool')
      }
    }
  }
}
</script>
