<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css')?>" rel="stylesheet">
<link href="<?php echo base_url('assets/datatables/css/dataTables.bootstrap.min.css')?>" rel="stylesheet">
<link href="<?php echo base_url('assets/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')?>" rel="stylesheet">
<title>Pengeluaran dan Pemasukan Kas Panrestu</title>
<script src="<?php echo base_url('assets/jquery/jquery-2.1.4.min.js')?>"></script>
<script src="<?php echo base_url('assets/highcharts.js')?>"></script>
<script type="text/javascript">
 
$(function () {
	$('#container').highcharts({
		chart: {
			plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: false
		},
		title: {
			text: 'Rasio Pengeluaran dan Pemasukan Kas'
		},
		tooltip: {
			pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: true,
					format: '<b>{point.name}</b>: {point.percentage:.1f} %',
					style: {
						color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
					}
				}
			}
		},
		series: [{
			type: 'pie',
			name: 'Persentase',
			data: [
					<?php 
					echo "['Inflow'," . $pemasukan ."],\n";
					echo "['Outflow'," . $pengeluaran ."],\n";
					// data yang diambil dari database
			
					?>
			]
		}]
	});
});
 


</script>
</head>
<body>
	<nav class="navbar navbar-inverse">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle Nav</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="<?php echo base_url('index.php/chart')?>">PANRESTU</a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav">
                <li><a href="<?php echo base_url('index.php/inflow')?>">Inflow</a></li>
                <li><a href="<?php echo base_url('index.php/outflow')?>">Outflow</a></li>
                <li><a href="<?php echo base_url('index.php/event')?>">Event</a></li>
                <li><a href="<?php echo base_url('index.php/member')?>">Member</a></li>
              </ul>
            </div>
          </div>
    </nav>
 	

 	<!-- buat nampilin chart -->
	<div class="container py-2">

		<div class="row"></div>
		<div class="row">
			<div class="col">
				<p class="h3 mt-2">Dashboard</p>
			</div>
		</div>
		<hr>
		<div class="col-sm-6" id="container"></div>

		<div class="row"">
			<div class="col-sm-4 bg-info" style="margin : 10px">
				<p class="h5">
					Total Pemasukan
				</p>
				<p class="h2 pl-3">
					<?php echo $pemasukan ?>
				</p>
			</div>
			<div class="col-sm-4 bg-warning" style="margin : 10px">
				<p class="h5">
					Total Pengeluaran
				</p>
				<p class="h2 pl-3">
					<?php echo $pengeluaran ?>
				</p>
			</div>
			<div class="col-sm-4 bg-success" style="margin : 10px">
				<p class="h5">
					Total Anggota
				</p>
				<p class="h2 pl-3">
					<?php echo $member ?>
				</p>
			</div>
		</div>
	</div>

 
</body>
</html>