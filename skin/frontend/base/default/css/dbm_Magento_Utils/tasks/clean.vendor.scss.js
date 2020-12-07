// Config
var config = require('../config.json');

// Require
var gulp = require('gulp');
var gutil = require('gulp-util');
var del = require('del');

// clean scss/vendor
gulp.task('clean:vendor:scss', function() {
    return del(config.styles.vendor_libs);
});
