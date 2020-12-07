// Config
var config = require('../config.json');

// Require
var gulp = require('gulp');
var gutil = require('gulp-util');
var gulpCopy = require('gulp-copy');
var plumber = require('gulp-plumber');

// copy scripts
gulp.task('copy:scripts:jquery', function() {
    return gulp.src(config.bower.jquery)
        .pipe(gulpCopy(config.scripts.build, {prefix: 3}));
});
