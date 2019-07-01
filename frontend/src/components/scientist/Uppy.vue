<template>
  <div id="drag-drop-area"></div>
</template>

<script>
import Uppy from '@uppy/core'
import xhrUpload from '@uppy/xhr-upload'
import Dashboard from '@uppy/dashboard'
import Url from '@uppy/url'

// And their styles (for UI plugins)
import '@uppy/core/dist/style.css'
import '@uppy/dashboard/dist/style.css'
import '@uppy/url/dist/style.css'

export default {
  props: {
    imagesetid: null
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
    this.uppy = Uppy()

    this.uppy.use(Dashboard, {
      inline: true,
      target: '#drag-drop-area',
      width: 750,
      height: 510,
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

    this.uppy.use(Url, {
      target: Dashboard,
      companionUrl: this.uppySettings.endpoint
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

    this.uppy.setMeta({
      imageSetId: this.imagesetid
    })
  }
}
</script>

<style scoped lang="css">
  a.uppy-Dashboard-poweredBy {
    display: none;
  }
</style>
