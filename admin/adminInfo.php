<?php
	$tpl = new skinController();
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	
	/*
	검사
	*/
	if($member[me_admin]!="Y"){
		$lib->error_alert_location("접근 권한이 없습니다.",$site_config[ad_site_url],"A");
	}
	
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
	$tpl->skin_file_path("admin/_tpl/adminInfo.html");
	
	/*
	템플릿 함수
	*/
	function sex_checked_value_func($obj){
		global $array;
		switch($array['me_sex']){
			case "M" :
				if($obj=="M"){
					return "checked";
				}else{
					return "";
				}
				break;
			case "F" :
				if($obj=="F"){
					return "checked";
				}else{
					return "";
				}
				break;
		}
	}

	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[id_value]",$array['me_id']);
	$tpl->skin_modeling("[nick_value]",$array['me_nick']);
	$tpl->skin_modeling("[sex_checked_value_M]",sex_checked_value_func("M"));
	$tpl->skin_modeling("[sex_checked_value_F]",sex_checked_value_func("F"));
	$tpl->skin_modeling("[phone_value]",$array['me_phone']);
	$tpl->skin_modeling("[telephone_value]",$array['me_telephone']);
	$tpl->skin_modeling("[point_value]",$array['me_point']);
	
	echo $tpl->skin_echo();
?>