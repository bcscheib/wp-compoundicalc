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
		$out = '';
		
		$rows = array(
			array(
				'title' => __( 'Withdrawal Text', $this->textdomain ),
				'type' => 'text',
				'name' => 'withdrawal_text',
				'extra' => 'size="50"',
				'desc' => '<br />
					' . __( 'Translation for the word withdrawal.', $this->textdomain ) . '<br />'
			),
			array(
				'title' => __( 'Deposit Text', $this->textdomain ),
				'type' => 'text',
				'name' => 'deposit_text',
				'extra' => 'size="50"',
				'desc' => '<br />
					' . __( 'Translation for the word deposit.', $this->textdomain ) . '<br />'
			),
			array(
				'title' => __( 'Years Singular Text', $this->textdomain ),
				'type' => 'text',
				'name' => 'years_singular_text',
				'extra' => 'size="50"',
				'desc' => '<br />
					' . __( 'Translation for the word year in singular form.', $this->textdomain ) . '<br />'
			),
			array(
				'title' => __( 'Years Plural Text', $this->textdomain ),
				'type' => 'text',
				'name' => 'years_plural_text',
				'extra' => 'size="50"',
				'desc' => '<br />
					' . __( 'Translation for the word year in plural form.', $this->textdomain ) . '<br />'
			),
			array(
				'title' => __( 'Interest Text', $this->textdomain ),
				'type' => 'text',
				'name' => 'interest_text',
				'extra' => 'size="50"',
				'desc' => '<br />
					' . __( 'Translation for the word interest in plural form.', $this->textdomain ) . '<br />'
			),
			array(
				'title' => __( 'Total Text', $this->textdomain ),
				'type' => 'text',
				'name' => 'total_text',
				'extra' => 'size="50"',
				'desc' => '<br />
					' . __( 'Translation for the word total.', $this->textdomain ) . '<br />'
			),
			array(
				'title' => __( 'BalanceText', $this->textdomain ),
				'type' => 'text',
				'name' => 'balance_text',
				'extra' => 'size="50"',
				'desc' => '<br />
					' . __( 'Translation for the word balance.', $this->textdomain ) . '<br />'
			),
		);

		$out .=
		 html( 'h3', __( 'Miscellaneous Translations', $this->textdomain ) ) .
		$this->table( $rows );
		
		
		
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
				'title' => __( 'Deposit/Withdrawal Label', $this->textdomain ),
				'type' => 'text',
				'name' => 'deposit_amount_text',
				'extra' => 'size="50"',
				'desc' => '<br />
					' . __( 'Label for deposit/withdrawal field.', $this->textdomain ) . '<br />'
			),
			array(
				'title' => __( 'Inflation Rate Label', $this->textdomain ),
				'type' => 'text',
				'name' => 'inflation_rate_text',
				'extra' => 'size="50"',
				'desc' => '<br />
					' . __( 'Label for inflation rate field.', $this->textdomain ) . '<br />'
			),
			array(
				'title' => __( 'Calculate Button', $this->textdomain ),
				'type' => 'text',
				'name' => 'calculate_text',
				'extra' => 'size="50"',
				'desc' => '<br />
					' . __( 'Text for the form calculate button.', $this->textdomain ) . '<br />'
			)
		);

		$out .=
		 html( 'h3', __( 'Form Label/Button Translations', $this->textdomain ) ) .
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

