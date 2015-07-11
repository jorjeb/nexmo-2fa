var gulp    = require('gulp');
var gutil   = require('gulp-util');
var jshint  = require('gulp-jshint');
var jscs    = require('gulp-jscs');
var stylish = require('gulp-jscs-stylish');
var csslint = require('gulp-csslint');
var clean   = require('gulp-clean');

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

gulp.task('default', ['scripts', 'styles']);

gulp.task('clean', function() {
	return gulp.src('dist', {read: false})
		.pipe(clean());
});

gulp.task('build', ['scripts', 'styles', 'clean'], function() {
	return gulp.src('{public/*,admin/*,includes/*,assets/*,*.php,LICENSE,readme.txt}')
		.pipe(gulp.dest('dist'));
});
