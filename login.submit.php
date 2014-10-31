<?php
	include "include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$session = new sessionController();
	$method = new methodController();
	$validator = new validator();
	
	$method->method_param("POST","save_id,redirect,id,password");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	검사
	*/
	if($member['me_level']<10){
		$validator->validt_diserror("","이미 로그인 되어 있습니다.");
	}
	$validator->validt_email("id",1,"");
	$validator->validt_password("password",1,"");
	$mysql->select("
		SELECT *
		FROM toony_member_list
		WHERE me_id='$id' AND me_password=password('$password') AND me_drop_regdate IS NULL
	");
	if($mysql->numRows()<1){
		$validator->validt_diserror("id","아이디 혹은 비밀번호가 잘못 되었습니다.");
	}
	if($mysql->fetch("me_idCheck")=="N"){
		$validator->validt_returnAjax("이메일 인증이 필요한 아이디입니다.","account.idCheck.send.php");
	}
	
	/*
	로그인 처리
	*/
	$member['me_id'] = $mysql->fetch("me_id");
	$member['me_idno'] = $mysql->fetch("me_idno");
	$session->session_register("__toony_member_idno",$member['me_idno']);
	
	/*
	로그인 내역 기록
	*/
	$mysql->query("
		UPDATE toony_member_list
		SET me_login_ip='{$_SERVER['REMOTE_ADDR']}',me_login_regdate=now()
		WHERE me_idno='{$member['me_idno']}';
	");
	
	/*
	아이디 저장을 체크한 경우 아이디를 쿠키에 저장
	*/
	if($save_id=="checked"){
		setcookie("__toony_member_saveId",$member['me_id'],time()+2592000,"/");
	}else{
		setcookie("__toony_member_saveId","",0,"/");
	}
	
	/*
	완료 후 리턴
	*/
	//로그인 후 이동할 페이지 URI를 리턴
	$validator->validt_success("",urldecode($redirect));
?>