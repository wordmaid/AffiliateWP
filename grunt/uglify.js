// https://github.com/gruntjs/grunt-contrib-uglify
module.exports = {
	theme: {
		options: {
			sourceMap: false,
			mangle: false
		},
		files: [
			{
				expand: true,
				cwd: 'assets/js',
				src: [
					'*.js',
					'!*.min.js'
				],
				dest: 'assets/js',
				ext: '.min.js',
				extDot: 'last'
			}
		]
	}
};
