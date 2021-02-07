/* jshint node:true */
module.exports = function( grunt ) {
	"use strict";

	// Project configuration
	grunt.initConfig( {

		pkg: grunt.file.readJSON( "package.json" ),

		// setting folder templates
		dirs: {
			css: "assets/css",
			less: "assets/css",
			js: "assets/js"
		},

		// Compile all .less files.
		less: {
			compile: {
				options: {
					// These paths are searched for @imports
					paths: ["<%= less.css %>/"]
				},
				files: [{
					expand: true,
					cwd: "<%= dirs.css %>/",
					src: [
						"*.less",
						"!mixins.less"
					],
					dest: "<%= dirs.css %>/",
					ext: ".css"
				}]
			}
		},

		// Minify all .css files.
		cssmin: {
			minify: {
				expand: true,
				cwd: "<%= dirs.css %>/",
				src: ["*.css"],
				dest: "<%= dirs.css %>/",
				ext: ".css"
			}
		},

		// Minify .js files.
		uglify: {
			options: {
				preserveComments: "some"
			},
			jsfiles: {
				files: [{
					expand: true,
					cwd: "<%= dirs.js %>/",
					src: [
						"*.js",
						"!*.min.js",
						"!Gruntfile.js",
					],
					dest: "<%= dirs.js %>/",
					ext: ".min.js"
				}]
			}
		},

		// Watch changes for assets
		watch: {
			less: {
				files: [
					"<%= dirs.less %>/*.less",
				],
				tasks: ["less", "cssmin"],
			},
			js: {
				files: [
					"<%= dirs.js %>/*js",
					"!<%= dirs.js %>/*.min.js"
				],
				tasks: ["uglify"]
			}
		},

		addtextdomain: {
			options: {
				textdomain: "wp-plugin-template",
			},
			update_all_domains: {
				options: {
					updateDomains: true
				},
				src: [ "*.php", "**/*.php", "!\.git/**/*", "!bin/**/*", "!node_modules/**/*", "!tests/**/*" ]
			}
		},

		makepot: {
			target: {
				options: {
					domainPath: "/lang",
					exclude: [ "\.git/*", "bin/*", "node_modules/*", "tests/*" ],
					mainFile: "wp-plugin-template.php",
					potFilename: "wp-plugin-template.pot",
					potHeaders: {
						poedit: true,
						"x-poedit-keywordslist": true
					},
					type: "wp-plugin",
					updateTimestamp: true
				}
			}
		},
	} );

	// Load NPM tasks to be used here
	grunt.loadNpmTasks( "grunt-contrib-less" );
	grunt.loadNpmTasks( "grunt-contrib-cssmin" );
	grunt.loadNpmTasks( "grunt-contrib-uglify" );
	grunt.loadNpmTasks( "grunt-contrib-watch" );
	grunt.loadNpmTasks( "grunt-wp-i18n" );

	// Register tasks
	grunt.registerTask( "default", [ "less", "cssmin", "uglify" ] );
	grunt.registerTask( "i18n", ["addtextdomain", "makepot"] );

	grunt.util.linefeed = "\n";

};
