'use strict';

// Config
// ========================================= //

// Config
var config = require('./config.json');

// Require
var gulp = require('gulp');
var gutil = require('gulp-util');
var runSequence = require('run-sequence');
var browserSync = require('browser-sync').create();
var es6Promise = require('es6-promise').polyfill();

var requireDir = require('require-dir');
requireDir('./tasks');

// run server / watch
gulp.task('live', function() {
    browserSync.init({
        startPath: config.views.build,
        server: {
            baseDir: './',
            directory: true
        }
    });

    gulp.watch(config.styles.local_libs, ['styles:local']);
    gulp.watch(config.views.tpl, ['views:jade', browserSync.reload]);
    gulp.watch(config.scripts.src, ['scripts:jshint']);
    gulp.watch(config.iconfont.src + '*.svg', ['iconfont']);
});

// deploy project
gulp.task('deploy', function(callback) {
    runSequence('bower', 'clean:vendor:scss', 'copy:foundation:scss', 'styles:vendor', 'copy:scripts:jquery', 'clean:bower', callback);
});

// generate favicon
gulp.task('favicon', function(callback) {
    runSequence('favicon:info', 'favicon:generate', callback);
})

// default
gulp.task('default', ['styles:local', 'views:jade', 'scripts:jshint', 'live']);
