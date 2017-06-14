var wpcompoundicalc = {
	graph1: {},
	graph2: {},
	draw_charts: function() {
	    var options = {
	      title: wpcompoundicalc.graph1.title,
	      legend: { position: 'bottom' },
	      series: {
	        0: { targetAxisIndex: 0, color: '#000099' },
	      },
	    vAxes: {
	      0: {title: wpcompoundicalc.graph1.balance,  titleTextStyle: {color: '#008000'}, format: 'currency'},
	    },
	     hAxes: {
	      0: {title: wpcompoundicalc.graph1.years,  titleTextStyle: {color: '#008000'}},
	    },
	    titleTextStyle: {
		    color: '#000099'
		}
	    };
	
	    var chart1 = new google.visualization.LineChart(document.getElementById('calculator_balance_chart'));
	
	    chart1.draw(google.visualization.arrayToDataTable(wpcompoundicalc.graph1.data), options);
	    
	    var options2 = {
	      title: wpcompoundicalc.graph2.title,
	      legend: { position: 'bottom' },
	      series: {
	        0: { targetAxisIndex: 0, color: '#ff0000' },
	      },
	      vAxes: {
	      0: {title: wpcompoundicalc.graph2.interest,  titleTextStyle: {color: '#008000'}, format: 'currency'},
	    },
	     hAxes: {
	      0: {title: wpcompoundicalc.graph2.years,  titleTextStyle: {color: '#008000'}},
	    },
	    titleTextStyle: {
		    color: '#ff0000'
		}
	    };
	    
	    var chart2 = new google.visualization.LineChart(document.getElementById('calculator_interest_chart'));
	
	    chart2.draw(google.visualization.arrayToDataTable(wpcompoundicalc.graph2.data), options2);
}
};



(function(){
  	if(typeof jQuery !=='undefined') {
    	jQuery(document).ready(function($){
			var results_tab = $('.results_tab'),
		    graphs_tab = $('.graphs_tab'),
			    results_div = $('.calculator_results table'),
			    graphs_div = $('.calculator_graphs');
			    
			$('.calculator_tabs li').click(function(){
				$(this).parent('ul').find('li').removeClass('calculator_tab_active');
				$(this).addClass('calculator_tab_active');
			});
			
			results_tab.on('click', function(){
				results_div.show();
				graphs_div.addClass('calculator_hidden');
			});
			
			graphs_tab.on('click', function(){
				wpcompoundicalc.draw_charts();
				results_div.hide();
				graphs_div.removeClass('calculator_hidden');
			});
			
			$(window).on('resize', wpcompoundicalc.draw_charts);
		});
	} else {
		throw new Error('jQuery is required for the Compound Interest Calculator Plugin.')
		return false;
	}
	          
	if( (typeof google !=='undefined') && (typeof google.charts !=='undefined') ) {
	  google.charts.load('current', {'packages':['corechart']});
	 
	} else {
	throw new Error('Google charts is required.')
}
})();