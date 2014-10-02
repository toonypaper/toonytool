<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$mysql = new mysqlConnection();
	
	//현재 월을 구함
	$nowYear = date("Y");
	$nowMonth = date("m");
	$nowDay = date("d");
	$month = array(
		date("Y.m",mktime(0,0,0,$nowMonth-5,$nowDay,$nowYear)),
		date("Y.m",mktime(0,0,0,$nowMonth-4,$nowDay,$nowYear)),
		date("Y.m",mktime(0,0,0,$nowMonth-3,$nowDay,$nowYear)),
		date("Y.m",mktime(0,0,0,$nowMonth-2,$nowDay,$nowYear)),
		date("Y.m",mktime(0,0,0,$nowMonth-1,$nowDay,$nowYear)),
		$nowYear.".".$nowMonth
	);
?>
<!DOCTYPE HTML>
<html>
<head>
<link type="text/css" rel="stylesheet" href="library/css/common.css" />
<link type="text/css" rel="stylesheet" href="library/css/visualize.jQuery.css" />
<style type="text/css">
body{ padding:0; margin:0; }
*{ font-size:11px; font-family:Arial; font-size:10px; }
.visualize{ margin-left:30px; margin-top:12px; }
.visualize-info{ margin-top:7px; }
.visualize-labels-x li .label{ font-family:Arial; width:30px; font-weight:bold; font-size:11px; }
.visualize-labels-y *{ font-family:Arial; }
.visualize-info *{ font-size:11px; }
</style>
<script type="text/javascript" src="library/js/jquery-1.7.1.js"></script>
<script type="text/javascript" src="library/js/visualize.jQuery.js"></script>
<!--[if IE]><script type="text/javascript" src="library/js/excanvas.compiled.js"></script><![endif]-->
<script type="text/javascript">
$(function(){
	$('table').visualize({
		type:'area',
		width:'730px',
		height:'180px',
		lineWeight:'2' ,
		pieMargin: 10
	});
});
</script>
</head>
<body>
<table style="display:none;">
	<thead>
		<tr>
			<td></td>
			<?php
				$mysql->select("
					SELECT DATE_FORMAT(regdate,'%m') month,DATE_FORMAT(regdate,'%Y') year
					FROM toony_admin_counter 
					GROUP BY year,month
					ORDER BY year ASC, month ASC
					LIMIT 6
				");
				$monthCount = $mysql->numRows();
				if($mysql->numRows()>0){
					do{
						$mysql->fetchArray("year,month");
						$array = $mysql->array;
			?>
			<th><?=$array['year']?>.<?=$array['month']?></th>
			<?php
					}while($mysql->nextRec());
				}
			?>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th>누적 포인트</th>
			<?php
				for($i=6-$monthCount;$i<6;$i++){
					$mysql->select("
						SELECT SUM(point_in) sum_re,
						DATE_FORMAT(regdate,'%Y.%m') date_re
						FROM toony_member_point
						WHERE point_in>0
						GROUP BY date_re
						HAVING date_re='".$month[$i]."'
					");
					$mysql->fetchArray("sum_re");
					$array = $mysql->array;
					$pointIn[$i] = $array['sum_re'];
			?>
			<td><?=(int)$array['sum_re']?></td>
			<?php
				}
			?>
		</tr>
		<tr>
			<th>순수누적 포인트</th>
			<?php
				for($i=6-$monthCount;$i<6;$i++){
					$mysql->select("
						SELECT SUM(point_out) sum_re,
						DATE_FORMAT(regdate,'%Y.%m') date_re
						FROM toony_member_point
						WHERE point_out>0
						GROUP BY date_re
						HAVING date_re='".$month[$i]."'
					");
					$mysql->fetchArray("sum_re,outSum");
					$array = $mysql->array;	
			?>
			<td><?=(int)$pointIn[$i]-$array['sum_re']?></td>
			<?php
				}
			?>
		</tr>
	</tbody>
</table>
</body>