<?php
	include "include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	include __DIR_PATH__."capcha/zmSpamFree.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("POST","name,email,phone,memo");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	검사
	*/
	if(trim($name)==""){
		echo '<!--error::null_name-->'; exit;
	}
	if(trim($email)==""){
		echo '<!--error::null_email-->'; exit;
	}
	$lib->func_method_param_check("id",$email,"<!--error::not_email-->");
	if(trim($phone)==""){
		echo '<!--error::null_phone-->'; exit;
	}
	$lib->func_method_param_check("phone",$phone,"<!--error::not_phone-->");
	if(trim($memo)==""){
		echo '<!--error::null_memo-->'; exit;
	}
	if(!isset($__toony_member_idno)&&zsfCheck($capcha,"")!=true){
		echo '<!--error::spam_replace-->'; exit;
	}
	
	/*
	DB 저장
	*/
	$mysql->query("
		INSERT INTO toony_customer_qna
		(me_idno,memo,cst_name,cst_email,cst_phone,regdate)
		VALUES
		('{$member['me_idno']}','$memo','$name','$email','$phone',now())
	");
		
	/*
	완료 후 리턴
	*/
	echo '<!--success::1-->';
?>