module.exports = function(grunt) {
    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        // Task for JS minification
        uglify: {
            target: {
                files: {
                    'assets/js/script.min.js': ['assets/js/script.js']
                }
            }
        }
    });

    // Load the plugin for JS minification
    grunt.loadNpmTasks('grunt-contrib-uglify');

    // Default task for JS minification
    grunt.registerTask('default', ['uglify']);
};
