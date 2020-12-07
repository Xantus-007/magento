// Config
var config = require('../config.json');

// Require
var gulp = require('gulp');
var gutil = require('gulp-util');
var plumber = require('gulp-plumber');
var del = require('del');
var jade = require('gulp-jade');
var browserSync = require('browser-sync').create();

// jade to html
gulp.task('views:jade', function() {
    del([config.views.build + '/*.html', '!' + config.views.build + '/index.html']);

    return gulp.src(config.views.src)
        .pipe(plumber())
        .pipe(jade({
            pretty: true
        }))
        .pipe(gulp.dest(config.views.build))
        .pipe(browserSync.stream());
});
