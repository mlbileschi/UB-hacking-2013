<?php
	require_once( 'include/dal.php' );
	
	$categories = array();
	function get_series_data( $rows, $c ) {
		global $categories;

		$tuples = array();
		foreach( $rows as $row ) {
			$els = array_splice( $row, 1, 3 );
			$nel = array();

			$nel['name'] = $row[0];
			$categories[] = $row[0];

			$i = 120;
			if( $c == 1 )
				++$i;
			foreach( $els as $el ) 
				$nel[chr($i++)] = floatval( $el );

			$tuples[] = $nel;
		}
		return $tuples;
	}

	function get_x_axis_title() {
		($_GET['criteria'][1] != "Choose one ...") ? $_GET['criteria'][1] : "";
	}
		
	function get_y_axis_title() {
		return $_GET['criteria'][0];
	}

	function get_graph_title($criteria) {
		$clean = array();
		foreach( $criteria as $c )
			if( $c != 'Choose one ...' )
				$clean[] = $c;
		return join( ' vs ', $clean );
	}

	function get_graph_type( $c ) {
		if( $c == 3 || $c == 2 )
			return 'bubble';
		return 'column';
	}

	$criteria_combo = <<<EOF
<select name="criteria[]" class="cmb-criteria span2"><option>Choose one ...</option><option>Easiness</option><option>Helpfulness</option><option>Interest</option><option>Quality</option><option>Clarity</option></select>
EOF;
	
	$restriction_combo = <<<EOF
<select name="restriction[]" class="cmb-restriction" class="span2"><option>Course Number</option><option>Professor Name</option><option>Department Name</option></select><select name="operator[]" class="span2"><option>Contains</option><option>Prefix</option><option>Equals</option><option>One of</option><option>Less Than</option><option>Greater Than</option></select><input name="keyword[]" class="span2" type="text" /><br/>
EOF;
	if( isset( $_GET['query'] ) ) {
		$entity = $_GET['entity'];
		$criteria = $_GET['criteria'];
		$count = 0;
		foreach( $criteria as &$c ) {
			if( strlen( $c ) > 0 && $c != "Choose one ..." )
				++$count;
			$c = pg_escape_string($c);
		}

		$restrictions = $_GET['restriction'];
		$operators = $_GET['operator'];
		$keywords = $_GET['keyword'];

		$filters = array();
		$total = count( $_GET['restriction'] );
		for( $i = 0; $i < $total; ++$i )
			$filters[] = array( 'restriction' => pg_escape_string($restrictions[$i]), 
					    'operator' => pg_escape_string($operators[$i]), 'keyword' => pg_escape_string($keywords[$i]) );

		
		$result = process_query( $entity, $criteria, $filters );
		$data = get_series_data( $result, $count );
		$graph_type = get_graph_type( $count );
	}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>University Visualizer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="css/bootstrap.css" rel="stylesheet">

    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>

    <link href="css/bootstrap-responsive.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="ico/apple-touch-icon-72-precomposed.png">
                    <link rel="apple-touch-icon-precomposed" href="ico/apple-touch-icon-57-precomposed.png">
                                   <link rel="shortcut icon" href="ico/favicon.png">
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="#">Professor Analytics</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li class="active"><a href="#">Home</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">
	<h3>Sample questions you may ask ...</h3>
	<ul>
		<li><a href="http://hack.script3r.com/query.php?entity=Course&criteria[]=Easiness&criteria[]=Choose+one+...&restriction[]=Department+Name&operator[]=Equals&keyword[]=Computer+Science&restriction[]=Course+Number&operator[]=Prefix&keyword[]=4&query=">Easiest CSE-400 level course?</a></li>
		<li><a href="http://hack.script3r.com/query.php?entity=Professor&criteria[]=Quality&criteria[]=Choose+one+...&restriction[]=Course+Number&operator[]=Equals&keyword[]=421&restriction[]=Department+Name&operator[]=Equals&keyword[]=Computer+Science&query=">I need to tkake CSE-421. Which teacher, by quality?</a></li>
		<li><a href="#">Debating whether taking CSE596, CSE531 or CSE510. Which should I pick?</a></li>
	</ul>
	<form action="query.php" method="get">
	 	<fieldset>
			<legend>Query</legend>
			<label>What are we looking for?</label>
			<select name="entity" class="span2">
				<option>Professor</option>
				<option>Course</option>
				<option>Department</option>
			</select>
			
			<div id="criteria-container">
			<label>Criteria</label>
			<?php echo $criteria_combo; ?>
			</div>
			<div id="restriction-container">
			<label>Restriction</label>
			<?php echo $restriction_combo; ?>
			</div>
			<br/>
			<input type="hidden" name="query"/>
			<button type="button" id="btn-add-criteria" class="btn btn-info">+ Restriction</button>
			<button class="btn btn-primary">Search!</button>
		</fieldset>

	<?php
		if( isset( $_GET['query'] ) ) {
	?>
	<div id="sample-chart">
	
	</div>	
	<?php } ?>
    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->

    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$(document.body).on('change', 'select[class^="cmb-criteria"]', function() {
				var len = $('.cmb-criteria').length;
				if( len >= 3 ) return;
				$('#criteria-container').append('<?= $criteria_combo ?>');
			});

			$('#btn-add-criteria').click(function(e) {
				$('#restriction-container').append('<?= $restriction_combo ?>');	
				e.stopPropagation();
			});
			
			<?php if( isset( $_GET['query'] ) ) { ?>
			$('#sample-chart').highcharts({
				chart: {
					type: '<?= $graph_type ?>',
					zoomType: 'xy'
				},

				title: {
					text: '<?= get_graph_title($criteria) ?>'
				},

				tooltip: {
					formatter: function() {
						return this.point.name;
					}
				},
				
				xAxis: {
					title: {
						text: '<?= get_x_axis_title() ?>'
					},
					labels: {
						enabled: <?= ( $count>1 || count( $categories ) < 200) ? 'true' : 'false' ?>
					}
					<?php if( count( $categories ) < 200 ) { ?>
						,categories : <?= json_encode( $categories ) ?>
					<?php } ?>
				},
				
				yAxis: {
					title: {
						enabled: true,
						text: '<?= get_y_axis_title() ?>'
					}
				},
				series:[{
					data: <?= json_encode( $data ) ?>,
					events: {
						click: function(event) {
							alert(event.point.name);
						}
					}
				}]
			});
			<?php } ?>
		});
	</script>
	<script src="hs/js/highcharts.js"></script>
	<script src="hs/js/highcharts-more.js"></script>
	<script src="hs/js/modules/exporting.js"></script>
  </body>
</html>

