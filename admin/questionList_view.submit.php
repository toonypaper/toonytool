<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$mailSender = new mailSender();
	$validator = new validator();
	
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	$method->method_param("POST","idno,email,memo,name");
	
	/*
	검사
	*/
	$validator->validt_tags("memo",1,"");
	
	/*
	DB저장
	*/
	$mysql->query("
		INSERT INTO toony_customer_qna
		(re_idno,memo,regdate)
		VALUES
		('$idno','$memo',now())
	");
	
	/*
	고객의 메일로 답변 발송
	*/
	$mailSender->template = "mailling";
	$mailSender->t_email = $email;
	$mailSender->t_name = $name;
	$mailSender->subject = $site_config['ad_site_name']."에서 문의에 대한 답변을 발송 합니다.";
	$mailSender->memo = str_replace('\"','"',stripslashes($memo));
	$mailSender->mail_send();
	
	/*
	완료 후 리턴
	*/
	$validator->validt_success("성공적으로 답변이 발송 되었습니다.","admin/?p=questionList_view&act={$idno}");
?>