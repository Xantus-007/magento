// Config
var config = require('../config.json');

// Require
var gulp = require('gulp');
var gutil = require('gulp-util');
var plumber = require('gulp-plumber');
var sass = require('gulp-sass');
var del = require('del');

// scss to css [vendor]
gulp.task('styles:vendor', function() {
    del(config.styles.vendor_build + '/*');

    return gulp.src(config.styles.vendor_libs + '/**/*.scss')
        .pipe(plumber())
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(gulp.dest(config.styles.vendor_build));
});
