<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$validator = new validator();
	
	$method->method_param("POST","skinType,skinName");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	홈페이지, 모바일페이지를 구분하여 DB update 구문 작성
	*/
	if($skinType=="p"){
		$update = "ad_site_layout='{$skinName}'";
	}else{
		$update = "ad_msite_layout='{$skinName}'";
	}
	
	/*
	DB수정
	*/
	$mysql->query("
		UPDATE toony_admin_siteconfig SET
		$update
	");
	
	/*
	완료후 리턴
	*/
	$validator->validt_success("성공적으로 반영 되었습니다.","");
?>