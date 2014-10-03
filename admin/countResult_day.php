<?php
	include_once "../include/engine.inc.php";
	include_once __DIR_PATH__."include/global.php";
	
	$header = new skinController();
	$loop = new skinController();
	$footer = new skinController();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("GET","month");
	
	/*
	시간 초기화
	*/
	$month_var = $month;
	
	/*
	템플릿 로드
	*/
	//header
	$header->skin_file_path("admin/_tpl/countResult_day.html");
	$header->skin_loop_header("[{loop_start}]");
	//loop
	$loop->skin_file_path("admin/_tpl/countResult_day.html");
	$loop->skin_loop_array("[{loop_start}]","[{loop_end}]");
	//footer
	$footer->skin_file_path("admin/_tpl/countResult_day.html");
	$footer->skin_loop_footer("[{loop_end}]");

	/*
	템플릿 치환
	*/
	//header
	echo $header->skin_echo();
	//loop
	$mysql->select("
		SELECT COUNT(*) count_re,
		DATE_FORMAT(regdate,'%Y.%m.%d') date_re
		FROM toony_admin_counter
		WHERE DATE_FORMAT(regdate,'%Y.%m')='$month_var'
		GROUP BY date_re
		ORDER BY date_re DESC
	");
	if($mysql->numRows()>0){
		do{
			$loop->skin_modeling("[count]",number_format($mysql->fetch("count_re")));
			$loop->skin_modeling("[date]",substr($mysql->fetch("date_re"),5));
			$loop->skin_modeling("[list_btn]","<img src=\"".__URL_PATH__."admin/images/countResult_spr_down.png\" RV_list=\"".$mysql->fetch("date_re")."\" class=\"list_btn_img\" RV_list=\"".$mysql->fetch("date_re")."\" />");
			echo $loop->skin_echo();
		}while($mysql->nextRec());
	}
	//footer
	echo $footer->skin_echo();
?>