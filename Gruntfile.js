module.exports = function (grunt) {
	grunt.initConfig({
		compress: {
			main: {
			  options: {
				archive: 'metronet-profile-picture.zip'
			  },
			  files: [
				{src: ['readme.txt'], dest: '/', filter: 'isFile'},
				{src: ['index.php'], dest: '/', filter: 'isFile'},
				{src: ['metronet-profile-picture.php'], dest: '/', filter: 'isFile'},
				{src: ['css/**'], dest: '/'},
				{src: ['dist/**'], dest: '/'},
				{src: ['gutenberg/**'], dest: '/'},
				{src: ['img/**'], dest: '/'},
				{src: ['js/**'], dest: '/'},
				{src: ['languages/**'], dest: '/'},
				{src: ['includes/**'], dest: '/'},
				{src: ['libraries/**'], dest: '/'},
				{src: ['images/**'], dest: '/'},
			  ]
			}
		  }
	  });
	  grunt.registerTask('default', ["compress"]);

 
 
	grunt.loadNpmTasks( 'grunt-contrib-compress' );
   
 };
