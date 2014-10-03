<?php
	include_once "include/pageJustice.inc.php";
	
	$method = new methodController();
	$lib = new libraryClass();
	$tpl = new skinController();
	
	$method->method_param("GET","redirect");
	
	/*
	검사
	*/
	if($member['me_level']<10){
		$lib->error_alert_location("이미 로그인 되어 있습니다.",__URL_PATH__.$viewDir,"A");
	}
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("_tpl/{$viewDir}login.html");
	
	/*
	템플릿 함수
	*/
	function id_value_func(){
		if($_COOKIE['__toony_member_saveId']!=""){
			return $_COOKIE['__toony_member_saveId'];
		}else{
			return "이메일 아이디";
		}
	}
	function password_value_func(){
		if($_COOKIE['__toony_member_saveId']!=""){
			return "";
		}else{
			return "비밀번호";
		}
	}
	function save_id_checked_func(){
		if($_COOKIE['__toony_member_saveId']!=""){
			return "checked";
		}
	}
	
	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[viewDir_value]",$viewDir);
	$tpl->skin_modeling("[id_value]",id_value_func());
	$tpl->skin_modeling("[password_value]",password_value_func());
	$tpl->skin_modeling("[save_id_checked]",save_id_checked_func());
	$tpl->skin_modeling("[redirectUri]",urlencode($redirect));
	
	echo $tpl->skin_echo();
?>