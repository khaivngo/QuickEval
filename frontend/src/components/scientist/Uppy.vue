<template>
  <div>
    <slot></slot>
    <div id="drag-drop-area-reproductions" style="max-width: 100%;"></div>
  </div>
</template>

<script>
import Uppy from '@uppy/core'
import xhrUpload from '@uppy/xhr-upload'
import Dashboard from '@uppy/dashboard'

// And their styles (for UI plugins)
import '@uppy/core/dist/style.css'
import '@uppy/dashboard/dist/style.css'

export default {
  props: {
    imagesetid: null,
    width: {
      type: Number,
      required: false,
      default: 750
    },
    height: {
      type: Number,
      required: false,
      default: 550
    }
  },

  watch: {
    imagesetid (val) {
      this.uppy.setMeta({ imageSetId: val })
    }
  },

  data () {
    return {
      uppy: null,
      uppySettings: {
        endpoint: `${this.$API_URL}/file`,
        headers: {
          'authorization': `Bearer ${localStorage.access_token}`
        }
      }
    }
  },

  mounted () {
    this.uppy = Uppy({
      autoProceed: true
    })

    this.uppy.use(Dashboard, {
      trigger: '#UppyModalOpenerBtn',
      // inline: true,
      target: '#drag-drop-area-reproductions',
      width: this.width,
      height: this.height,
      thumbnailWidth: 280,
      showLinkToFileUploadResult: true,
      showProgressDetails: false,
      hideUploadButton: false,
      hideRetryButton: false,
      hidePauseResumeButton: false,
      hideCancelButton: false,
      hideProgressAfterFinish: false,
      note: null,
      closeModalOnClickOutside: true,
      closeAfterFinish: false,
      disableStatusBar: false,
      disableInformer: false,
      disableThumbnailGenerator: false,
      disablePageScrollWhenModalOpen: false,
      animateOpenClose: true,
      proudlyDisplayPoweredByUppy: false,
      showSelectedFiles: true,
      browserBackButtonClose: false
    })

    this.uppy.use(xhrUpload, {
      endpoint: this.uppySettings.endpoint
    })

    this.uppy.setMeta({
      imageSetId: this.imagesetid,
      original: 0
    })

    this.uppy.on('complete', (result, test) => {
      // wait 2 sec before clearing the files after upload
      // window.setTimeout(() => { this.uppy.reset() }, 2000)
      // console.log(result)
    })

    this.uppy.on('upload-success', (file, response) => {
      this.$emit('uploaded', response.body)
    })

    // override Uppy's hardcoded min-height of 450px
    var el = document.querySelector('.uppy-Dashboard-inner')
    el.setAttribute('style', 'min-height: 200px;')
  }
}
</script>

<style scoped lang="css">
  .default {
    border: 1px solid #ccc;
    background: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    height: 250px;
    cursor: pointer;
  }
  #UppyModalOpenerBtn:hover {
    background: #eee;
  }
</style>
