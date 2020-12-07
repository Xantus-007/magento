// Require
var gulp = require('gulp');
var gutil = require('gulp-util');
var bower = require('gulp-bower');

// bower : get dependencies
gulp.task('bower', function() {
    return bower();
});
