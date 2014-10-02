<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$mailSender = new mailSender();
	
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	$method->method_param("POST","idno,email,memo,name");
	
	/*
	검사
	*/
	if(trim($memo)==""){
		echo '<!--error::null_memo-->'; exit;
	}
	
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
	$mailSender->func_mail_sender();
	$mailSender->func_mail_sender->temp = "mailling";
	$mailSender->func_mail_sender->t_email = $email;
	$mailSender->func_mail_sender->t_name = $name;
	$mailSender->func_mail_sender->subject = $site_config['ad_site_name']."에서 문의에 대한 답변을 발송 합니다.";
	$mailSender->func_mail_sender->memo = str_replace('\"','"',stripslashes($memo));
	$mailSender->func_mail_sender_get();
	
	/*
	완료 후 리턴
	*/
	echo '<!--success::1-->';
?>