// Docu : http://www.tinymce.com/wiki.php/API3:tinymce.api.3.x

(function() {

	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('simple_pull_quotes');
	
	tinymce.create('tinymce.plugins.simple_pull_quotes', {

		init : function(ed, url) {

			// Register the command so that it can be invoked from the button
			ed.addCommand('mce_simple_pull_quotes', function() {
				simple_pull_quotes_canvas = ed;
				simple_pull_quotes_caller = 'visual';
				jQuery( "#simple-pull-quotes-dialog" ).dialog( "open" );
			});

			// Register example button
			ed.addButton('simple_pull_quotes', {
				title : 'simple_pull_quotes.desc',
				cmd : 'mce_simple_pull_quotes',
				image : url + '/simple-pull-quotes.png'
			});

		},

		/**
		 * Returns information about the plugin as a name/value array.
		 */
		getInfo : function() {
			return {
					longname  : 'Simple Pull Quotes',
					author 	  : 'Finding Simple',
					authorurl : 'http://findingsimple.com/',
					infourl   : 'http://findingsimple.com/',
					version   : '1.0'
			};
		}

	});

	// Register plugin
	tinymce.PluginManager.add('simple_pull_quotes', tinymce.plugins.simple_pull_quotes);

})();
