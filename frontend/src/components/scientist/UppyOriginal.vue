<template>
  <div id="drag-drop-area-original"></div>
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
        endpoint: 'http://127.0.0.1/QuickEval/public/api/file',
        headers: {
          'authorization': `Bearer ${localStorage.access_token}`
        }
      }
    }
  },

  mounted () {
    this.uppy = Uppy({
      restrictions: {
        maxNumberOfFiles: 1
      }
    })

    this.uppy.use(Dashboard, {
      inline: true,
      target: '#drag-drop-area-original',
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
      closeModalOnClickOutside: false,
      closeAfterFinish: false,
      disableStatusBar: false,
      disableInformer: false,
      disableThumbnailGenerator: false,
      disablePageScrollWhenModalOpen: true,
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
      original: 1
    })

    this.uppy.on('complete', (result) => {
      console.log('Upload complete! We’ve uploaded these files: ', result.successful)
      console.log(result)

      // show toast and/or redirect

      // disable name when clicking upload
      // emit event
    })

    this.uppy.on('upload-success', (file, response) => {
      console.log(response.status) // HTTP status code
      console.log(response.body) // extracted response data
      // console.log(response.data)
      console.log(file)

      // do something with file and response
    })

    // override Uppy's hardcoded min-height of 450px
    var el = document.querySelector('.uppy-Dashboard-inner')
    el.setAttribute('style', 'min-height: 200px;')
  }
}
</script>
