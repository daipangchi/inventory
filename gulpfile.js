const elixir = require('laravel-elixir');
const gulp = require('gulp');
const concat = require('gulp-concat');

const vendorFiles = [
    './resources/assets/js/lib/jquery/jquery.min.js',
    './resources/assets/js/lib/tether/tether.min.js',
    './resources/assets/js/lib/bootstrap/bootstrap.min.js',
    // './resources/assets/js/lib/perfect-scrollbar/perfect-scrollbar.jquery.js',
    // './resources/assets/js/vendor/jquery.clickout.js',
    // './resources/assets/js/vendor/jquery-asPieProgress.min.js',
    './resources/assets/js/lib/moment/moment-with-locales.min.js',
    './resources/assets/js/lib/moment-timezone/moment-timezone-with-data.min.js',
    './resources/assets/js/lib/eonasdan-bootstrap-datetimepicker/bootstrap-datetimepicker.min.js',
    // './resources/assets/js/lib/peity/jquery.peity.js',
    './resources/assets/js/lib/autosize/autosize.js',
    // './resources/assets/js/lib/match-height/jquery.matchHeight.js',
    './resources/assets/js/lib/fancybox/jquery.fancybox.js',
    // './resources/assets/js/lib/slick-carousel/slick.js',
    './resources/assets/js/lib/bootstrap-tags-input/bootstrap-tagsinput.js',
    './resources/assets/js/lib/handlebars/handlebars.js',
    // './resources/assets/js/lib/jquery.steps/jquery.steps.js',
    './resources/assets/js/lib/jstree/jstree.min.js',
    './resources/assets/js/lib/bootstrap-sweetalert/sweetalert.min.js',

    // './resources/assets/js/lib/datatables-net/datatables.js',
    // './resources/assets/js/lib/datatables-net/select-1.2.0/js/jquery.dataTables.select.min.js',
    // './resources/assets/js/lib/datatables-net/select-1.2.0/js/dataTables.select.min.js',
    // './resources/assets/js/lib/datatables-net/editor-1.5.6/dataTables.editor.js',
    // './resources/assets/js/lib/datatables-net/editor-1.5.6/editor.bootstrap.js',
];
const appJsFiles = [
    'app/**/*.js',
    'app/helpers.js',
    'app/app.js',
];

gulp.task('templates', function() {
    gulp.src('resources/assets/js/templates/**/*')
        .pipe(concat('templates.html'))
        .pipe(gulp.dest('public/js'));
});

elixir(function (mix) {
    mix.less('main.less');
    mix.less('theme/theme.less');

    mix.scripts(vendorFiles, 'public/js/vendor.js');
    mix.babel(appJsFiles, './public/js/app.js');
    mix.task('templates');

    mix.browserSync({
        open: false,
        proxy: 'cadabra.app:8000'
    });
});
