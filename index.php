<?php

require 'helpers.php';

$interface = get_interface($_GET['interface']);
$traffic = $interface->traffic;
//print_r($interface);
//print_r($traffic);
	
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title><?php _e('Network Traffic') ?></title>
	
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="css/style.css">
	
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/app.js"></script>
	<script>
		window.translations = <?php echo json_encode($translations) ?>
	</script>
</head>
<body>
	
<div class="container container-fluid">
	<div class="row">
		<div class="col-lg-12">
				
			<form action="./" method="get" class="center">
				<h1><?php _e('Network Traffic') ?></h1>
				
				<?php if(count($config->interfaces) > 1) : ?>
				<div class="btn-group" role="group" aria-label="Interfaces">
					<button type="submit" name="interface" value="all" class="btn <?php is_interface('all') ?>"><?php _e('All Interfaces') ?></button>
					<?php foreach($config->interfaces as $id => $name) : ?>
					<button type="submit" name="interface" value="<?php echo $id ?>"  class="btn <?php is_interface($id) ?>"><?php echo $name ?></button>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
			</form>
						
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active hidden-xs"><a href="#summary" role="tab" data-toggle="tab"><?php _e('Summary') ?></a></li>

				<li class="hidden-xs" role="presentation"><a href="#hourly" role="tab" data-toggle="tab"><?php _e('Hourly') ?></a></li>
				<li class="hidden-xs" role="presentation"><a href="#daily" role="tab" data-toggle="tab"><?php _e('Daily') ?></a></li>
				<li class="hidden-xs" role="presentation"><a href="#monthly" role="tab" data-toggle="tab"><?php _e('Monthly') ?></a></li>
				<li class="hidden-xs" role="presentation"><a href="#tops" role="tab" data-toggle="tab"><?php _e('Top 10') ?></a></li>
				<li class="dropdown hidden-sm hidden-md hidden-lg" role="presentation">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php _e('Reports') ?> <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="#summary" role="tab" data-toggle="tab"><?php _e('Summary') ?></a></li>
						<li><a href="#hourly" role="tab" data-toggle="tab"><?php _e('Hourly') ?></a></li>
						<li><a href="#daily" role="tab" data-toggle="tab"><?php _e('Daily') ?></a></li>
						<li><a href="#monthly" role="tab" data-toggle="tab"><?php _e('Monthly') ?></a></li>
						<li><a href="#tops" role="tab" data-toggle="tab"><?php _e('Top 10') ?></a></li>
					</ul>
				</li>
			</ul>
			
			<div class="tab-content">
				
				<!-- Tab: Summary -->
				<div role="tabpanel" class="tab-pane fade in active" id="summary">	
					<?php $summary = get_summary($traffic); ?>
					
					<div class="row">
						<div class="col-sm-4">
							<h2><?php _e('Today') ?></h2>
							<canvas id="chartToday" width="200" height="200"></canvas>
							<script>
								drawChartDoughnut({
									id: 'chartToday',
									data: [<?php echo join(',', (array) $summary->today) ?>]
								});
							</script>
							<dl>
								<dt><?php _e('Received') ?>:</dt><dd><?php the_traffic($summary->today->rx) ?></dd>
								<dt><?php _e('Sent') ?>:</dt><dd><?php the_traffic($summary->today->tx) ?></dd>
								<dt><?php _e('Total') ?>:</dt><dd><?php the_traffic($summary->today->rx + $summary->today->tx) ?></dd>
							</dl>
						</div>
						<div class="col-sm-4">
							<h2><?php _e('Yesterday') ?></h2>
							<canvas id="chartYesterday" width="200" height="200"></canvas>
							<script>
								drawChartDoughnut({
									id: 'chartYesterday',
									data: [<?php echo join(',', (array) $summary->yesterday) ?>]
								});
							</script>
							<dl>
								<dt><?php _e('Received') ?>:</dt><dd><?php the_traffic($summary->yesterday->rx) ?></dd>
								<dt><?php _e('Sent') ?>:</dt><dd><?php the_traffic($summary->yesterday->tx) ?></dd>
								<dt><?php _e('Total') ?>:</dt><dd><?php the_traffic($summary->yesterday->rx + $summary->yesterday->tx) ?></dd>
							</dl>
						</div>
						<div class="col-sm-4">
							<h2><?php _e('Information') ?></h2>
							<dl>
								<dt><?php _e('Since') ?>:</dt><dd><?php the_day($interface->created->date) ?></dd>
								<dt><?php _e('Updated') ?>:</dt><dd><?php the_day($interface->updated->date) ?></dd>
							</dl>
							<dl>
								<dt><span class="color-received">&nbsp;</span></dt><dd><?php _e('Received') ?></dd>
								<dt><span class="color-sent">&nbsp;</span></dt><dd><?php _e('Sent') ?></dd>
							</dl>
						</div>
					</div>

					<hr class="hidden-sm hidden-xs">
					
					<div class="row">
						<div class="col-sm-4">
							<h2><?php _e('This Month') ?></h2>
							<canvas id="chartMonth" width="200" height="200"></canvas>
							<script>
								drawChartDoughnut({
									id: 'chartMonth',
									data: [<?php echo join(',', (array) $summary->month) ?>]
								});
							</script>
							<dl>
								<dt><?php _e('Received') ?>:</dt><dd><?php the_traffic($summary->month->rx) ?></dd>
								<dt><?php _e('Sent') ?>:</dt><dd><?php the_traffic($summary->month->tx) ?></dd>
								<dt><?php _e('Total') ?>:</dt><dd><?php the_traffic($summary->month->rx + $summary->month->tx) ?></dd>
							</dl>		
						</div>
						<div class="col-sm-4">
							<h2><?php _e('Last Month') ?></h2>
							<canvas id="chartLastMonth" width="200" height="200"></canvas>
							<script>
								drawChartDoughnut({
									id: 'chartLastMonth',
									data: [<?php echo join(',', (array) $summary->last_month) ?>]
								});
							</script>
							<dl>
								<dt><?php _e('Received') ?>:</dt><dd><?php the_traffic($summary->last_month->rx) ?></dd>
								<dt><?php _e('Sent') ?>:</dt><dd><?php the_traffic($summary->last_month->tx) ?></dd>
								<dt><?php _e('Total') ?>:</dt><dd><?php the_traffic($summary->last_month->rx + $summary->last_month->tx) ?></dd>
							</dl>
						</div>
						<div class="col-sm-4">
							<h2><?php _e('All Time') ?></h2>
							<canvas id="chartAllTime" width="200" height="200"></canvas>
							<script>
								drawChartDoughnut({
									id: 'chartAllTime',
									data: [<?php echo join(',', (array) $summary->total) ?>]
								});
							</script>
							<dl>
								<dt><?php _e('Received') ?>:</dt><dd><?php the_traffic($summary->total->rx) ?></dd>
								<dt><?php _e('Sent') ?>:</dt><dd><?php the_traffic($summary->total->tx) ?></dd>
								<dt><?php _e('Total') ?>:</dt><dd><?php the_traffic($summary->total->rx + $summary->total->tx) ?></dd>
							</dl>
						</div>
					</div>
						
				</div>
				
				<!-- Tab: Hourly -->
				<div role="tabpanel" class="tab-pane fade" id="hourly">	
					<h2><?php _e('Hourly') ?></h2>
								
					<canvas id="chartHourly" width="700" height="200"></canvas>
					<script>
						<?php
						$labels = $rx = $tx = array();
						$hours = $traffic->hours;
						foreach(array_reverse($hours) as $hour) {
							$labels[] = get_hour($hour);
							$rx[] = $hour->rx;
							$tx[] = $hour->tx;
						} 
						?>
						drawChartLine({
							xAxes: true,
							id: 'chartHourly',
							labels: ["<?php echo join('","', $labels) ?>"],
							rx: [<?php echo join(',', $rx) ?>],
							tx: [<?php echo join(',', $tx) ?>]
						});
					</script>
					
					<div class="table-responsive">
						<table class="table table-striped">
						<thead>
							<tr>
								<th class="title"><?php _e('Hour') ?></th>
								<th class="receive right"><?php _e('Received') ?></th>
								<th class="sent right"><?php _e('Sent') ?></th>
								<th class="total right"><?php _e('Total') ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($traffic->hours as $key => $hour) : ?>
							<tr>
								<td><?php the_hour($hour) ?></td>
								<td class="right"><?php the_traffic($hour->rx) ?></td>
								<td class="right"><?php the_traffic($hour->tx) ?></td>
								<td class="right"><?php the_traffic($hour->rx + $hour->tx) ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
						</table>
					</div>
				</div>
				
				<!-- Tab: Daily -->
				<div role="tabpanel" class="tab-pane fade" id="daily">
					<h2><?php _e('Daily') ?></h2>
								
					<canvas id="chartDaily" width="700" height="200"></canvas>
					<script>
						<?php
						$labels = $rx = $tx = array();
						foreach(array_reverse($traffic->days) as $day) {
							$labels[] = $day->date->day;
							$rx[] = $day->rx;
							$tx[] = $day->tx;
						}
						?>
						drawChartLine({
							xAxes: true,
							id: 'chartDaily',
							labels: ["<?php echo join('","', $labels) ?>"],
							rx: [<?php echo join(',', $rx) ?>],
							tx: [<?php echo join(',', $tx) ?>]
						});
					</script>
					
					<div class="table-responsive">
						<table class="table table-striped">
						<thead>
							<tr>
								<th class="number">#</th>
								<th class="title"><?php _e('Day') ?></th>
								<th class="receive right"><?php _e('Received') ?></th>
								<th class="sent right"><?php _e('Sent') ?></th>
								<th class="total right"><?php _e('Total') ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$total_days_rx = $total_days_tx = 0;
							foreach($traffic->days as $key => $day) : 
								$total_days_rx += $day->rx;
								$total_days_tx += $day->tx;
							?>
							<tr>
								<td><?php echo $key + 1 ?></td>
								<td><?php the_day($day->date); ?></td>
								<td class="right"><?php the_traffic($day->rx) ?></td>
								<td class="right"><?php the_traffic($day->tx) ?></td>
								<td class="right"><?php the_traffic($day->rx + $day->tx) ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="2"><?php _e('Average') ?>:</th>
								<td class="right"><?php the_traffic($traffic->averages->day->rx) ?></td>
								<td class="right"><?php the_traffic($traffic->averages->day->tx) ?></td>
								<td class="right"><?php the_traffic($traffic->averages->day->rx + $traffic->averages->day->tx) ?></td>
							</tr>
							<tr>
								<th class="right" colspan="4"><?php _e('Received') ?>:</th>
								<td class="right"><?php the_traffic($total_days_rx) ?></td>
							</tr>
							<tr>
								<th class="right" colspan="4"><?php _e('Sent') ?>:</th>
								<td class="right"><?php the_traffic($total_days_tx) ?></td>
							</tr>
							<tr>
								<th class="right" colspan="4"><?php _e('Total') ?>:</th>
								<td class="right"><?php the_traffic($total_days_rx + $total_days_tx) ?></td>
							</tr>
						</tfoot>
						</table>
					</div>
				</div>
				
				<!-- Tab: Monthly -->
				<div role="tabpanel" class="tab-pane fade" id="monthly">							
					<h2><?php _e('Monthly') ?></h2>
					
					<canvas id="chartMonthly" width="700" height="200"></canvas>
					<script>
						<?php
						$labels = $rx = $tx = array();
						foreach(array_reverse($traffic->months) as $month) {
							$labels[] = strftime('%B', mktime(0, 0, 0, $month->date->month, 1, $month->date->year));
							$rx[] = $month->rx;
							$tx[] = $month->tx;
						} 
						?>
						drawChartLine({
							xAxes: true,
							id: 'chartMonthly',
							labels: ["<?php echo join('","', $labels) ?>"],
							rx: [<?php echo join(',', $rx) ?>],
							tx: [<?php echo join(',', $tx) ?>]
						});
					</script>
					
					<div class="table-responsive">
						<table class="table table-striped">
						<thead>
							<tr>
								<th class="title"><?php _e('Month') ?></th>
								<th class="receive right"><?php _e('Received') ?></th>
								<th class="sent right"><?php _e('Sent') ?></th>
								<th class="total right"><?php _e('Total') ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($traffic->months as $month) : ?>
							<tr>
								<td><?php the_month($month->date); ?></td>
								<td class="right"><?php the_traffic($month->rx) ?></td>
								<td class="right"><?php the_traffic($month->tx) ?></td>
								<td class="right"><?php the_traffic($month->rx + $month->tx) ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
						<tfoot>
							<tr>
								<th><?php _e('Average') ?>:</th>
								<td class="right"><?php the_traffic($traffic->averages->month->rx) ?></td>
								<td class="right"><?php the_traffic($traffic->averages->month->tx) ?></td>
								<td class="right"><?php the_traffic($traffic->averages->month->rx + $traffic->averages->month->tx) ?></td>
							</tr>
						</tfoot>
						</table>
					</div>
				</div>
				
				<!-- Tab: Top 10 -->
				<div role="tabpanel" class="tab-pane fade" id="tops">
					<h2><?php _e('Top 10') ?></h2>
					<div class="table-responsive">
						<table class="table table-striped">
						<thead>
							<tr>
								<th class="number">#</th>
								<th class="title"><?php _e('Day') ?></th>
								<th class="receive right"><?php _e('Received') ?></th>
								<th class="sent right"><?php _e('Sent') ?></th>
								<th class="total right"><?php _e('Total') ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($traffic->tops as $key => $tops) : ?>
							<tr>
								<td><?php echo $key + 1 ?></td>
								<td><?php the_day($tops->date); ?></td>
								<td class="right"><?php the_traffic($tops->rx) ?></td>
								<td class="right"><?php the_traffic($tops->tx) ?></td>
								<td class="right"><?php the_traffic($tops->rx + $tops->tx) ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
						</table>
					</div>
				</div>

			</div>
			
			<p>&nbsp;</p>
			<hr>
			<p class="center"><a href="https://github.com/edirpedro/vnstat-reports" target="_blank">vnStat Reports</a></p>
			<p>&nbsp;</p>
			
		</div>
	</div>
</div>

</body>
</html>
