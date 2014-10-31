<?php
	include "include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$mailSender = new mailSender();
	$validator = new validator();
	
	$method->method_param("POST","id");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	검사
	*/
	$validator->validt_email("id",1,"");
	$mysql->select("
		SELECT me_idno
		FROM toony_member_list 
		WHERE me_id='$id'
	");
	if($mysql->numRows()<1){
		$validator->validt_diserror("id","존재하지 않는 아이디입니다.");
	}
	
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
	$mailSender->template = "password";
	$mailSender->t_email = $id;
	$mailSender->t_name = $nick;
	$mailSender->subject = "{$nick}님의 {$site_config['ad_site_name']} 로그인 임시 비밀번호";
	$mailSender->account_password = $upw;
	$mailSender->mail_send();
	
	/*
	완료 후 리턴
	*/
	//로그인 후 이동할 페이지 URI를 리턴
	$validator->validt_success("회원님의 이메일로 임시 비밀번호가\n\n성공적으로 발송 되었습니다.","window.document.location.reload");
	
	
?>