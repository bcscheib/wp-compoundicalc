<?php
define('ABSPATH', '');
require __DIR__ . '/../lib/Calculator.php';
 
class CalculatorTests extends PHPUnit_Framework_TestCase
{
    private $calculator;
 
    protected function setUp()
    {
        $this->calculator = new Calculator();
    }
 
    protected function tearDown()
    {
        $this->calculator = NULL;
    }
    /**
    * @dataProvider providerTestCalculator
    */
    
    public function testCalculator( $expected_deposit, $expected_interest, $expected_balance, $period_deposit, $principle, $apr_dec, $periods_per_year = 12, $years = 1, $deposit_inflation_rate = 0.0)
    {
        $periods = $this->calculator->calculate($period_deposit, $principle, $apr_dec, $periods_per_year, $years, $deposit_inflation_rate);
        
//         die(print_r($periods));
        $last_period = !empty($periods) ? $periods[sizeof($periods) - 1] : array('balance' => 0, 'interest' => 0, 'deposit' => 0);
        
        $this->assertEquals($expected_balance, $last_period['balance'], 'Balance of ' . $last_period['balance'] . ' did not match expected balance of ' . $expected_balance . ' after ' . $years . ' years.');
        
        $this->assertEquals($years * $periods_per_year, sizeof($periods), 'Period size of ' . sizeof($periods) . ' did not match expected size of ' . ($years * $periods_per_year)  . ' after ' . $years . ' years.');
        
        $this->assertEquals($expected_deposit, $last_period['deposit'], 'Last Deposit of ' . $last_period['deposit'] . ' did not match expected deposit of ' . $expected_deposit . ' after ' . $years . ' years.');
        
        $interest = Calculator::sum_key('interest', $periods, 4);
        $this->assertEquals($expected_interest, $interest, 'Interest of ' . $interest . ' did not match expected interest of ' . $expected_interest . ' after ' . $years . ' years.');
    }
 
    #A = P (1 + r/n)^(nt)

	# A = the future value of the investment/loan, including interest
	# P = the principal investment amount (the initial deposit or loan amount)
	# r = the annual interest rate (decimal)
	# n = the number of times that interest is compounded per year
	# t = the number of years the money is invested or borrowed for
	
    public function providerTestCalculator() {
	    return array(
		  /* standard one year tests */
		  array(0, 1233.5562, 21233.5562, 0, 20000,0.06),
		  array(200, 1313.0043, 23713.0043, 200, 20000,0.06),
		  array(-200, 1154.1082, 18754.1082, -200, 20000,0.06),
		  
		  /* two year tests */
		  array(-200, 2231.3725, 17431.3725, -200, 20000, 0.06, 12, 2),
		  array(-206, 2228.9891, 17356.9891, -200, 20000, 0.06, 12, 2, 0.03),
		  
		  
		   /* 3 year tests */
		  array(-212.18, 3215.2438, 15797.0838, -200, 20000, 0.06, 12, 3, 0.03),
		  
		   /* 4 year tests */
		  array(-218.55, 4102.7565, 14061.9965, -200, 20000, 0.06, 12, 4, 0.03),
		  
		  
		   /* 5 year tests */
		  array(-225.11, 4880.6469, 12138.5669, -200, 20000, 0.06, 12, 5, 0.03),
		  
		  
		  array(0, 0, 0, 0, 0, 0.06),
		  array(0, 0, 0, 0, 0, 0),
		  array(0, 0, 0, 0, 0, 0.00),
		  array(0, 0, 0, 0, 'a', 0.00),
		  array(0, 0, 0, 0, 0, 0.06, 0),
		  array(0, 0, 0, 0, 0, 0.06, 12, 0),
	    );
    }
    
    /**
    * @dataProvider providerTestCalculateSchedule
    * @group test
    */
    public function testCalculateSchedule($years, $period_deposit, $deposits, $balances, $interests, $year_interests, $deposit_inflation_rate = 0.0, $year_deposits = array())
    {
	    $principle = 20000;
	    $apr_dec = 0.06;
	    $periods_per_year = 12;
	    
        $result = $this->calculator->calculate_schedule($period_deposit, $principle, $apr_dec, $periods_per_year, $years, $deposit_inflation_rate);
        
        
        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('years', $result);
        
        $this->assertEquals($years, sizeof($result['years']), 'Calculator schedule was expected to have ' . $years . ' years of info, but had ' . sizeof($result['years']));
        
        for($i = 0; $i < $years; $i++) {
	         $this->assertArrayHasKey('balance', $result['years'][$i]);
	         $this->assertArrayHasKey('interest', $result['years'][$i]);
	         $this->assertArrayHasKey('deposit', $result['years'][$i]);
	         $this->assertArrayHasKey('year_deposit', $result['years'][$i]);
	         $this->assertArrayHasKey('year_interest', $result['years'][$i]);
	         
	         $this->assertEquals($balances[$i], $result['years'][$i]['balance'], 'Balance of ' . $result['years'][$i]['balance'] . ' at year ' . ($i + 1). ' did not match expected balance of ' . $balances[$i] . '.' );
	         
	         
	         $this->assertEquals($interests[$i], $result['years'][$i]['interest'], 'Interest of ' . $result['years'][$i]['interest'] . ' at year ' . ($i + 1). ' did not match expected interest of ' . $interests[$i] . '.' );
	         
	         $this->assertEquals($year_interests[$i], $result['years'][$i]['year_interest'], 'Year Interest of ' . $result['years'][$i]['year_interest'] . ' at year ' . ($i + 1). ' did not match expected year interest of ' . $year_interests[$i] . '.' );
	         
	         if( $deposit_inflation_rate != 0 ) {
		         
		         $this->assertEquals($deposits[$i], $result['years'][$i]['deposit'], 'Total Deposit of ' . $result['years'][$i]['deposit'] . ' at year ' . ($i + 1). ' did not match expected total deposit of ' . $deposits[$i] . '.' );
		         
		         $this->assertEquals($year_deposits[$i], $result['years'][$i]['year_deposit'], 'Year Deposit of ' . $result['years'][$i]['year_deposit'] . ' at year ' . ($i + 1). ' did not match expected year deposit of ' . $year_deposits[$i] . '.' );
	         }
        }

    }
	
    public function providerTestCalculateSchedule() {
	    return array(
		  array(1, 0, array(0), array(21233.5562), array(1233.5562), array(1233.5562)),


		  array(1, 200, array(200), array(23713.0043), array(1313.0043), array(1313.0043) ),
		  
		  array(2, 200, array(22400.00, 24872), array(23713.0043,27729.4020), array(1313.0043, 2857.4020), array(1313.0043, 1544.3977), 0.03, array(2400, 2472)  ),
		  
		  
		  array(1, -200, array(-200), array(18754.1082), array(1154.1082), array(1154.1082) ),
		 
		  array(2, -200, array(-200), array(18754.1082,17431.3725), array(1154.1082, 2231.3725), array(1154.1082, 1077.2643) ),
		   
		  array(0, -200, array(-200), array(18754.1082,17431.3725), array(1154.1082, 2231.3725), array(1154.1082, 2231.3725) ),
		 
		  array(2, -200, array(-2400, -4872), array(18754.1082,17356.9891), array(1154.1082, 2228.9891), array(1154.1082, 1074.8809), 0.03, array(-2400, -2472) ),

	    );
    }
}
