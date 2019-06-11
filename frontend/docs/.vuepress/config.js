module.exports = {
  title: 'QuickEval',
  description: 'Documentation for the QuickEval source code.',
  themeConfig: {
    logo: '/hero.png',
    nav: [
      { text: 'Home', link: '/' },
      { text: 'Documentation', link: '/documentation/' },
      { text: 'GitHub', link: 'https://github.com/khaivngo/QuickEval' }
    ],
    sidebar: {
      '/documentation/': [
        '',
        'database',
        'structure',
        'algorithms'
      ]
    }
  }
}