<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$validator = new validator();
	
	$method->method_param("POST","id,password,password02,nick,sex,phone,telephone,point");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	검사
	*/
	$validator->validt_email("id",1,"");
	$validator->validt_nick("nick",1,"");
	$validator->validt_phone("phone",0,"");
	$validator->validt_phone("telephone",0,"");
	$validator->validt_number("point",1,10,0,"");
	
	/*
	최고 운영자 기본 정보 로드
	*/
	$mysql->select("
		SELECT *
		FROM toony_member_list
		WHERE me_admin='Y' AND me_level=1
	");
	$mysql->fetchArray("me_password,me_point,me_idno");
	$array = $mysql->array;
	
	/*
	비밀번호 인풋에 값이 입력된 경우 비밀번호를 변경함
	*/
	if(trim($password)!=""){
		if($password!=$password02){
			$validator->validt_diserror("password02","비밀번호와 비밀번호 확인이 일치하지 않습니다.");
		}
		$validator->validt_password("password",1,"");
		$password_val = "password('$password')";
	}else{
		$password_val = "'$array[me_password]'";
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
		SET me_id='$id',me_password=$password_val,me_nick='$nick',me_sex='$sex',me_phone='$phone',me_telephone='$telephone',me_point='$point'
		WHERE me_admin='Y' AND me_level=1
	");
	
	/*
	완료 후 리턴
	*/
	$validator->validt_success("수정이 완료 되었습니다.","admin/?p=adminInfo");
	
	
?>