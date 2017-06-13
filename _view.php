<?
	if( !isset($principle) ) {
		$principle = 0;
	}	
	if( !isset($apr) ) {
		$apr = 0.00;
	}
	if( !isset($inflation_rate) ) {
		$inflation_rate = 0.00;
	}
	if( !isset($deposit_op) ) {
		$deposit_op = '+';
	}
	if( !isset($schedule) || !isset($schedule['years']) ) {
		$schedule = array('years' => array());
	}
	
	if( !isset($calculator_action_url) || !is_string($calculator_action_url) ) {
		$calculator_action_url = '';
	}
	
	if( !isset($calculator_action) || !is_string($calculator_action) ) {
		$calculator_action = '';
	}
	
	$form_prefix = !empty($calculator_action) && function_exists('the_ID') ? 'calc' . get_the_ID() : 'calc';
	
	if( !isset($texts) || !is_array($texts) ) {
		$texts = array();
	}
	function wp_compoundicalc_label($name, $texts = array(), $default = '', $filter_function = null) {
		$val = isset($texts[$name]) && is_string($texts[$name]) && !empty($texts[$name]) ? trim($texts[$name]) : $default;
		
		// sanitize these values 
		$val = function_exists('sanitize_text_field') ? sanitize_text_field($val) : $val;
		
		if( function_exists($filter_function) ) {
			$val = call_user_func_array($filter_function, array($val) );
		}
		echo $val;
	}
?>
<div class="wp-compoundicalc-body">
	<ul class="calculator_tabs">
		<li class="calculator_tab_active"><a href="javascript:;"><?php wp_compoundicalc_label('tab_text', $texts, 'Regular Deposit / Withdrawal') ?></a></li>
	</ul>
	<form method="POST" class="calculator_form" action="<?php echo  $calculator_action_url ?>">
		
		<?php if( !empty($calculator_action) && function_exists('the_ID') ) { ?>
			<input type="hidden" name="action" value="<?php echo $calculator_action ?>" />
			<input type="hidden" name="origin" value="<?php the_ID(); ?>">
		<? } ?>
		
		<div class="form_field">
			<label><?php wp_compoundicalc_label('principle_text', $texts, 'Base Amount') ?></label>
			<input type="number" name="<?php echo $form_prefix; ?>[principle]" value="<?php echo  $principle ?>" />
		</div>
		<div class="form_field">
			<label><?php wp_compoundicalc_label('apr_text', $texts, 'Base Amount') ?></label>
			<input type="number" name="<?php echo $form_prefix; ?>[apr]" value="<?php echo  is_null($apr) ? null : $apr * 100 ?>" step="0.01" />
			<span>%</span>
		</div>
		<div class="form_field">
			<label><?php wp_compoundicalc_label('period_text', $texts, 'Calculation Period') ?></label>
			<input type="number" name="<?php echo $form_prefix; ?>[years]" value="<?php echo  $years ?>" />
			<span><?php wp_compoundicalc_label('years_plural_text', $texts, 'years', 'strtolower') ?></span>
		</div>
		<div class="form_field">
			<label class="not_required"><?php wp_compoundicalc_label('deposit_amount_text', $texts, 'Regular Monthly') ?></label>
			<input type="number" name="<?php echo $form_prefix; ?>[deposit]" value="<?php echo  $deposit ?>" step="0.01" />
			<select name="<?php echo $form_prefix; ?>[deposit_op]">
				<option value="-"<?php echo  $deposit_op === '-' ? ' selected' : '' ?>><?php wp_compoundicalc_label('withdrawal_text', $texts, 'Withdrawal', 'ucwords') ?></option>
				<option value=""<?php echo  $deposit_op !== '-' ? 'selected' : '' ?>><?php wp_compoundicalc_label('deposit_text', $texts, 'Deposit', 'ucwords') ?></option>
			</select>
		</div>
		<div class="form_field">
			<label class="not_required"><?php wp_compoundicalc_label('inflation_rate_text', $texts, 'Inflation Rate?') ?></label>
			<input type="number" name="<?php echo $form_prefix; ?>[inflation_rate]" value="<?php echo  is_null($inflation_rate) ? null : $inflation_rate * 100 ?>" step="0.01" />
			<span class="not_required">%</span>
		</div>
		<div class="form_field">
			<label>&nbsp;</label>
			<input type="submit" name="submit" value="<?php wp_compoundicalc_label('calculate_text', $texts, 'Calculate') ?>" />
		</div>
	</form>
	
	<div class="calculator_results<?php echo  is_null($principle) ? ' calculator_hidden' : '' ?>">
	    <ul class="calculator_tabs calculator_body_tabs">
	    	<li class="results_tab calculator_tab_active"><a href="javascript:;"><?php wp_compoundicalc_label('results_tab_text', $texts, 'Calculation Results') ?></a></li>
	    	<li class="graphs_tab"><a href="javascript:;"><?php wp_compoundicalc_label('graphs_tab_text', $texts, 'Graphs of Results') ?></a></li>
	    </ul>
		<table>
			<thead>
				<tr>
					<td class="calculator_highlight"><?php wp_compoundicalc_label('years_singular_text', $texts, 'Year', 'ucwords') ?></td>
					<td><?php wp_compoundicalc_label('years_singular_text', $texts, 'Year', 'ucwords') ?> <?php   $deposit_op === '-' ?  wp_compoundicalc_label('withdrawal_text', $texts, 'Withdrawal', 'ucwords') : wp_compoundicalc_label('deposit_text', $texts, 'Deposit', 'ucwords') ?></td>
					<td><?php wp_compoundicalc_label('years_singular_text', $texts, 'Year', 'ucwords') ?> <?php wp_compoundicalc_label('interest_text', $texts, 'Interest', 'ucwords') ?></td>
					<td><?php wp_compoundicalc_label('total_text', $texts, 'Total', 'ucwords') ?> <?php $deposit_op === '-' ? wp_compoundicalc_label('withdrawal_text', $texts, 'Withdrawal', 'ucwords') : wp_compoundicalc_label('deposit_text', $texts, 'Deposit', 'ucwords') ?></td>
					<td><?php wp_compoundicalc_label('total_text', $texts, 'Total', 'ucwords') ?> <?php wp_compoundicalc_label('interest_text', $texts, 'Interest', 'ucwords') ?></td>
					<td><?php wp_compoundicalc_label('balance_text', $texts, 'Balance', 'ucwords') ?></td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td height="5"></td>
				</tr>
				<? foreach($schedule['years'] as $year => $year_sch) { 
					$last = ($year + 1) == sizeof($schedule['years']) ? 'calculator_highlight' : '';
					$money_f = '%(#0n';
				?>
					<tr>
						<td class="calculator_highlight"><?php echo  $year + 1 ?></td>
						<td class="<?php echo $last ?>"><?php echo  money_format($money_f, $year_sch['year_deposit'])   ?></td>
						<td class="<?php echo $last ?>"><?php echo  money_format($money_f, $year_sch['year_interest'])  ?></td>
						<td class="<?php echo $last ?>"><?php echo  money_format($money_f, $year_sch['deposit'])  ?></td>
						<td class="<?php echo $last ?>"><?php echo  money_format($money_f, $year_sch['interest'])  ?></td>
						<td class="<?php echo $last ?>"><?php echo  money_format($money_f, $year_sch['balance'])  ?></td>
					</tr>
				<? } ?>
			</tbody>
		</table>
		
		<script>
			wpcompoundicalc.graph1.title = '<?php wp_compoundicalc_label('graph1_title_text', $texts, 'Graph 1 - Balance (Compounded Monthly)') ?>';
			wpcompoundicalc.graph1.balance = '<?php wp_compoundicalc_label('balance_text', $texts, 'balance', 'strtolower') ?>';
			wpcompoundicalc.graph1.years = '<?php wp_compoundicalc_label('years_plural_text', $texts, 'years', 'strtolower') ?>';
			
			wpcompoundicalc.graph1.data = [ ['<?php wp_compoundicalc_label('years_singular_text', $texts, 'Year', 'ucwords') ?>', '<?php wp_compoundicalc_label('balance_text', $texts, 'Balance', 'ucwords') ?>'],
			  <? foreach($schedule['years'] as $year => $year_schedule) { ?>
			  	['<?php echo  $year + 1 ?>', <?php echo  $year_schedule['balance'] ?> ],
			  <? } ?>
	          
	          ];
	          wpcompoundicalc.graph2.title = '<?php wp_compoundicalc_label('graph2_title_text', $texts, 'Graph 2 - Total Interest (Compounded Monthly)') ?>';
	          wpcompoundicalc.graph2.years = '<?php wp_compoundicalc_label('years_plural_text', $texts, 'years', 'strtolower') ?>';
	          wpcompoundicalc.graph2.interest = '<?php wp_compoundicalc_label('interest_text', $texts, 'interest', 'strtolower') ?>';
	        wpcompoundicalc.graph2.data = [ ['<?php wp_compoundicalc_label('years_singular_text', $texts, 'Year', 'ucwords') ?>', '<?php wp_compoundicalc_label('total_text', $texts, 'Total', 'ucwords') ?> <?php wp_compoundicalc_label('interest_text', $texts, 'Interest', 'ucwords') ?>'],
			  <? foreach($schedule['years'] as $year => $year_schedule) { ?>
			  	['<?php echo  $year + 1 ?>', <?php echo  $year_schedule['interest'] ?> ],
			  <? } ?>
	          
	          ];
		</script>
		
		<div class="calculator_graphs calculator_hidden">
			<div id="calculator_balance_chart"></div>
			<div id="calculator_interest_chart"></div>
		</div>
	</div>
</div>