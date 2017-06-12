<?php

class CompoundCalculator_Options_Page extends scbAdminPage {

	function setup() {
		$this->textdomain = 'wp-compoundicalc';

		$this->args = array(
			'page_title' => __( 'Compound Interest Calculator Settings', $this->textdomain ),
			'menu_title' => __( 'Calculator Settings', $this->textdomain ),
			'page_slug' => 'wp-compoundicalc',
		);
	}

	function validate( $new_data, $old_data ) {
		$options = wp_parse_args($new_data, $old_data);
		foreach ( array( 'style', 'num_pages', 'num_larger_page_numbers', 'larger_page_numbers_multiple' ) as $key ) {
			$options[ $key ] = absint( @$options[ $key ] );
		}
		foreach ( array( 'use_pagenavi_css', 'always_show' ) as $key ) {
			$options[ $key ] = intval( @$options[ $key ] );
		}
		foreach ( array( 'pages_text', 'current_text', 'page_text', 'first_text', 'last_text', 'prev_text', 'next_text', 'dotleft_text', 'dotright_text' ) as $key ) {
			$options[ $key ] = wp_kses_post( @$options[ $key ] );
		}
		
		return $options;
	}

	function page_content() {
		$rows = array(
			array(
				'title' => __( 'Tab Text', $this->textdomain ),
				'type' => 'text',
				'name' => 'tab_text',
				'extra' => 'size="50"',
				'desc' => '<br />
					' . __( 'Text that appears in the tab above calculator.', $this->textdomain ) . '<br />'
			),
			array(
				'title' => __( 'Principle Label', $this->textdomain ),
				'type' => 'text',
				'name' => 'principle_text',
				'extra' => 'size="50"',
				'desc' => '<br />
					' . __( 'Label for the principle field.', $this->textdomain ) . '<br />'
			),
			array(
				'title' => __( 'APR Label', $this->textdomain ),
				'type' => 'text',
				'name' => 'apr_text',
				'extra' => 'size="50"',
				'desc' => '<br />
					' . __( 'Label for the annual interest rate percentage.', $this->textdomain ) . '<br />'
			),
			array(
				'title' => __( 'Period Text', $this->textdomain ),
				'type' => 'text',
				'name' => 'period_text',
				'extra' => 'size="50"',
				'desc' => '<br />
					' . __( 'Label for the periods (ie months or years).', $this->textdomain ) . '<br />'
			),
			array(
				'title' => __( 'Period Suffix', $this->textdomain ),
				'type' => 'text',
				'name' => 'period_suffix',
				'extra' => 'size="50"',
				'desc' => '<br />
					' . __( 'Label for period type that goes after period input box (ie months or years).', $this->textdomain ) . '<br />'
			),
			array(
				'title' => __( 'Deposit/Withdrawal Label', $this->textdomain ),
				'type' => 'text',
				'name' => 'deposit_text',
				'extra' => 'size="50"',
				'desc' => '<br />
					' . __( 'Label for deposit/withdrawal field.', $this->textdomain ) . '<br />'
			)
		);

		$out =
		 html( 'h3', __( 'Form Labels', $this->textdomain ) ) .
		$this->table( $rows );
		
		$rows = array(
			array(
				'title' => __( 'Results Tab Text', $this->textdomain ),
				'type' => 'text',
				'name' => 'results_tab_text',
				'extra' => 'size="50"',
				'desc' => '<br />
					' . __( 'Text that appears in the tab to show results container.', $this->textdomain ) . '<br />'
			),
			array(
				'title' => __( 'Graphs Tab Text', $this->textdomain ),
				'type' => 'text',
				'name' => 'graphs_tab_text',
				'extra' => 'size="50"',
				'desc' => '<br />
					' . __( 'Text that appears in the tab to show graphs container.', $this->textdomain ) . '<br />'
			),
		);

		$out .=
		 html( 'h3', __( 'Table Labels', $this->textdomain ) ) .
		$this->table( $rows );




		echo $this->form_wrap( $out );
	}
}

