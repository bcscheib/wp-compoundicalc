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
    
	CompoundiCalc_Core::init( $options );

	if ( is_admin() ) {
		require_once dirname( __FILE__ ) . '/admin.php';
		new CompoundCalculator_Options_Page( __FILE__, $options );
	}
}
scb_init( '_pagenavi_init' );

