<?php
	$tpl = new skinController();
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("GET","act");
	
	/*
	최고 운영자 기본 정보 로드
	*/
	$mysql->select("
		SELECT *
		FROM toony_member_list
		WHERE me_admin='Y' AND me_level=1
	");
	$mysql->fetchArray("me_id,me_nick,me_sex,me_phone,me_telephone,me_password,me_point");
	$array = $mysql->array;
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/mailling.html");
	
	/*
	템플릿 함수
	*/
	function memeber_level_option_value(){
		global $member_type_var;
		for($i=1;$i<=9;$i++){
			$option .= "<option value=\"".$i."\" ".$selected_var.">".$i." (".$member_type_var[$i].")</option>\n";
		}
		return $option;
	}

	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[receiver_id]",$act);
	$tpl->skin_modeling("[memeber_level_option_value]",memeber_level_option_value());
	
	echo $tpl->skin_echo();
?>