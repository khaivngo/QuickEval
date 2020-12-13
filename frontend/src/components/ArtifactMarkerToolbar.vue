<template>
  <div class="pa-2 d-flex align-center" style="background: #707070;">
    <v-btn large icon @click="mode = 'setModeToPanning'" title="Make image movable" class="mr-2">
      <v-icon large>mdi-drag</v-icon>
    </v-btn>

    <v-btn large icon @click="mode = 'setModeToDrawing'" title="Enable drawing tool" class="mr-2">
      <v-icon large>mdi-draw</v-icon>
    </v-btn>

    <div class="mode marking-tools">
      <v-btn icon @click="mode = 'setMarkingActiveTool'" title="Pen tool" class="mr-2">
        <v-icon>mdi-pencil</v-icon>
      </v-btn>

      <v-btn icon @click="mode = 'setEraseActiveTool'" title="Erase tool" class="mr-2">
        <v-icon>mdi-scissors-cutting</v-icon>
      </v-btn>

      <v-btn icon @click="mode = 'undo'" title="Undo last drawing">
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
