var gulp = require('gulp');
var gutil = require('gulp-util');
var jshint = require('gulp-jshint');
var jscs = require('gulp-jscs');
var stylish = require('gulp-jscs-stylish');
var csslint = require('gulp-csslint');

gulp.task('scripts', function() {
	return gulp
		.src('{admin,public}/js/**/*.js')
		.pipe(jshint())
		.pipe(jscs())
		.on('error', gutil.log)
		.pipe(stylish.combineWithHintResults())
		.pipe(jshint.reporter(require('jshint-stylish'), {
			verbose: true
		}));
});

gulp.task('styles', function() {
	return gulp.src('{admin,public}/css/**/*.css')
    	.pipe(csslint())
    	.pipe(csslint.reporter());
});
