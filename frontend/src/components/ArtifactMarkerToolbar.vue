<template>
  <div class="pa-2 d-flex align-center"> <!-- style="background: #707070;" -->
    <v-tooltip left>
      <template v-slot:activator="{ on, attrs }">
        <!-- <v-btn
          color="primary"
          dark
          v-bind="attrs"
          v-on="on"
        >
          Button
        </v-btn> -->
        <v-btn
          small fab
          :color="(mode === 'setModeToPanning') ? 'primary' : 'default'"
          @click="mode = 'setModeToPanning'"
          v-on="on"
          v-bind="attrs"
          title="Make image movable"
          class="mr-2"
        >
          <v-icon>mdi-drag</v-icon>
        </v-btn>
      </template>
      <span>Movable images</span>
    </v-tooltip>

    <v-tooltip right>
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          small fab
          v-on="on"
          v-bind="attrs"
          :color="(mode === 'setModeToDrawing' || mode === 'setEraseActiveTool'  || mode === 'setMarkingActiveTool') ? 'primary' : 'default'"
          @click="mode = 'setModeToDrawing'"
          title="Enable drawing tool"
          class="mr-2"
        >
          <v-icon>mdi-draw</v-icon>
        </v-btn>
      </template>
      <span>Drawing tool</span>
    </v-tooltip>

    <div v-show="mode === 'setModeToDrawing' || mode === 'setEraseActiveTool' || mode === 'setMarkingActiveTool'">
      <v-btn
        x-small fab
        :color="(mode === 'setMarkingActiveTool') ? 'primary' : 'default'"
        @click="mode = 'setMarkingActiveTool'"
        title="Pen tool"
        class="mr-2"
      >
        <v-icon>mdi-pencil</v-icon>
      </v-btn>

      <v-btn
        x-small fab
        :color="(mode === 'setEraseActiveTool') ? 'primary' : 'default'"
        @click="mode = 'setEraseActiveTool'"
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
      mode: 'setModeToDrawing'
    }
  },
  watch: {
    mode () {
      this.$emit('changed', this.mode)
    }
  }
}
</script>
