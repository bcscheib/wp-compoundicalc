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

function _pagenavi_init() {
	load_plugin_textdomain( 'wp-compoundicalc' );

	require_once dirname( __FILE__ ) . '/core.php';

	$options = new scbOptions( 'compoundicalc_options', __FILE__, array(
		/* form values */
		'tab_text'          => 'Regular Deposit/Withdrawal',
		'principle_text'    => 'Base Amount',
		'apr_text'          => 'Annual Interest Rate',
		'period_text'       => 'Calculation Period',
		'period_suffix'     => 'years',
		'deposit_text'      => 'Regular Monthly?',
		
		/* table values */
		'results_tab_text'  => 'Calculation Results',
		'graphs_tab_text'   => 'Graphs of Results'
	) );
    
	CompoundiCalc_Core::init( $options );

	if ( is_admin() ) {
		require_once dirname( __FILE__ ) . '/admin.php';
		new CompoundCalculator_Options_Page( __FILE__, $options );
	}
}
scb_init( '_pagenavi_init' );

