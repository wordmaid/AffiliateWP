/* global require, process */
module.exports = function( grunt ) {
	// Load Grunt plugin configurations
	require( 'load-grunt-config' )( grunt, {
		data: {
			pkg: grunt.file.readJSON( 'package.json' )
		}
	} );

	grunt.initConfig( {
		exec: {
			make_docs: {
				cmd: './docs.sh'
			}
		}
	} );

	grunt.registerTask( 'docs', [ 'exec:make_docs' ] );
};
