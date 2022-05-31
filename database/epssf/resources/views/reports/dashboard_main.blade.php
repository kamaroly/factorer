<script src="{{Url()}}/assets/dist/js/Chart.js" type="text/javascript"></script>
<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3" style="border: 1px solid #f1f1f1;">
<h4>{{ trans('report.contribution_per_month') }}</h4>
        <canvas id="chart-area1" />
</div> 
<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3" style="border: 1px solid #f1f1f1;">
<h4>{{ trans('report.member_per_institution') }}</h4>
	<canvas id="chart-area2" />
</div> 


<script>
		var barChartData = {
			labels : [@foreach ($contributions as $contribution)'{!! $contribution->month !!}',@endforeach],
			datasets : [
				{
					label: "Contribution per month",
					fillColor : "rgba(151,187,205,0.2)",
					strokeColor : "rgba(151,187,205,1)",
					pointColor : "rgba(151,187,205,1)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(151,187,205,1)",
					data :  [@foreach ($contributions as $contribution){!!$contribution->amount!!},@endforeach]
				}
			]

		}
		var lineChartData = {
			labels : [@foreach ($loans as $loan){!!$loan->month!!},@endforeach],
			datasets : [
				{
					label: "Contribution per month",
					fillColor : "rgba(151,187,205,0.2)",
					strokeColor : "rgba(151,187,205,1)",
					pointColor : "rgba(151,187,205,1)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(151,187,205,1)",
					data :  [@foreach ($loans as $loan){!!$loan->amount!!},@endforeach]
				}
			]

		}

	  var countColor = 0;
      var pieData = [ @foreach ($institutions as $institution)
                       {
				        value: {!!$institution->countStudents !!},
				        color:    "#"+((1<<24)*Math.random()|0).toString(16),
				        highlight: "#FF5A5E",
				        label: "{!!$institution->name !!}"
					    }
					  ,@endforeach
					 ];

	var radarChartData = {
		labels: [@foreach ($institutionsLoan as $institution) "{!!$institution->name!!}",@endforeach],
		datasets: [
			{
				label: "My Second dataset",
				fillColor: "rgba(151,187,205,0.2)",
				strokeColor: "rgba(151,187,205,1)",
				pointColor: "rgba(151,187,205,1)",
				pointStrokeColor: "#fff",
				pointHighlightFill: "#fff",
				pointHighlightStroke: "rgba(151,187,205,1)",
				data: [@foreach ($institutionsLoan as $institution) {!!$institution->loansCount!!} ,@endforeach]
			}
		]
	};

    window.onload = function() {
        var ctx1 = document.getElementById("chart-area1").getContext("2d");
        window.myPie = new Chart(ctx1).Bar(barChartData);

        var ctx2 = document.getElementById("chart-area2").getContext("2d");
        window.myPie = new Chart(ctx2).Pie(pieData);       
        
       
    };
    
	</script>