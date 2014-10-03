<?php
	include_once "include/pageJustice.inc.php";
	
	$check = new libraryClass();
	$method = new methodController();
	$success_tpl = new skinController();
	$notAccount_tpl = new skinController();
	$notFound_tpl = new skinController();
	$destroy_tpl = new skinController();
	$mysql = new mysqlConnection();
	
	$method->method_param("GET","code");
	
	/*
	템플릿 로드
	*/
	//이메일 인증이 성공적으로 수행된 경우 템플릿
	$success_tpl->skin_file_path("_tpl/account.idCheck.html");
	$success_tpl->skin_loop_array("[{idCheck_success_start}]","[{idCheck_success_end}]");
	//찾을 수 없는 인증코드인 경우 템플릿
	$notAccount_tpl->skin_file_path("_tpl/account.idCheck.html");
	$notAccount_tpl->skin_loop_array("[{idCheck_notAccount_start}]","[{idCheck_notAccount_end}]");
	//폐기된 인증코드인 경우
	$notFound_tpl->skin_file_path("_tpl/account.idCheck.html");
	$notFound_tpl->skin_loop_array("[{idCheck_notFound_start}]","[{idCheck_notFound_end}]");
	//이미 인증된 인증코드인 경우
	$destroy_tpl->skin_file_path("_tpl/account.idCheck.html");
	$destroy_tpl->skin_loop_array("[{idCheck_destroy_start}]","[{idCheck_destroy_end}]");
	
	/*
	검사
	*/
	$successVar = true;
	if(trim($code)==""){ $lib->error_alert_location("정상적으로 접근하세요.",$site_config[ad_site_url],"A"); }
	$mysql->select("
		SELECT *
		FROM toony_member_idCheck 
		WHERE ric_code='$code'
	");
	$ric_me_idno = $mysql->fetch("me_idno");
	if($mysql->numRows()<1){
		echo $notAccount_tpl->skin_echo();
		$successVar = false;
	}
	$mysql->select("
		SELECT *
		FROM toony_member_idCheck
		WHERE me_idno='$ric_me_idno'
		ORDER BY ric_regdate DESC
		LIMIT 1
	");
	if($successVar==true&&$mysql->fetch("ric_code")!=$code){
		echo $notFound_tpl->skin_echo();
		$successVar = false;
	}
	if($successVar==true&&$mysql->fetch("ric_check")=="Y"){
		echo $destroy_tpl->skin_echo();
		$successVar = false;
	}
	
	/*
	회원 DB에서 아이디 체크 완료 내역을 기록
	*/
	$mysql->select("
		SELECT me_idno
		FROM toony_member_idCheck 
		WHERE ric_code='$code'
	");
	$me_idno = $mysql->fetch("me_idno");
	$mysql->query("
		UPDATE toony_member_list
		SET me_idCheck='Y'
		WHERE me_idno='$me_idno'
	");
	
	/*
	인증코드 폐기
	*/
	$mysql->query("
		UPDATE toony_member_idCheck
		SET ric_check='Y'
		WHERE ric_code='$code'
	");
	
	/*
	아무런 이상 없이 수행된 경우 완료 화면 출력
	*/
	if($successVar==true){
		echo $success_tpl->skin_echo();
	}
?>