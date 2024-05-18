const mix = require('laravel-mix');

mix.setPublicPath('assets')
   .autoload({
       jquery: ['$','window.jQuery', 'jQuery']
   })
   .js('src/admin/admin.js', 'assets/js/admin.js')
   .vue() // This is important to enable Vue support
   .sourceMaps(false)
   .extract(['vue']);

// Uncomment if you have Sass files to compile
// mix.sass('assets/sass/admin.scss', 'assets/css/admin.css');
