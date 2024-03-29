module.exports = {
  chainWebpack: (config) => {
    config.plugins.delete('prefetch')
  },

  // proxy API requests during development
  devServer: {
    // proxy: 'http://blog.test'
    proxy: 'http://localhost'
  },

  // output built static files to Laravel's public dir.
  // note the "build" script in package.json needs to be modified as well.
  outputDir: '../public_html',

  // modify the location of the generated HTML file.
  // make sure to do this only in production.
  indexPath: process.env.NODE_ENV === 'production'
    ? '../resources/views/index.blade.php'
    : 'index.html',

  publicPath: process.env.NODE_ENV === 'production'
    ? '/' // live production
    // ? '/QuickEval/public_html' // local production
    : '/'
}
