<?php
	include_once "include/pageJustice.inc.php";
	
	$tpl = new skinController();
	$method = new methodController();
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	
	/*
	회원의 기본 정보 로드
	*/
	$mysql->select("
		SELECT *
		FROM toony_member_list
		WHERE me_idno='{$member['me_idno']}' AND me_drop_regdate IS NULL
	");
	$mysql->fetchArray("me_id,me_nick,me_sex,me_phone,me_telephone,me_password,me_point,me_level,me_login_regdate,me_login_ip,me_regdate,me_idCheck");
	$array = $mysql->array;
	
	/*
	검사
	*/
	$lib->func_page_level(__URL_PATH__."{$viewDir}?article=login&redirect=".urlencode("?article=").$article,9);
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("_tpl/{$viewDir}myInformation.html");
	
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
	$tpl->skin_modeling("[id]",$array['me_id']);
	$tpl->skin_modeling("[nick_value]",$array['me_nick']);
	$tpl->skin_modeling("[sex_checked_value_M]",sex_checked_value_func("M"));
	$tpl->skin_modeling("[sex_checked_value_F]",sex_checked_value_func("F"));
	$tpl->skin_modeling("[phone_value]",$array['me_phone']);
	$tpl->skin_modeling("[telephone_value]",$array['me_telephone']);
	$tpl->skin_modeling("[point]",number_format($array['me_point']));
	$tpl->skin_modeling("[level]",$array['me_level']);
	$tpl->skin_modeling("[member_type]",$member_type_var[$array['me_level']]);
	$tpl->skin_modeling("[regdate]",$array['me_regdate']);
	$tpl->skin_modeling("[login_regdate]",$array['me_login_regdate']);
	$tpl->skin_modeling("[login_ip]",$array['me_login_ip']);
	
	echo $tpl->skin_echo();
?>