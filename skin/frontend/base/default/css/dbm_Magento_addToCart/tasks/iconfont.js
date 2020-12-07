// Config
var config = require('../config.json');

// Require
var gulp = require('gulp');
var gutil = require('gulp-util');
var plumber = require('gulp-plumber');
var iconfont = require('gulp-iconfont');
var del = require('del');
var consolidate = require('gulp-consolidate');
var browserSync = require('browser-sync').create();
var runTimestamp = Math.round(Date.now()/1000);

// iconFont
gulp.task('iconfont', function(){
    del(config.iconfont.build);
    del(config.styles.vendor_build + '/fonticon.css');

    return gulp.src([config.iconfont.src + '*.svg'])
        .pipe(plumber())
        .pipe(iconfont({
            fontName: 'iconfont', // required
            appendUnicode: true, // recommended option
            formats: ['ttf', 'eot', 'woff'], // default, 'woff2' and 'svg' are available
            timestamp: runTimestamp, // recommended to get consistent builds when watching files
        }))
        .on('glyphs', function(glyphs, options) {
            // CSS templating, e.g.
            gulp.src(config.iconfont.tpl + 'fonticon.css')
                .pipe(consolidate('lodash', {
                    glyphs: glyphs,
                    fontName: 'iconfont',
                    fontPath: config.iconfont.dist,
                    className: 'c-fonticon'
                }))
                .pipe(gulp.dest(config.styles.vendor_build));
        })
        .pipe(gulp.dest(config.iconfont.build))
        .pipe(browserSync.stream());
});
