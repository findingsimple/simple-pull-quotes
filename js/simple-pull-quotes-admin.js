// Global variables to keep track on the canvas instance and from what editor
// that opened the Simple Pull Quote popup.
var simple_pull_quotes_canvas;
var simple_pull_quotes_caller;

jQuery(document).ready(function($){

	$(function() {

		$( '#simple-pull-quotes-dialog' ).dialog({
			autoOpen: false,
			modal: true,
			dialogClass: 'wp-dialog',
			buttons: {
				Cancel: function() {

					$( this ).dialog('close');

				},
				Insert: function() {

					$(this).dialog('close');

					var QuoteToInsert = '[pullquote';

					if ( typeof pullquoteFields != 'undefined' ) {

						$.each( pullquoteFields,function(id,label) {

							if( $('#pull-quote-'+id).val().length != 0 ){

								QuoteToInsert += ' '+id+'="'+$('#pull-quote-'+id).val()+'"';

								$('#pull-quote-'+id).val('');

							}

						});

					}

					QuoteToInsert += ']';

					QuoteToInsert += simple_pull_quotes_canvas.selection.getContent();

					QuoteToInsert += '[/pullquote]';

					// HTML editor
					if (simple_pull_quotes_caller == 'html') {
						QTags.insertContent(QuoteToInsert);
					} else { // Visual Editor
						simple_pull_quotes_canvas.execCommand('mceInsertContent', false, QuoteToInsert);
					}

				}
			},

			width: 400,

		});

	});
	
	if ( typeof QTags != 'undefined' ) {

		QTags.addButton('simple_pull_quotes_id','pullquote',function(){

			simple_pull_quotes_caller = 'html';

			jQuery('#simple-pull-quotes-dialog').dialog('open');

		});	
	}	

});