<?php
/*
Plugin Name: Simple Pull Quotes
Plugin URI: http://plugins.findingsimple.com
Description: Simple plugin for helping insert pull quotes 
Version: 1.0
Author: Finding Simple
Author URI: http://findingsimple.com
License: GPL2
*/
/*
Copyright 2014  Finding Simple  (email : plugins@findingsimple.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! class_exists( 'Simple_Pull_Quotes' ) ) {

/**
 * Plugin Main Class.
 *
 */
class Simple_Pull_Quotes {

	private static $pullquote_fields;
	
	/**
	 * Initialise
	 */
	function Simple_Pull_Quotes() {

		self::$pullquote_fields = array(
			'align' => __( 'Alignment' ),
			'back' => __( 'Back' ),
			'forward' => __( 'Forward' ),
			'width' => __( 'Width' ),
			'wrap' => __( 'Wrap' ),
			'cite' => __( 'Citation')
		);
		
		add_action( 'init', array( $this , 'simple_pull_quotes_init' ) );
			   
		add_shortcode( 'pullquote', array( $this, 'pullquote' ) );

	}

	/**
	 * Apply appropriate hooks and filters
	 */
	function simple_pull_quotes_init() {

		if ( !is_admin() ) {

			add_action( 'wp_enqueue_scripts', array( $this, 'add_simple_pull_quotes_script' ) );

			add_filter( 'the_content', array( $this, 'shortcode_empty_paragraph_fix' ) );

		}

		if ( ( current_user_can('edit_posts') || current_user_can('edit_pages') ) && get_user_option('rich_editing') ) {
		  
			add_action( 'admin_enqueue_scripts', array( $this, 'add_simple_pull_quotes_admin_scripts' ) );
 
			add_action( 'admin_footer', array( $this, 'the_jquery_dialog_markup' ) );

			add_filter( 'mce_external_plugins', array( $this, 'mce_external_plugins' ) );
		
			add_filter( 'mce_buttons', array( $this, 'mce_buttons' ) );
		
		}
		
	}  

	/**
	 * Add tinymce plugin
	 */
	function mce_external_plugins( $plugin_array ) {

		$plugin_array['simple_pull_quotes'] = plugins_url( '/tinymce/editor_plugin.js', __FILE__ );

		return $plugin_array;

	}

	/**
	 * Add pull quote button WordPress editor
	 */
	function mce_buttons( $buttons ) {

		if ( ! in_array( 'simple_pull_quotes', $buttons ) )
			array_push( $buttons, 'simple_pull_quotes' );

		return $buttons;

	}

	/**
	 * Register the front end script for displaying when a pull quote is used
	 */
	function add_simple_pull_quotes_script() {

		wp_register_script( 'simple-pull-quotes', plugins_url( 'js/simple-pull-quotes.js', __FILE__ ), array( 'jquery' ), '1', true );
	
	}

	/**
	 * Add admin scripts
	 */
	function add_simple_pull_quotes_admin_scripts() {
 
		global $pagenow;

		if ( is_admin() && $pagenow == 'post-new.php' || $pagenow == 'post.php' ) {

			/**
			 * Add dashboard stylesheets
			 */
			wp_enqueue_style( 'wp-jquery-ui-dialog' );

			/**
			 * Add admin scripts
			 */
			wp_enqueue_script( 'simple-pull-quotes-admin', plugins_url( '/js/simple-pull-quotes-admin.js', __FILE__ ), array( 'jquery-ui-dialog', 'jquery-ui-tabs' ), '1', true );

			wp_localize_script( 'simple-pull-quotes-admin', 'pullquoteFields', self::$pullquote_fields );
		
		}

	}

	/**
	 * Add pullquote shortcode
	 */
	function pullquote( $attr, $content = '' ) {

		/**
		 * Enqueue the front end script when pull quotes are used
		 */
		wp_enqueue_script( 'simple-pull-quotes' );

		$defaults = array(
			'align'   => 'left',
			'back'    => 0,
			'forward' => 0,
			'width'   => '',
			'wrap'    => '',
			'cite'    => ''
		);

		$attr = shortcode_atts( $defaults, $attr );

		$attr['align'] = strtolower( $attr['align'] );

		if ( ! in_array( $attr['align'], array( 'left', 'right', '' ) ) )
			$attr['align'] = 'left';

		if ( ! empty( $attr['align'] ) )
			$attr['align'] = ' align' . $attr['align'];

		$data = '';

		if ( ! empty( $attr['back'] ) )
			$data = ' data-back="' . absint( $attr['back'] ) . '"';
		elseif ( ! empty( $attr['forward'] ) )
			$data = ' data-forward="' . absint( $attr['forward'] ) . '"';

		if ( ! empty( $attr['width'] ) )
			$data .= ' style="width:' . esc_attr( $attr['width'] ) . '"';

		if ( ! empty( $attr['wrap'] ) )
			$data .= ' data-wrap="' . esc_attr( $attr['wrap'] ) . '"';

		if ( ! empty( $attr['cite'] ) )
			$data .= ' data-cite="' . esc_attr( $attr['cite'] ) . '"';

		return Simple_Pull_Quotes::remove_wpautop( '<span class="pullquote' . $attr['align'] . '"' . $data . ' ' . $cite . '>'. do_shortcode( $content ) .'</span>' );
 
	}

	/**
	 * Build jQuery UI Window.
	 */
	function the_jquery_dialog_markup() {

		$screen = get_current_screen();

		if ( $screen->base != 'post' )
				return;
		?>
<div class="hidden">
	<div id="simple-pull-quotes-dialog" title="Insert Pull Quote">
		<div id="pull-quote-details" class="clear clearfix" style="margin: 1em; clear:both">
		<?php foreach ( self::$pullquote_fields as $field_id => $field_label ) { ?>
			<?php if ( $field_id == 'align' ) { ?>
			<div style="display: inline-block; width: 100%; margin: 2px;">
			<label for="pull-quote-<?php echo $field_id; ?>" style="display: inline-block; width: 20%;">
				<?php echo $field_label; ?></label>
				<select id="pull-quote-<?php echo $field_id; ?>" name="pull-quote-<?php echo $field_id; ?>" style="width: 75%; float: right;" >
					<option></option>
					<option value="left">Left</option>
					<option value="right">Right</option>
				</select> 
			</div>
		   <?php } elseif ( $field_id == 'wrap' ) { ?>
			<div style="display: inline-block; width: 100%; margin: 2px;">
			<label for="pull-quote-<?php echo $field_id; ?>" style="display: inline-block; width: 20%;">
				<?php echo $field_label; ?></label>
				<select id="pull-quote-<?php echo $field_id; ?>" name="pull-quote-<?php echo $field_id; ?>" style="width: 75%; float: right;" >
					<option></option>
					<option value="true">True</option>
				</select> 
			</div>
			<?php } else { ?>
			<label for="pull-quote-<?php echo $field_id; ?>" style="display: inline-block; width: 100%; margin: 2px;">
				<?php echo $field_label; ?>
				<input type="text" id="pull-quote-<?php echo $field_id; ?>" name="pull-quote-<?php echo $field_id; ?>" value=""  style="width: 75%; float: right;"/>
			</label>
			<?php } ?>
		<?php } ?>
		</div><!-- #pull-quote-details -->
	</div><!-- #simple-pull-quotes-dialog -->
</div><!-- .hidden -->
<?php
	}

	/**
	 * Replaces WP autop formatting 
	 *
	 */
	function remove_wpautop($content) {
		$content = do_shortcode( shortcode_unautop( $content ) ); 
		$content = preg_replace( '#^<\/p>|^<br \/>|<p>$#', '', $content);
		return $content;
	}

	/**
	 * Shortcode Empty Paragraph Fix
	 */
	function shortcode_empty_paragraph_fix( $content ) {

		$array = array (
			'<p>[' => '[',
			']</p>' => ']',
			']<br />' => ']'
		);

		$content = strtr( $content, $array );

		return $content;
	}

}

$Simple_Pull_Quotes = new Simple_Pull_Quotes();

}