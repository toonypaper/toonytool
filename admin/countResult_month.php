<?php
	include_once "../include/engine.inc.php";
	include_once __DIR_PATH__."include/global.php";
	
	$tpl = new skinController();
	$header = new skinController();
	$loop = new skinController();
	$footer = new skinController();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("GET","year");
	
	/*
	시간 초기화
	*/
	$year_var = $year;
	if(!$year_var||$year_var>date("Y",time())){
		$year_var = date("Y",time());
	}
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/countResult_month.html");
	$header->skin_html_load($tpl->skin);
	$header->skin_loop_header("[{loop_start}]");
	$loop->skin_html_load($tpl->skin);
	$loop->skin_loop_array("[{loop_start}]","[{loop_end}]");
	$footer->skin_html_load($tpl->skin);
	$footer->skin_loop_footer("[{loop_end}]");
	
	/*
	템플릿 함수
	*/
	function month_select_option(){
		global $year_var,$mysql;
		$mysql->select("
			SELECT DATE_FORMAT(regdate,'%Y') month
			FROM toony_admin_counter 
			GROUP BY month
			ORDER BY month DESC;
		");
		do{
			if($year_var==$mysql->fetch("month")){ $op_selected = " selected"; }else{ $op_selected = ""; }
			$option.= "<option value=\"".$mysql->fetch("month")."\"".$op_selected.">".$mysql->fetch("month")."</option>\n";
		}while($mysql->nextRec());
		return $option;
	}

	/*
	템플릿 치환
	*/
	//header
	$header->skin_modeling("[month_select_option]",month_select_option());
	echo $header->skin_echo();
	//loop
	$mysql->select("
		SELECT
		COUNT(*) count_re,
		DATE_FORMAT(regdate,'%Y.%m') date_re
		FROM toony_admin_counter
		WHERE DATE_FORMAT(regdate,'%Y')='$year_var'
		GROUP BY date_re
		ORDER BY date_re DESC;
	");
	if($mysql->numRows()>0){
		do{
			$loop->skin_modeling("[count]",number_format($mysql->fetch("count_re")));
			$loop->skin_modeling("[date]",$mysql->fetch("date_re"));
			$loop->skin_modeling("[day_btn]","<a href=\"#\" class=\"RV_btn_img __btn_s_detail\" title=\"월별 보기\" RV_day=\"".$mysql->fetch("date_re")."\"></a>");
			echo $loop->skin_echo();
		}while($mysql->nextRec());
	}
	//footer
	echo $footer->skin_echo();
?>