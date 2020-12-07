// Config
var config = require('../config.json');

// Require
var gulp = require('gulp');
var gutil = require('gulp-util');
var del = require('del');

// clean bower_components
gulp.task('clean:bower', function(callback) {
    return del(config.bower.libs, callback);
});
