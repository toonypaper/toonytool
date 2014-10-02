<?php
	include "include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("POST","type,idno,password,password02,nick,sex,phone,telephone");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/**************************************************
	수정 모드인 경우
	**************************************************/
	if($type=="modify"){
		/*
		검사
		*/
		if(trim($nick)==""){
			echo '<!--error::null_nick-->'; exit;
		}
		$lib->func_method_param_check("nick",$nick,"<!--error::not_nick-->");
		$lib->func_method_param_check("phone",$phone,"<!--error::not_phone-->");
		$lib->func_method_param_check("telephone",$telephone,"<!--error::not_telephone-->");
		
		/*
		비밀번호 인풋에 값이 입력된 경우 비밀번호를 변경함
		*/
		if(trim($password)!=""){
			if($password!=$password02){
				echo '<!--error::not_samePassword-->'; exit;
			}
			$lib->func_method_param_check("password",$password,"<!--error::not_password-->");
			$password_val = "password('$password')";
		}else{
			$password_val = "'$member[me_password]'";
		}
		
		/*
		DB수정
		*/
		$mysql->query("
			UPDATE toony_member_list
			SET me_password=$password_val,me_nick='$nick',me_sex='$sex',me_phone='$phone',me_telephone='$telephone'
			WHERE me_idno='{$member['me_idno']}' AND me_drop_regdate IS NULL
		");
		
		/*
		완료 후 리턴
		*/
		echo '<!--success::1-->';
	/**************************************************
	탈퇴 모드인 경우
	**************************************************/
	}else if($type=="leave"){
		/*
		검사
		*/
		if($member['me_admin']=="Y"){
			echo '<!--error::admin_member-->'; exit;
		}
		
		/*
		탈퇴처리
		*/
		$mysql->query("
			UPDATE toony_member_list
			SET me_drop_regdate=now()
			WHERE me_idno='{$member['me_idno']}' AND me_drop_regdate IS NULL
		");
		
		/*
		현재 접속자 정보 삭제
		*/
		$mysql->query("
			DELETE FROM toony_admin_member_online
			WHERE me_idno='{$member['me_idno']}'
		");
		
		/*
		로그인 세션 삭제
		*/
		$session->session_destroy("__toony_member_idno");
		
		/*
		완료 후 리턴
		*/
		echo "<!--success::2-->";
	}
?>