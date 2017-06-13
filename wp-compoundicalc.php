<?php
/*
Plugin Name: Compound Calculator
Plugin URI: http://lesterchan.net/portfolio/programming/php/
Description: Compound interest calculator with shortcode and customizable labels.
Version: 1.0
Author: BenS
Text Domain: wp-compoundicalc
*/

require_once dirname( __FILE__ ) . '/scb/load.php';

function _compoundicalc_init() {
	load_plugin_textdomain( 'wp-compoundicalc' );
	
	$translations = new scbOptions( 'compoundicalc_options', __FILE__, array(
		/* misc values */
		'withdrawal_text'   => 'Withdrawal',
		'deposit_text'   => 'Deposit',
		'years_singular_text'   => 'year',
		'years_plural_text'   => 'years',
		'interest_text'   => 'Interest',
		'total_text'   => 'Total',
		'balance_text'   => 'Balance',
		
		/* form values */
		'tab_text'          => 'Regular Deposit/Withdrawal',
		'principle_text'    => 'Base Amount',
		'apr_text'          => 'Annual Interest Rate',
		'period_text'       => 'Calculation Period',
		'period_suffix'     => 'years',
		'deposit_amount_text'      => 'Regular Monthly?',
		'inflation_rate_text'      => 'Inflation Rate?',
		'calculate_text'      => 'Calculate',
		
		/* table values */
		'results_tab_text'  => 'Calculation Results',
		'graphs_tab_text'   => 'Graphs of Results',
		'graph1_title_text'   => 'Graph 1 - Balance (Compounded Monthly)',
		'graph2_title_text'   => 'Graph 2 - Total Interest (Compounded Monthly)',
	) );
	
	$default_args = array(
		'before' => '',
		'after' => '',
		'wrapper_tag' => 'div',
		'wrapper_class' => 'wp-compoundicalc',
		'query' => $GLOBALS['wp_query'],
		'type' => 'posts',
		'principle' => 25000.00,
		'apr' => 0.03,
		'inflation_rate' => 0,
		'years' => 1,
		'deposit' => 0.0,
		'translate' => true
	);

	add_action('init', function(){
		if( !session_id() )
	  		session_start();
		}
	);
	add_action( 'admin_post_wp-compoundicalc', 'wp_compoundicalc_post' );
	add_action( 'admin_post_nopriv_wp-compoundicalc', 'wp_compoundicalc_post' );
		
	function wp_compoundicalc_post() {
		$page_id = isset($_REQUEST['origin']) ? intval($_REQUEST['origin']) : null;
		
		if( isset($_POST['calc' . $page_id]) ) {
			$post_prefix_text = 'calc' . $page_id;
			$post_prefix = $_POST[$post_prefix_text];
			
			if( isset($post_prefix['apr']) ) {
				$_SESSION[$post_prefix_text . '_apr'] = floatval($post_prefix['apr']) / 100;
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
				$_SESSION[$post_prefix_text . '_inflation_rate'] = floatval($post_prefix['inflation_rate']) / 100;
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
		require_once( __DIR__ . '/lib/calculator.php' );
		
		CompoundiCalc_Core::set_prop('args', CompoundiCalc_Core::merge_args($args) );
		CompoundiCalc_Core::set_prop('session_prefix', 'calc' . get_the_id());
		CompoundiCalc_Core::set_prop('action', 'wp-compoundicalc');
		
		extract(CompoundiCalc_Core::$args, EXTR_SKIP );
		
		$deposit_amt = CompoundiCalc_Core::floatize_prop('deposit');   
		$apr = CompoundiCalc_Core::floatize_prop('apr');   
		$inflation_rate = CompoundiCalc_Core::floatize_prop('inflation_rate');   
		$principle = CompoundiCalc_Core::intize_prop('principle');   
		$years = CompoundiCalc_Core::intize_prop('years');   
		
		$deposit_op = isset($_SESSION[$session_prefix . '_deposit_op']) && $_SESSION[CompoundiCalc_Core::$session_prefix . '_deposit_op'] === '-' ? $_SESSION[CompoundiCalc_Core::$session_prefix . '_deposit_op'] : '+';
		
		if( $deposit_op === '-' ) {
			$deposit_amt = $deposit_amt * -1;
		}
		
		CompoundiCalc_Core::reset_session();
		
		$instance = new Calculator();
		$schedule = $instance->calculate_schedule($deposit_amt, $principle, $apr, 12, $years, $inflation_rate);
		    
		$tmpl = CompoundiCalc_Core::start_template();
		require_once(__DIR__ . '/_view.php');
		CompoundiCalc_Core::end_template();
		
		
		$out = $before . "<" . $wrapper_tag . " class='" . $wrapper_class . "'>\n" . $tmpl->body . "\n</" . $wrapper_tag . ">" . $after;
	
		return apply_filters( 'wp_compoundicalc', $out );
	}

	class CompoundiCalc_Core {
		
		public static $action;
		public static $args;
		public static $default_args;
		public static $session_prefix;
		public static $translations;
		
		static function set_prop($name, $val) {
			$class_name = get_called_class();
			$class = new ReflectionClass($class_name);
			$class->setStaticPropertyValue($name, $val);
		}
	
		static function init( $translations = array(), $default_args = array()) {
			static::$translations = $translations;
			static::$default_args = $default_args;
			static::$args = $default_args;
			
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'stylesheets' ) );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'scripts' ) );
			add_shortcode('compound_calculator', 'wp_compoundicalc');
		}
		
		static function merge_args($args) {
			return wp_parse_args( $args, static::$default_args );
		}
		
		static function reset_session() {
			foreach($_SESSION as $key => $val) {
				if( 0 == strpos($key, $_SESSION[static::$session_prefix]) ) {
					unset($_SESSION[$key]);
				}
			}
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
	
			wp_enqueue_script( 'gcharts', 'https://www.gstatic.com/charts/loader.js', array('jquery'), '3.3.5', true );
			wp_enqueue_script( 'wp-compoundicalc', $js_file, array('gcharts'), '2.70' );
		}
		
		static function labelize($name, $filter_function = null) {
			$dont_translate = static::$args['translate'] === false || static::$args['translate'] === "false";
			$val = $dont_translate ? static::$translations->get_defaults($name) : static::$translations->get($name);
			
			$val = function_exists('sanitize_text_field') ? trim(sanitize_text_field($val)) : trim($val); // sanitize these values 
			
			if( function_exists($filter_function) ) {
				$val = call_user_func_array($filter_function, array($val) );
			}
			echo $val;
		}
		
		static function floatize_prop($prop) {
			return isset($_SESSION[static::$session_prefix . '_' . $prop]) ? abs(floatval($_SESSION[static::$session_prefix . '_' . $prop])) : abs(floatval(static::$args[$prop]));
		}
		
		static function intize_prop($prop) {
			return isset($_SESSION[static::$session_prefix . '_' . $prop]) ? intval($_SESSION[static::$session_prefix . '_' . $prop]) : intval(static::$args[$prop]);
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

	CompoundiCalc_Core::init( $translations, $default_args );

	if ( is_admin() ) {
		require_once dirname( __FILE__ ) . '/admin.php';
		new CompoundCalculator_Options_Page( __FILE__, $options );
	}
}
scb_init( '_compoundicalc_init' );

