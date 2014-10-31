<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$validator = new validator();
	
	$method->method_param("POST","type,idno,password,password02,nick,sex,phone,telephone,point,level,idCheck");
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
		$validator->validt_number("point",0,10,1,"");
		
		/*
		회원의 기본 정보 로드
		*/
		$mysql->select("
			SELECT *
			FROM toony_member_list
			WHERE me_admin!='Y' AND me_idno='$idno' AND me_drop_regdate IS NULL
		");
		$mysql->fetchArray("me_idno,me_password,me_point");
		$array = $mysql->array;
		
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
			$password_val = "'{$array['me_password']}'";
		}
		
		/*
		포인트 변경시 변경 이력 DB에 기록 남김
		*/
		if($point!=$array['me_point']){
			$point_var_void = $point-$array['me_point'];
			if($point_var_void>0){
				$lib->func_member_point_add($array['me_idno'],"in",$point_var_void,"운영자 포인트 조정");
			}else if($point_var_void<0){
				$lib->func_member_point_add($array['me_idno'],"out",$point_var_void/-1,"운영자 포인트 조정");
			}
		}
		
		/*
		DB수정
		*/
		$mysql->query("
			UPDATE toony_member_list
			SET me_password=$password_val,me_nick='$nick',me_sex='$sex',me_phone='$phone',me_telephone='$telephone',me_point='$point',me_level='$level',me_idCheck='$idCheck'
			WHERE me_admin!='Y' AND me_idno='$idno' AND me_drop_regdate IS NULL
		");
		
		/*
		완료 후 리턴
		*/
		$validator->validt_success("성공적으로 수정 되었습니다.","admin/?p=memberList_modify&act=$idno");
	/**************************************************
	탈퇴 모드인 경우
	**************************************************/
	}else if($type=="leave"){
		/*
		회원의 기본 정보 로드
		*/
		$mysql->select("
			SELECT *
			FROM toony_member_list
			WHERE me_admin!='Y' AND me_idno='$idno' AND me_drop_regdate IS NULL
		");
		$mysql->fetchArray("me_password,me_point");
		$array = $mysql->array;
		
		/*
		검사
		*/
		if($mysql->numRows()<1){
			$validator->validt_diserror("","회원이 존재하지 않습니다.");
		}
		
		/*
		탈퇴처리
		*/
		$mysql->query("
			UPDATE toony_member_list
			SET me_drop_regdate=now()
			WHERE me_idno='$idno' AND me_drop_regdate IS NULL
		");
		
		/*
		완료 후 리턴
		*/
		$validator->validt_success("성공적으로 탈퇴 되었습니다.","admin/?p=memberList");
	}
?>