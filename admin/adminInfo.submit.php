<?php
	include "../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("POST","id,password,password02,nick,sex,phone,telephone,point");
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	
	/*
	검사
	*/
	if(trim($id)==""){
		echo '<!--error::null_id-->'; exit;
	}
	$lib->func_method_param_check("id",$id,"<!--error::not_id-->");
	if(trim($nick)==""){
		echo '<!--error::null_nick-->'; exit;
	}
	$lib->func_method_param_check("nick",$nick,"<!--error::not_nick-->");
	$lib->func_method_param_check("phone",$phone,"<!--error::not_phone-->");
	$lib->func_method_param_check("telephone",$telephone,"<!--error::not_telephone-->");
	
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
			echo '<!--error::not_samePassword-->'; exit;
		}
		$lib->func_method_param_check("password",$password,"<!--error::not_password-->");
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
	echo '<!--success::1-->';
	
	
?>