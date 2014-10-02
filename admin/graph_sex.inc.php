<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$mysql = new mysqlConnection();
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
.visualize-labels-x li .label{ width:30px; font-weight:bold; font-size:11px; }
.visualize-label-pos span{ font-family:Arial; }
.visualize-info *{ font-size:11px !important; }
</style>
<script type="text/javascript" src="library/js/jquery-1.7.1.js"></script>
<script type="text/javascript" src="library/js/visualize.jQuery.js"></script>
<!--[if IE]><script type="text/javascript" src="library/js/excanvas.compiled.js"></script><![endif]-->
<script type="text/javascript">
$(function(){
	$('table').visualize({
		type:'pie',
		width:'343px',
		height:'280px',
		lineWeight:'2' ,
		pieMargin: 10,
		title: '성별 회원 비율'
	});
});
</script>
</head>
<body>
<table style="display:none;">
	<thead>
		<tr>
			<th>여자</th>
			<th>남자</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th>여자</th>
			<?php
				$mysql->select("
					SELECT *
					FROM toony_member_list
					WHERE me_sex='F'
				");
			?>
			<td><?=(int)$mysql->numRows();?></td>
		</tr>
		<tr>
			<th>남자</th>
			<?php
				$mysql->select("
					SELECT *
					FROM toony_member_list
					WHERE me_sex='M'
				");
			?>
			<td><?=(int)$mysql->numRows();?></td>
		</tr>
	</tbody>
</table>
</body>