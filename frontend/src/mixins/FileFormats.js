export default {
  methods: {
    /**
     * @param string
     * @returns boolean
     */
    isImage (extension) {
      return ['', ' ', 'jpg', 'jpeg', 'jpe', 'jif', 'jfif', 'jfi', 'png', 'gif', 'webp', 'tiff', 'tif', 'psd', 'raw', 'arw', 'cr2', 'nrw', 'k25', 'bmp', 'dib', 'heif', 'heic', 'ind', 'indd', 'indt', 'jp2', 'j2k', 'jpf', 'jpx', 'jpm', 'mj2', 'svg', 'svgz', 'ai', 'eps', 'pdf']
        .includes(extension.toLowerCase())
    },

    /**
     * @param string
     * @returns boolean
     */
    isVideo (extension) {
      return ['mp4', 'm4p', 'webm', 'wmv', '3g2', '3gp', 'aaf', 'asf', 'avchd', 'avi', 'drc', 'flv', 'm2v', 'm3u8', 'm4v', 'mkv', 'mng', 'mov', 'mp2', 'mpe', 'mpeg', 'mpg', 'mpv', 'mxf', 'nsv', 'ogg', 'ogv', 'qt', 'rm', 'rmvb', 'roq', 'svi', 'vob', 'yuv']
        .includes(extension.toLowerCase())
    }
  }
}
