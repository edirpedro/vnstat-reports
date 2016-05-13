
// Draw the Line Chart
function drawChartLine(chart) {
	
	var ctx = document.getElementById(chart.id).getContext("2d");
	
	var gradient = ctx.createLinearGradient(0,200,0,0);
	gradient.addColorStop(0, 'rgba(255,255,255,0.5)');
	gradient.addColorStop(1, '#ccc');
	
	new Chart(ctx, {
		type: 'line',
		data: {
			labels: chart.labels,
			datasets: [{
				label: _g('Sent'),
				fill: true,
				lineTension: .2,
				backgroundColor: gradient,
				borderColor: "#265a88",
				pointBorderColor: "#265a88",
				pointBackgroundColor: "#fff",
				pointHoverBackgroundColor: "#265a88",
				pointHoverBorderColor: "#265a88",
				data: chart.tx,
			},{
				label: _g('Received'),
				fill: true,
				lineTension: .2,
				backgroundColor: gradient,
				borderColor: "#337ab7",
				pointBorderColor: "#337ab7",
				pointBackgroundColor: "#fff",
				pointHoverBackgroundColor: "#337ab7",
				pointHoverBorderColor: "#337ab7",
				data: chart.rx,
			}]
		},
		options: {
			responsive: true,
			legend: {
				position: 'bottom',
			},
			scales: {
				xAxes: [{
					display: 'xAxes' in chart ? chart.xAxes : false,
				}],
				yAxes: [{
					display: 'yAxes' in chart ? chart.yAxes : false,
				}]
			},
			tooltips: {
				mode: 'label',
				callbacks: {
					label: function(tooltipItem, data) {
						return data.datasets[tooltipItem.datasetIndex].label + ' ' + get_traffic(tooltipItem.yLabel);
					}
				}
			}
		}
	});
	
}

// Draw the Doughnut Chart
function drawChartDoughnut(chart) {
	var ctx = document.getElementById(chart.id).getContext("2d");
	
	new Chart(ctx, {
		type: 'doughnut',
		data: {
			labels: [_g('Sent'), _g('Received'), 'NULL'],
			datasets: [{
				data: chart.data,
				backgroundColor: ['#265a88', '#337ab7', '#eee'],
				hoverBackgroundColor: ['#265a88', '#337ab7', '#eee']
			}]
		},
		options: {
			responsive: false,
			legend: {
				display: false,
			},
			tooltips: {
				enabled: false
			},
			cutoutPercentage: 50
		}
	});
	
}

// Get the traffic formatted
function get_traffic(bytes) {
	bytes *= 1024;
	var units = ['B', 'KiB', 'MiB', 'GiB', 'TiB'];
	var pow = Math.floor((bytes ? Math.log(bytes) : 0) / Math.log(1024));
	pow = Math.min(pow, units.length - 1);
	bytes /= Math.pow(1024, pow);
	return bytes.toFixed(2) + ' ' + units[pow];
}

// Get Translation
function _g(text) {
	if('translations' in window)
		return text in window.translations ? window.translations[text] : text;
	return text;
}
