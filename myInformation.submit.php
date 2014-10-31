<?php
	include "include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$validator = new validator();
	
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
		$validator->validt_nick("nick",1,"");
		$validator->validt_phone("phone",0,"");
		$validator->validt_phone("telephone",0,"");
		
		/*
		비밀번호 인풋에 값이 입력된 경우 비밀번호를 변경함
		*/
		if($password!=$password02){
			$validator->validt_diserror("password02","비밀번호와 비밀번호 확인이 일치하지 않습니다.");
		}
		if(trim($password)!=""){
			
			$validator->validt_password("password",1,"");
			$password_val = "password('$password')";
		}else{
			$password_val = "'".$member['me_password']."'";
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
		$validator->validt_success("성공적으로 수정 되었습니다.","window.document.location.reload");
	/**************************************************
	탈퇴 모드인 경우
	**************************************************/
	}else if($type=="leave"){
		/*
		검사
		*/
		if($member['me_admin']=="Y"){
			$validator->validt_diserror("","최고 회원은 탈퇴할 수 없습니다.");
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
		$validator->validt_success("성공적으로 탈퇴 되었습니다.\n\n그동안 이용해 주셔서 감사합니다.","?article=main");
	}
?>