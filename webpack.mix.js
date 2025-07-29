const mix = require('laravel-mix');
const path = require('path');
const Fs = require('fs');

if (process.env.MIX_PUBLIC_PATH !== null && process.env.MIX_PUBLIC_PATH !== undefined && process.env.MIX_PUBLIC_PATH !== '') {
  mix.setPublicPath('public').webpackConfig({
    output: { publicPath: process.env.MIX_PUBLIC_PATH }
  });
}

/**
 * !Copy Assets
 */
mix.copy('node_modules/@fortawesome/fontawesome-free/webfonts/*', 'public/webfonts');

/**
 * !Backend/Dashboard
 */
// Build Backend/Dashboard SASS
mix.sass('resources/sass/libs.scss', 'public/css/libs.min.css')
   .sass('public/scss/kivilab.scss', 'public/css')
   .sass('public/scss/custom.scss', 'public/css')
   .sass('public/scss/rtl.scss', 'public/css')
   .sass('public/scss/customizer.scss', 'public/css')

// Backend/Dashboard Styles
mix.styles(['public/css/kivilab.css'], 'public/css/backend.css');
mix.styles(['node_modules/@fortawesome/fontawesome-free/css/all.min.css'], 'public/css/icon.min.css');

mix.styles([
  'node_modules/select2/dist/css/select2.min.css'
], 'public/css/select2.css');

mix.scripts([
  'node_modules/select2/dist/js/select2.min.js'
], 'public/js/select2.js');

mix.scripts([
  'node_modules/apexcharts/dist/apexcharts.min.js'
], 'public/js/apexcharts.js');



// Copy flag images
mix.copy('node_modules/intl-tel-input/build/img', 'public/img/intl-tel-input');
mix.copy('node_modules/intl-tel-input/build/js/intlTelInput.min.js', 'public/js')
.copy('node_modules/intl-tel-input/build/img/flags.webp', 'public/img/flags.webp')
.copy('node_modules/intl-tel-input/build/js/utils.js', 'public/js')
.copy('node_modules/intl-tel-input/build/css/intlTelInput.css', 'public/css')
.copyDirectory('node_modules/intl-tel-input/build/img', 'public/img/intl-tel-input');

mix.scripts([
  'node_modules/chart.js/dist/chart.umd.js'
], 'public/js/chart.js');

mix.scripts([
  'node_modules/sweetalert2/dist/sweetalert2.all.min.js'
], 'public/js/sweetalert2.js');

mix.styles([
  'node_modules/sweetalert2/dist/sweetalert2.min.css'
], 'public/css/sweetalert2.css');

mix.styles([
  'node_modules/flatpickr/dist/flatpickr.min.css'
], 'public/css/flatpickr.css');

mix.scripts([
  'node_modules/flatpickr/dist/flatpickr.min.js'
], 'public/js/flatpickr.js');


// Backend/Dashboard Scripts
mix.js('resources/js/libs.js', 'public/js/core/libs.min.js')
   .js('resources/js/backend-custom.js', 'public/js/backend-custom.js')
   .scripts(['public/js/core/libs.min.js', 'public/js/backend-custom.js'], 'public/js/backend.js');

// Add alias
mix.alias({
  '@': path.join(__dirname, 'resources/js')
});

// Application-specific scripts and styles
mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css');

mix.js('resources/js/import-export.js', 'public/js/import-export.min.js')
/**
 * !Module-Based Script & Style Bundling
 */
const Modules = require('./modules_statuses.json');

for (const key in Modules) {
  if (Object.hasOwnProperty.call(Modules, key)) {
    if (Fs.existsSync(`${__dirname}/Modules/${key}/Resources/assets/js/app.js`)) {
      mix.js(`${__dirname}/Modules/${key}/Resources/assets/js/app.js`, `modules/${key.toLocaleLowerCase()}/script.js`).vue().sourceMaps()
    }
    if (Fs.existsSync(`${__dirname}/Modules/${key}//Resources/assets/sass/app.scss`)) {
      mix.sass(`${__dirname}/Modules/${key}//Resources/assets/sass/app.scss`, `modules/${key.toLocaleLowerCase()}/style.css`).sourceMaps()
    }
  }
}

// Add versioning for production builds
if (mix.inProduction()) {
  mix.version();
}
