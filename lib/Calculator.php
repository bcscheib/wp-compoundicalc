<?php
	

class Calculator
{
	public $schedule = array();
	
	public function calculate_schedule($period_deposit, $principle, $apr_dec, $periods_per_year = 12, $years = 1, $deposit_inflation_rate = 0){
		
		$year_schedule = array();
		
		if( $years > 0 ) {
			foreach(range(1, $years) as $year) {
				$periods = $this->calculate($period_deposit, $principle, $apr_dec, $periods_per_year, $year, $deposit_inflation_rate);
				
// 				die(print_r($periods));
				$balance = !empty($periods) ? $periods[sizeof($periods) - 1]['balance'] : 0;
				$total_interest = static::sum_key('interest', $periods, 4);
				
				$year_periods = array();
				if( !empty($periods) ) {
					$year_periods = array_filter($periods, function($period) use($year) {
						return $period['year'] == $year;
					});
				}
				
				$year_deposit = !empty($periods) ? ($periods[sizeof($periods) - 1]['deposit'] * $periods_per_year) : 0;
				
				$year_schedule[] = array(
					'balance' => $balance,
					'interest' => $total_interest, 
					'deposit' => $year_deposit < 0 ? static::sum_key('deposit', $periods, 4) : $balance - $total_interest,
					'year_deposit' => $year_deposit,
					'year_interest' => !empty($year_periods) ? static::sum_key('interest', $year_periods, 4) : 0
				);
			}
		}

		$schedule = array(
			'years' => $year_schedule
		);
		return $schedule;
	}
	
	
	public function calculate($period_deposit = 0, $principle = 0, $apr_dec = 0, $periods_per_year = 12, $years = 1, $deposit_inflation_rate = 0)
    {
	    
	    $periods = array();
	    
	    if( intval($periods_per_year) > 0 && intval($years) > 0) {
		    $balance = $principle;
		    $deposit = $period_deposit;
		    
		    $periods_per_year = intval($periods_per_year);
		    foreach(range(1, $years) as $year) {
			    
			    $deposit = $year == 1 ? $deposit : round($deposit + ($deposit * $deposit_inflation_rate), 2);
			    
			   
			    
			    foreach(range(1, $periods_per_year) as $period) {
				    $interest = ($balance + $deposit)  * ($apr_dec / $periods_per_year);
				   
				    $period_balance = $balance + $interest + $deposit;
				    
				    $periods[] = array(
					    'balance' => round($period_balance, 4),
						'interest' => $interest,
						'deposit' => $deposit,
						'year' => $year
				    );
				    $balance = $period_balance;
				}
				
		    }
	    }
	    
        return $periods;
    }
    
    public static function sum_key($key, $array, $precision = null) {
	    $sum = array_sum(array_map(function($var) use($key){ return $var[$key]; }, $array));
	    if( !is_null($precision) ) {
		    $sum = round($sum, $precision);
	    }
	    
	    return $sum;
    }
}
