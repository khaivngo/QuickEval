<template>
  <v-card class="editor elevation-0">
    <editor-menu-bar :editor="editor" v-slot="{ commands, isActive }">
      <div class="menubar">

        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn text icon small @click="commands.bold" v-on="on" class="menubar__button ma-1" :class="{ 'is-active': isActive.bold() }">
              <v-icon>mdi-format-bold</v-icon>
            </v-btn>
          </template>
          <span>Bold</span>
        </v-tooltip>

        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn text icon small @click="commands.italic" v-on="on" class="menubar__button ma-1" :class="{ 'is-active': isActive.italic() }">
              <v-icon>mdi-format-italic</v-icon>
            </v-btn>
          </template>
          <span>Italic</span>
        </v-tooltip>

        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn text icon small @click="commands.strike" v-on="on" class="menubar__button ma-1" :class="{ 'is-active': isActive.strike() }">
              <v-icon>mdi-format-strikethrough</v-icon>
            </v-btn>
          </template>
          <span>Strikethrough</span>
        </v-tooltip>

        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn text icon small @click="commands.underline" v-on="on" class="menubar__button ma-1" :class="{ 'is-active': isActive.underline() }">
              <v-icon>mdi-format-underline</v-icon>
            </v-btn>
          </template>
          <span>Underline</span>
        </v-tooltip>

        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn text icon small @click="commands.code" v-on="on" class="menubar__button ma-1" :class="{ 'is-active': isActive.code() }">
              <v-icon>mdi-code-tags</v-icon>
            </v-btn>
          </template>
          <span>Line of code</span>
        </v-tooltip>

        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn text icon small @click="commands.paragraph" v-on="on" class="menubar__button ma-1" :class="{ 'is-active': isActive.paragraph() }">
              <v-icon>mdi-format-textdirection-l-to-r</v-icon>
            </v-btn>
          </template>
          <span>Paragraph</span>
        </v-tooltip>

        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn text icon small @click="commands.heading({ level: 1 })" v-on="on" class="menubar__button ma-1" :class="{ 'is-active': isActive.heading({ level: 1 }) }">
              H1
            </v-btn>
          </template>
          <span>Title 1</span>
        </v-tooltip>

        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn text icon small @click="commands.heading({ level: 2 })" v-on="on" class="menubar__button ma-1" :class="{ 'is-active': isActive.heading({ level: 2 }) }">
              H2
            </v-btn>
          </template>
          <span>Title 2</span>
        </v-tooltip>

        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn text icon small @click="commands.heading({ level: 3 })" v-on="on" class="menubar__button ma-1" :class="{ 'is-active': isActive.heading({ level: 3 }) }">
              H3
            </v-btn>
          </template>
          <span>Title 3</span>
        </v-tooltip>

        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn text icon small @click="commands.bullet_list" v-on="on" class="menubar__button ma-1" :class="{ 'is-active': isActive.bullet_list() }">
              <v-icon>mdi-format-list-bulleted</v-icon>
            </v-btn>
          </template>
          <span>Bullet point list</span>
        </v-tooltip>

        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn text icon small @click="commands.ordered_list" v-on="on" class="menubar__button ma-1" :class="{ 'is-active': isActive.ordered_list() }">
              <v-icon>mdi-format-list-numbered</v-icon>
            </v-btn>
          </template>
          <span>Numbered list</span>
        </v-tooltip>

        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn text icon small @click="commands.blockquote" v-on="on" class="menubar__button ma-1" :class="{ 'is-active': isActive.blockquote() }">
              <v-icon>mdi-format-quote-close</v-icon>
            </v-btn>
          </template>
          <span>Blockquote</span>
        </v-tooltip>

        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn text icon small @click="commands.code_block" v-on="on" class="menubar__button ma-1" :class="{ 'is-active': isActive.code_block() }">
              <v-icon>mdi-code-tags</v-icon>
            </v-btn>
          </template>
          <span>Block of code</span>
        </v-tooltip>

        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn text icon small @click="commands.horizontal_rule" v-on="on" class="menubar__button ma-1">
              —
            </v-btn>
          </template>
          <span>Horizontal line</span>
        </v-tooltip>

        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn text icon small @click="commands.undo" v-on="on" class="menubar__button ma-1">
              <v-icon>mdi-undo</v-icon>
            </v-btn>
          </template>
          <span>Undo</span>
        </v-tooltip>

        <v-tooltip top>
          <template v-slot:activator="{ on }">
            <v-btn text icon small @click="commands.redo" v-on="on" class="menubar__button ma-1">
              <v-icon>mdi-redo</v-icon>
            </v-btn>
          </template>
          <span>Redo</span>
        </v-tooltip>

      </div>
    </editor-menu-bar>

    <editor-content class="editor__content" :editor="editor"/>
  </v-card>
</template>

<script>
import { Editor, EditorContent, EditorMenuBar } from 'tiptap'
import {
  Placeholder,
  Blockquote,
  CodeBlock,
  HardBreak,
  Heading,
  HorizontalRule,
  OrderedList,
  BulletList,
  ListItem,
  TodoItem,
  TodoList,
  Bold,
  Code,
  Italic,
  Link,
  Strike,
  Underline,
  History
} from 'tiptap-extensions'

export default {
  props: ['value'],
  components: {
    EditorContent,
    EditorMenuBar
  },
  data () {
    return {
      editor: null,
      emitAfterOnUpdate: false
    }
  },
  watch: {
    // https://github.com/ueberdosis/tiptap/issues/133#issuecomment-559014481
    value (val) {
      if (this.emitAfterOnUpdate) {
        this.emitAfterOnUpdate = false
        return
      }
      if (this.editor) {
        this.editor.setContent(val, true)
      }
    }
  },
  mounted () {
    this.editor = new Editor({
      extensions: [
        new Placeholder({
          emptyNodeClass: 'is-empty',
          emptyNodeText: 'Write your instructions …',
          showOnlyWhenEditable: true
        }),
        new Blockquote(),
        new BulletList(),
        new CodeBlock(),
        new HardBreak(),
        new Heading({ levels: [1, 2, 3] }),
        new HorizontalRule(),
        new ListItem(),
        new OrderedList(),
        new TodoItem(),
        new TodoList(),
        new Link(),
        new Bold(),
        new Code(),
        new Italic(),
        new Strike(),
        new Underline(),
        new History()
      ],
      content: this.value,
      onUpdate: ({ getJSON, getHTML }) => {
        this.emitAfterOnUpdate = true
        this.$emit('input', getHTML())
      }
    })
  },
  beforeDestroy () {
    this.editor.destroy()
  }
  // methods: {
  //   handleInput (e) {
  //     this.$emit('input', this.html)
  //   }
  // }
}
</script>

<style lang="css">
  .ProseMirror {
    outline: none;
    height: 100%;
  }
</style>

<style lang="scss">
  .editor p.is-empty:first-child::before {
    content: attr(data-empty-text);
    float: left;
    color: #aaa;
    pointer-events: none;
    height: 0;
    font-style: italic;
  }
</style>

<style scoped lang="css">
  .editor {
    width: 100%;
    max-width: 100%;
    border: 1px solid rgba(0,0,0,.38);
  }
  /*.editor:focus {
    outline: #1976d2 solid 3px;
  }*/
  .menubar {
    padding-bottom: 5px;
    /*display: flex;*/
    background-color: #f5f5f5;
  }
  .menubar__button {
    padding: 5px;
    margin-right: 4px;
    color: rgb(112, 112, 112);
  }
  .is-active {
    background-color: rgb(222, 222, 222);
  }
  .editor__content {
    padding: 15px;
    height: 100%;
    max-width: 100%;
  }
</style>
