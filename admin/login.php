<?php
	include_once "../include/pageJustice.inc.php";
	
	$method = new methodController();
	$lib = new libraryClass();
	$tpl = new skinController();
	$mysql = new mysqlConnection();
	
	$method->method_param("GET","redirect");
	
	/*
	검사
	*/
	if($member['me_level']<10){
		$lib->error_alert_location("이미 로그인 되어 있습니다.",$site_config['ad_site_url'],"A");
	}
	
	/*
	최고 운영자 이메일 로드
	*/
	$mysql->select("
		SELECT me_id
		FROM toony_member_list
		WHERE me_admin='Y' AND me_drop_regdate IS NULL
		LIMIT 1
	");
	$admin_email = $mysql->fetch("me_id");
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/login.html");

	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[admin_email]",$admin_email);
	$tpl->skin_modeling("[redirectUri]",urlencode($redirect));
	
	echo $tpl->skin_echo();
?>