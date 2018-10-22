<!DOCTYPE html>
<html>
<head>
	<title>My Team | {{ env('SITE_NAME') }}</title>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
</head>
<body>
<?php

	
	function render_temp($file, $data = array())
	{
		if (file_exists($file))
		{
			extract($data);
			ob_start();
			require($file);
			$out = ob_get_contents();
			ob_end_clean();
			return $out;
			/* } else {
			return false;
			}
			*/
		}
	}


?>
	<script type="text/javascript">
		google.charts.load('current', {packages:["orgchart"]});
		google.charts.setOnLoadCallback(drawChart);

		function drawChart() {
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Name');
		data.addColumn('string', 'Manager');
		data.addColumn('string', 'ToolTip');
		<?php echo $data ?>

		var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
		chart.draw(data, {allowHtml:true});
		}
	</script>
<div id="chart_div"></div>

</body>
</html>