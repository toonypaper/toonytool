<?php
	include "include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$mailSender = new mailSender();
	
	$method->method_param("POST","id");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	검사
	*/
	if(trim($id=="")){ echo '<!--error::null_id-->'; exit; }
	$mysql->select("
		SELECT me_idno
		FROM toony_member_list 
		WHERE me_id='$id'
	");
	if($mysql->numRows()<1){ echo '<!--error::not_member-->'; exit; }
	
	/*
	임시 비밀번호 생성 후 회원의 비밀번호를 임시 비밀번호로 DB 변경
	*/
	$upw = md5(date("YmdHis").$id);
	$mysql->query("
		UPDATE toony_member_list 
		SET me_password=password('$upw')
		WHERE me_id='$id' AND me_drop_regdate IS NULL
	");
	
	/*
	회원의 기본 정보 로드
	*/
	$mysql->select("
		SELECT me_nick
		FROM toony_member_list
		WHERE me_id='$id' AND me_drop_regdate IS NULL
	");
	$mysql->fetchArray("me_nick");
	$array = $mysql->array;
	$nick = $array['me_nick'];
	
	
	/*
	회원의 이메일로 임시 비밀번호 발송
	*/
	$mailSender->func_mail_sender();
	$mailSender->func_mail_sender->temp = "password";
	$mailSender->func_mail_sender->t_email = $id;
	$mailSender->func_mail_sender->t_name = $nick;
	$mailSender->func_mail_sender->subject = "{$nick}님의 {$site_config['ad_site_name']} 로그인 임시 비밀번호";
	$mailSender->func_mail_sender->account_password = $upw;
	$mailSender->func_mail_sender_get();
	$sendCount++;
	
	/*
	완료 후 리턴
	*/
	//로그인 후 이동할 페이지 URI를 리턴
	echo '<!--success::1-->';
	
	
?>