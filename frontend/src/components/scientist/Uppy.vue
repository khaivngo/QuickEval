<template>
  <div id="drag-drop-area"></div>
</template>

<script>
import Uppy from '@uppy/core'
import Tus from '@uppy/tus'
import Dashboard from '@uppy/dashboard'
import Url from '@uppy/url'

// And their styles (for UI plugins)
import '@uppy/core/dist/style.css'
import '@uppy/dashboard/dist/style.css'
import '@uppy/url/dist/style.css'

export default {
  data () {
    return {
      endpoint: 'https://master.tus.io/files/'
    }
  },

  mounted () {
    var uppy = Uppy()

    uppy.use(Dashboard, {
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

    uppy.use(Tus, {
      endpoint: this.endpoint
    })

    uppy.use(Url, {
      target: Dashboard,
      companionUrl: this.endpoint
    })

    uppy.on('complete', (result) => {
      console.log('Upload complete! We’ve uploaded these files: ', result.successful)

      // show toast and/or redirect

      // disable name when clicking upload
      // emit event
    })
  }
}
</script>

<style scoped lang="css">
  a.uppy-Dashboard-poweredBy {
    display: none;
  }
</style>
