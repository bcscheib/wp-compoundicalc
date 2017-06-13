<?php
	
function wp_compoundicalc_session_start()
{
  if( !session_id() )
  	session_start();
}

add_action('init', 'wp_compoundicalc_session_start');
	
function wp_compoundicalc_post() {
	$page_id = isset($_REQUEST['origin']) ? intval($_REQUEST['origin']) : null;
	
	if( isset($_POST['calc' . $page_id]) ) {
		$post_prefix_text = 'calc' . $page_id;
		$post_prefix = $_POST[$post_prefix_text];
	
		if( isset($post_prefix['apr']) ) {
			$_SESSION[$post_prefix_text . '_apr'] = floatval($post_prefix['apr']);
		}
		
		if( isset($post_prefix['principle']) ) {
			$_SESSION[$post_prefix_text . '_principle'] = floatval($post_prefix['principle']);
		}
	
		if( isset($post_prefix['deposit']) ) {
			$_SESSION[$post_prefix_text . '_deposit'] = floatval($post_prefix['deposit']);
		}
	
		if( isset($post_prefix['years']) ) {
			$_SESSION[$post_prefix_text . '_years'] = intval($post_prefix['years']);
		}
	
		if( isset($post_prefix['inflation_rate'])  ) {
			$_SESSION[$post_prefix_text . '_inflation_rate'] = floatval($post_prefix['inflation_rate']);
		}
		
		if( isset($post_prefix['deposit_op']) ) {
			$_SESSION[$post_prefix_text . '_deposit_op'] = $post_prefix['deposit_op'];
		}
	} 
	$url = !is_null($page_id) ? get_permalink($page_id) : '/';
	wp_redirect($url);
    exit();
}

function wp_compoundicalc( $args = array() ) {
	
	if (!session_id()) {
	    session_start();
	}
	
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
	
	$page_id = get_the_id();
	$session_prefix = 'calc' . $page_id;
	
	
	$calculator_action = 'wp-compoundicalc';
	$deposit = isset($_SESSION[$session_prefix . '_deposit']) ? abs(floatval($_SESSION[$session_prefix . '_deposit'])) : null;
	$deposit_op = isset($_SESSION[$session_prefix . '_deposit_op']) && $_SESSION[$session_prefix . '_deposit_op'] === '-' ? $_SESSION[$session_prefix . '_deposit_op'] : '+';
	
	$deposit_amt = $deposit;
	if( $deposit_op === '-' ) {
		$deposit_amt = $deposit * -1;
	}
	
	
	$principle = isset($_SESSION[$session_prefix . '_principle']) ? $_SESSION[$session_prefix . '_principle'] : null;
	$apr = isset($_SESSION[$session_prefix . '_apr']) ? floatval($_SESSION[$session_prefix . '_apr']) / 100 : null;
	$years = isset($_SESSION[$session_prefix . '_years']) ? $_SESSION[$session_prefix . '_years'] : null;
	$periods = isset($_SESSION[$session_prefix . '_periods']) ? $_SESSION[$session_prefix . '_periods'] : 12;
	$inflation_rate = isset($_SESSION[$session_prefix . '_inflation_rate']) ? floatval($_SESSION[$session_prefix . '_inflation_rate']) / 100 : null;
	
	
	$instance = new Calculator( $args );
	$schedule = $instance->calculate_schedule($deposit_amt, $principle, $apr, $periods, $years, $inflation_rate);
	
	
	$texts = $options;
	
	$calculator_action_url = admin_url('admin-post.php');
	
	    
	$tmpl = CompoundiCalc_Core::start_template();
	require_once(__DIR__ . '/_view.php');
	CompoundiCalc_Core::end_template();
	
	
	$out = $before . "<" . $wrapper_tag . " class='" . $wrapper_class . "'>\n" . $tmpl->body . "\n</" . $wrapper_tag . ">" . $after;

	return apply_filters( 'wp_compoundicalc', $out );
}

add_action( 'admin_post_wp-compoundicalc', 'wp_compoundicalc_post' );
add_action( 'admin_post_nopriv_wp-compoundicalc', 'wp_compoundicalc_post' );

class CompoundiCalc_Core {
	static $options;

	static function init( $options ) {
		
		self::$options = $options;

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'stylesheets' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'scripts' ) );
		add_shortcode('compound_calculator', 'wp_compoundicalc');
	}

	static function stylesheets() {

		if ( @file_exists( get_stylesheet_directory() . '/wp-compoundicalc-css.css' ) )
			$css_file = get_stylesheet_directory_uri() . '/wp-compoundicalc-css.css';
		elseif ( @file_exists( get_template_directory() . '/wp-compoundicalc-css.css' ) )
			$css_file = get_template_directory_uri() . '/wp-compoundicalc-css.css';
		else
			$css_file = plugins_url( 'wp-compoundicalc-css.css', __FILE__ );

		wp_enqueue_style( 'wp-compoundicalc', $css_file, false, '2.70' );
	}
	
	static function scripts() {

		if ( @file_exists( get_stylesheet_directory() . '/wp-compoundicalc-js.js' ) )
			$js_file = get_stylesheet_directory_uri() . '/wp-compoundicalc-js.js';
		elseif ( @file_exists( get_template_directory() . '/wp-compoundicalc-js.js' ) )
			$js_file = get_template_directory_uri() . '/wp-compoundicalc-js.js';
		else
			$js_file = plugins_url( '/wp-compoundicalc-js.js', __FILE__ );

// 		wp_enqueue_script('jquery');
		wp_enqueue_script( 'gcharts', 'https://www.gstatic.com/charts/loader.js', array('jquery'), '3.3.5', true );
		wp_enqueue_script( 'wp-compoundicalc', $js_file, array('gcharts'), '2.70' );
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