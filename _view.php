<div class="wp-compoundicalc-body">
	<ul class="calculator_tabs">
		<li class="calculator_tab_active"><a href="javascript:;"><?php CompoundiCalc_Core::labelize('tab_text') ?></a></li>
	</ul>
	<form method="POST" class="calculator_form" action="<?php echo admin_url('admin-post.php') ?>">
		
		<input type="hidden" name="action" value="<?php echo CompoundiCalc_Core::$action ?>" />
		<input type="hidden" name="origin" value="<?php the_ID(); ?>">
		
		<div class="form_field">
			<label><?php CompoundiCalc_Core::labelize('principle_text') ?></label>
			<input type="number" name="<?php echo CompoundiCalc_Core::$session_prefix; ?>[principle]" value="<?php echo  $principle ?>" />
		</div>
		<div class="form_field">
			<label><?php CompoundiCalc_Core::labelize('apr_text') ?></label>
			<input type="number" name="<?php echo CompoundiCalc_Core::$session_prefix; ?>[apr]" value="<?php echo $apr * 100 ?>" step="0.01" />
			<span>%</span>
		</div>
		<div class="form_field">
			<label><?php CompoundiCalc_Core::labelize('period_text') ?></label>
			<input type="number" name="<?php echo CompoundiCalc_Core::$session_prefix; ?>[years]" value="<?php echo  $years ?>" />
			<span><?php CompoundiCalc_Core::labelize('years_plural_text', 'years', 'strtolower') ?></span>
		</div>
		<div class="form_field">
			<label class="not_required"><?php CompoundiCalc_Core::labelize('deposit_amount_text') ?></label>
			<input type="number" name="<?php echo CompoundiCalc_Core::$session_prefix; ?>[deposit]" value="<?php echo $deposit_amt ?>" step="1" />
			<select name="<?php echo CompoundiCalc_Core::$session_prefix; ?>[deposit_op]">
				<option value="-"<?php echo  $deposit_op === '-' ? ' selected' : '' ?>><?php CompoundiCalc_Core::labelize('withdrawal_text', 'ucwords') ?></option>
				<option value=""<?php echo  $deposit_op !== '-' ? 'selected' : '' ?>><?php CompoundiCalc_Core::labelize('deposit_text', 'ucwords') ?></option>
			</select>
		</div>
		<div class="form_field">
			<label class="not_required"><?php CompoundiCalc_Core::labelize('inflation_rate_text') ?></label>
			<input type="number" name="<?php echo CompoundiCalc_Core::$session_prefix; ?>[inflation_rate]" value="<?php echo $inflation_rate * 100 ?>" step="1" />
			<span class="not_required">%</span>
		</div>
		<div class="form_field">
			<label>&nbsp;</label>
			<input type="submit" name="submit" value="<?php CompoundiCalc_Core::labelize('calculate_text') ?>" />
		</div>
	</form>
	
	<div class="calculator_results<?php echo  is_null($principle) ? ' calculator_hidden' : '' ?>">
	    <ul class="calculator_tabs calculator_body_tabs">
	    	<li class="results_tab calculator_tab_active"><a href="javascript:;"><?php CompoundiCalc_Core::labelize('results_tab_text') ?></a></li>
	    	<li class="graphs_tab"><a href="javascript:;"><?php CompoundiCalc_Core::labelize('graphs_tab_text') ?></a></li>
	    </ul>
		<table>
			<thead>
				<tr>
					<td class="calculator_highlight"><?php CompoundiCalc_Core::labelize('years_singular_text', 'ucwords') ?></td>
					<td><?php CompoundiCalc_Core::labelize('years_singular_text', 'ucwords') ?> <?php   $deposit_op === '-' ?  CompoundiCalc_Core::labelize('withdrawal_text', 'ucwords') : CompoundiCalc_Core::labelize('deposit_text', 'ucwords') ?></td>
					<td><?php CompoundiCalc_Core::labelize('years_singular_text', 'ucwords') ?> <?php CompoundiCalc_Core::labelize('interest_text', 'Interest', 'ucwords') ?></td>
					<td><?php CompoundiCalc_Core::labelize('total_text', 'ucwords') ?> <?php $deposit_op === '-' ? CompoundiCalc_Core::labelize('withdrawal_text', 'Withdrawal', 'ucwords') : CompoundiCalc_Core::labelize('deposit_text', 'ucwords') ?></td>
					<td><?php CompoundiCalc_Core::labelize('total_text', 'ucwords') ?> <?php CompoundiCalc_Core::labelize('interest_text', 'ucwords') ?></td>
					<td><?php CompoundiCalc_Core::labelize('balance_text', 'ucwords') ?></td>
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
			wpcompoundicalc.graph1.title = '<?php CompoundiCalc_Core::labelize('graph1_title_text') ?>';
			wpcompoundicalc.graph1.balance = '<?php CompoundiCalc_Core::labelize('balance_text', 'strtolower') ?>';
			wpcompoundicalc.graph1.years = '<?php CompoundiCalc_Core::labelize('years_plural_text', 'strtolower') ?>';
			
			wpcompoundicalc.graph1.data = [ ['<?php CompoundiCalc_Core::labelize('years_singular_text', 'ucwords') ?>', '<?php CompoundiCalc_Core::labelize('balance_text', 'Balance', 'ucwords') ?>'],
			  <? foreach($schedule['years'] as $year => $year_schedule) { ?>
			  	['<?php echo  $year + 1 ?>', <?php echo  $year_schedule['balance'] ?> ],
			  <? } ?>
	          
	          ];
	          wpcompoundicalc.graph2.title = '<?php CompoundiCalc_Core::labelize('graph2_title_text') ?>';
	          wpcompoundicalc.graph2.years = '<?php CompoundiCalc_Core::labelize('years_plural_text', 'strtolower') ?>';
	          wpcompoundicalc.graph2.interest = '<?php CompoundiCalc_Core::labelize('interest_text', 'strtolower') ?>';
	        wpcompoundicalc.graph2.data = [ ['<?php CompoundiCalc_Core::labelize('years_singular_text', 'ucwords') ?>', '<?php CompoundiCalc_Core::labelize('total_text', 'ucwords') ?> <?php CompoundiCalc_Core::labelize('interest_text', 'ucwords') ?>'],
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