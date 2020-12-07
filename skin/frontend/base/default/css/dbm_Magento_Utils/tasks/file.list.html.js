// Config
var config = require('../config.json');

// Require
var gulp = require('gulp');
var fs = require('fs');
var _ = require('lodash');
var chalk = require('chalk');

var templateChalkGreen = chalk.bold.green;

// read folder content -> add markup to index.html
gulp.task('file:list', function(){
    console.log(templateChalkGreen('/----------------------------------------------/'));
    console.log(templateChalkGreen('               Generate index.html              '));
    console.log(templateChalkGreen('/----------------------------------------------/'));

    var files = fs.readdirSync(config.views.build);
    var filesFiltered = _.without(files, 'assets', 'index.html');

    var templateFile  = '<html>';

        templateFile += '<head>';
        templateFile += '<meta name="viewport" content="width=device-width, initial-scale=1">';
        templateFile += '</head>';

        templateFile += '<body>';
        templateFile += '<ul>';
        _.forEach(filesFiltered, function(value){
            templateFile += '<li><a href="' + value + '">' + value + '</a></li>';
        });
        templateFile += '</ul>';
        templateFile += '</body>';

        templateFile += '</html>';

    var markupHtml = fs.openSync('./'+ config.views.build +'/index.html', 'a+');
    fs.write(markupHtml, templateFile, 0, templateFile.length, 0);
    fs.close(markupHtml);
});
