// Config
var config = require('../config.json');

// Require
var gulp = require('gulp');
var gutil = require('gulp-util');
var plumber = require('gulp-plumber');
var realFavicon = require ('gulp-real-favicon');
var html2jade = require('html2jade');
var chalk = require('chalk');
var fs = require('fs');

var FAVICON_DATA_FILE = config.views.tpl + '/_favicon.json';
var templateChalkGreen = chalk.bold.green;

gulp.task('favicon:info', function(){
    console.log(templateChalkGreen('/----------------------------------------------/'));
    console.log(templateChalkGreen('      This step will take several secondes      '));
    console.log(templateChalkGreen('   (use of a thirdpart, a connexion is needed)  '));
    console.log(templateChalkGreen('/----------------------------------------------/'));
});

// Generate the icons. This task takes a few seconds to complete.
// You should run it at least once to create the icons. Then,
// you should run it whenever RealFaviconGenerator updates its
// package (see the check-for-favicon-update task below).
gulp.task('favicon:generate', function(){
    realFavicon.generateFavicon({
        masterPicture: config.faviconPath.src,
        dest: config.faviconPath.dest,
        iconsPath: '/',
        design: {
            ios: {
                pictureAspect: 'noChange'
            },
            desktopBrowser: {},
            windows: {
                pictureAspect: 'noChange',
                backgroundColor: config.themeColor,
                onConflict: 'override'
            },
            androidChrome: {
                pictureAspect: 'noChange',
                themeColor: config.themeColor,
                manifest: {
                    name: config.projectName,
                    display: 'browser',
                    orientation: 'notSet',
                    onConflict: 'override',
                    declared: true
                }
            },
            safariPinnedTab: {
                pictureAspect: 'blackAndWhite',
                threshold: 53.90625,
                themeColor: config.themeColor
            }
        },
        settings: {
            scalingAlgorithm: 'Mitchell',
            errorOnImageTooSmall: false
        },
        markupFile: FAVICON_DATA_FILE
    }, function(){
        console.log(templateChalkGreen('/----------------------------------------------/'));
        console.log(templateChalkGreen('                Favicon generate                '));
        console.log(templateChalkGreen('/----------------------------------------------/'));

        var exportJson = require('../' + config.views.tpl + '/_favicon.json');
        var html = exportJson.favicon.html_code;
        var options = {
            nspaces: 4,
            bodyless: true
        };

        html2jade.convertHtml(html, options, function (err, jade) {
            var input = /href='/g;
            var output = 'href=\'#{mediaDir}favicon';
            var resJade = jade.replace(input, output);

            var tpl = fs.openSync('./'+ config.views.tpl +'/_components-favicon.jade', 'a+');
            fs.write(tpl, resJade, 0, resJade.length, 0);
            fs.close(tpl);

            console.log(templateChalkGreen('/----------------------------------------------/'));
            console.log(templateChalkGreen('./'+ config.views.tpl +'/_components-favicon.jade == Update'));
            console.log(templateChalkGreen('/----------------------------------------------/'));
        });
    });
});

// gulp.task('favicon:markup', function(){
//     var exportJson = require('../' + config.views.tpl + '/_favicon.json');
//     var html = exportJson.favicon.html_code;
//     var options = {
//         nspaces: 4,
//         bodyless: true
//     };

//     html2jade.convertHtml(html, options, function (err, jade) {
//         var input = /href='/g;
//         var output = 'href=\'#{mediaDir}favicon';
//         var resJade = jade.replace(input, output);

//         var tpl = fs.openSync('./'+ config.views.tpl +'/_components-favicon.jade', 'a+');
//         fs.write(tpl, resJade, 0, resJade.length, 0);
//         fs.close(tpl);

//         console.log(templateChalkGreen('/----------------------------------------------/'));
//         console.log(templateChalkGreen('./'+ config.views.tpl +'/_components-favicon.jade == Update'));
//         console.log(templateChalkGreen('/----------------------------------------------/'));
//     });
// });
