<?php

/**
 * Template tag: Boxed Style Paging
 *
 * @param array $args:
 *  'before': (string)
 *  'after': (string)
 *  'options': (string|array) Used to overwrite options set in WP-Admin -> Settings -> PageNavi
 *
 * @return void|string
 */
function wp_compoundicalc( $args = array() ) {
	
	if ( !is_array( $args ) ) {
		$argv = func_get_args();

		$args = array();
		foreach ( array( 'before', 'after', 'options' ) as $i => $key ) {
			$args[ $key ] = isset( $argv[ $i ]) ? $argv[ $i ] : '';
		}
	}

	$args = wp_parse_args( $args, array(
		'before' => '',
		'after' => '',
		'wrapper_tag' => 'div',
		'wrapper_class' => 'wp-compoundicalc',
		'options' => array(),
		'query' => $GLOBALS['wp_query'],
		'type' => 'posts',
		'echo' => true
	) );

	extract( $args, EXTR_SKIP );

	$options = wp_parse_args( $options, CompoundiCalc_Core::$options->get() );
	
	require_once( __DIR__ . '/lib/calculator.php' );

	$instance = new Calculator( $args );
	
	
	$tab_text = $options['tab_text'];
	$principle_text = $options['principle_text'];
	$apr_text = $options['apr_text'];
	$period_text = $options['period_text'];
	$period_suffix = $options['period_suffix'];
	
    
	$tmpl = CompoundiCalc_Core::start_template();
	require_once(__DIR__ . '/_view.php');
	CompoundiCalc_Core::end_template();
	
	
	$out = $before . "<" . $wrapper_tag . " class='" . $wrapper_class . "'>\n" . $tmpl->body . "\n</" . $wrapper_tag . ">" . $after;

	return apply_filters( 'wp_compoundicalc', $out );
}




# http://core.trac.wordpress.org/ticket/16973
if ( !function_exists( 'get_multipage_link' ) ) :
	function get_multipage_link( $page = 1 ) {
		global $post, $wp_rewrite;

		if ( 1 == $page ) {
			$url = get_permalink();
		} else {
			if ( '' == get_option('permalink_structure') || in_array( $post->post_status, array( 'draft', 'pending') ) )
				$url = add_query_arg( 'page', $page, get_permalink() );
			elseif ( 'page' == get_option( 'show_on_front' ) && get_option('page_on_front') == $post->ID )
				$url = trailingslashit( get_permalink() ) . user_trailingslashit( $wp_rewrite->pagination_base . "/$page", 'single_paged' );
			else
				$url = trailingslashit( get_permalink() ) . user_trailingslashit( $page, 'single_paged' );
		}

		return $url;
	}
endif;

// Template tag: Drop Down Menu (Deprecated)
function wp_compoundicalc_dropdown() {
	wp_compoundicalc();
}


class CompoundiCalc_Core {
	static $options;

	static function init( $options ) {
		
		self::$options = $options;

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'stylesheets' ) );
		add_shortcode('compound_calculator', 'wp_compoundicalc');
	}

	static function stylesheets() {
/*
		if ( !self::$options->use_pagenavi_css )
			return;
*/

		if ( @file_exists( get_stylesheet_directory() . '/wp-compoundicalc-css.css' ) )
			$css_file = get_stylesheet_directory_uri() . '/wp-compoundicalc-css.css';
		elseif ( @file_exists( get_template_directory() . '/wp-compoundicalc-css.css' ) )
			$css_file = get_template_directory_uri() . '/wp-compoundicalc-css.css';
		else
			$css_file = plugins_url( 'wp-compoundicalc-css.css', __FILE__ );

		wp_enqueue_style( 'wp-compoundicalc', $css_file, false, '2.70' );
	}
	
	static function start_template() {
		$tmpl = new stdClass();
		$tmpl->body = '';
		ob_start(function($buffer) use ($tmpl) {
			$tmpl->body = $buffer;
			return $buffer;
		});
		return $tmpl;
	}
	
	static function end_template() {
		ob_end_clean();
	}	
	
	static function evaluate_template($tmpl, $key_values = array() ) {
		$body = $tmpl->body;
		foreach( $key_values as $key => $value ) {
			$body = str_replace("#$key#", $value, $body);
		}
		return $body;
	}
}

