<?php
	include "include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	include __DIR_PATH__."capcha/zmSpamFree.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$validator = new validator();
	
	$method->method_param("POST","name,email,phone,memo,capcha");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	검사
	*/
	$validator->validt_nick("name",1,"");
	$validator->validt_email("email",1,"");
	$validator->validt_phone("phone",1,"");
	$validator->validt_null("memo","");
	if(!isset($__toony_member_idno)&&zsfCheck($capcha,"")!=true){
		$validator->validt_diserror("capcha","NOT_CAPCHA");
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
	$validator->validt_success("성공적으로 접수 되었습니다.\n\n신속한 답변 드리도록 하겠습니다.","window.document.location.reload");
?>