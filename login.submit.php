<?php
	include "include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$session = new sessionController();
	$method = new methodController();
	
	$method->method_param("POST","save_id,redirect,id,password,viewDir");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	검사
	*/
	if($member['me_level']<10){ echo '<!--error::logged-->'; exit; }
	if(trim($id)==""){ echo '<!--error::null_id-->'; exit; }
	if(trim($password)==""){ echo '<!--error::null_password-->'; exit; }
	$mysql->select("
		SELECT *
		FROM toony_member_list
		WHERE me_id='$id' AND me_password=password('$password') AND me_drop_regdate IS NULL
	");
	if($mysql->numRows()<1){ echo '<!--error::not_member-->'; exit; }
	
	/*
	이메일 인증이 되지 않은 아이디인 경우
	*/
	if($mysql->fetch("me_idCheck")=="N"){
		echo '<!--error::not_idCheck-->'; exit;
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
	echo __URL_PATH__.$viewDir.urldecode($redirect);
	
	
?>