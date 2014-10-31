<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$validator = new validator();
	
	$method->method_param("POST","vtype,body_bgColor,body_txtColor,body_txtSize,link_txtColor,link_hoverColor,link_activeColor,link_visitedColor,link_txtSize,input_txtColor,input_txtSize,useDefault");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	변수 처리
	*/
	if($useDefault=="checked"){
		$useDefault = "Y";
	}else{
		$useDefault = "N";
	}
	
	/*
	검사
	*/
	$validator->validt_number("body_txtSize",1,10,1,"");
	$validator->validt_number("link_txtSize",1,10,1,"");
	$validator->validt_number("input_txtSize",1,10,1,"");
	
	/*
	DB수정
	*/
	$mysql->query("
		UPDATE toony_admin_design_bodyStyle
		SET
		body_bgColor='$body_bgColor',body_txtColor='$body_txtColor',body_txtSize='$body_txtSize',link_txtColor='$link_txtColor',link_hoverColor='$link_hoverColor',
		link_activeColor='$link_activeColor',link_visitedColor='$link_visitedColor',link_txtSize='$link_txtSize',input_txtColor='$input_txtColor',input_txtSize='$input_txtSize',useDefault='$useDefault'
		WHERE vtype='$vtype'
	");
	
	/*
	완료 후 리턴
	*/
	$validator->validt_success("성공적으로 수정 되었습니다.","admin/?p=bodyStyle");
	
	
?>