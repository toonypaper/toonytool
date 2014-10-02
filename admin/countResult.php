<?php
	$tpl = new skinController();
	$mysql = new mysqlConnection();
	$lib = new libraryClass();
	$paging = new pagingClass();
	$method = new methodController();
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/countResult.html");

	/*
	템플릿 함수
	*/
	//Total Count
	function total_count(){
		$query = new mysqlConnection();
		$query->select("select count(*) total_count from toony_admin_counter");
		return number_format($query->fetch("total_count"));
	}

	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[total_count]",total_count());
	
	echo $tpl->skin_echo();
?>