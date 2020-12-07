// Config
var config = require('../config.json');

// Require
var gulp = require('gulp');
var gutil = require('gulp-util');
var gulpCopy = require('gulp-copy');
var plumber = require('gulp-plumber');

// copy bower_components
gulp.task('copy:foundation:scss', function() {
    return gulp.src(config.bower.foundation_styles)
        .pipe(gulpCopy(config.styles.vendor_libs, {prefix: 3}));
});
