<?php
	$tpl = new skinController();
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("GET","act");
	
	/*
	회원의 기본 정보 로드
	*/
	$mysql->select("
		SELECT *
		FROM toony_member_list
		WHERE me_admin!='Y' AND me_idno='$act' AND me_drop_regdate IS NULL
	");
	$mysql->fetchArray("me_id,me_nick,me_sex,me_phone,me_telephone,me_password,me_point,me_level,me_login_regdate,me_login_ip,me_regdate,me_idCheck");
	$array = $mysql->array;
	
	/*
	검사
	*/
	if($member['me_level']>1){
		$lib->error_alert_location("접근 권한이 없습니다.",$site_config['ad_site_url'],"A");
	}
	if($mysql->numRows()<1){
		$lib->error_alert_location("회원이 존재하지 않거나 수정할 수 없는 회원입니다.",$site_config['ad_site_url'],"A");
	}
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/memberList_modify.html");
	
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
	function idCheck_checked_value_func($obj){
		global $array;
		switch($array['me_idCheck']){
			case "Y" :
				if($obj=="Y"){
					return "checked";
				}else{
					return "";
				}
				break;
			case "N" :
				if($obj=="N"){
					return "checked";
				}else{
					return "";
				}
				break;
		}
	}
	function level_selectbox_options(){
		global $array,$member_type_var;
		$option = "";
		for($i=1;$i<=9;$i++){
			$selected_var = "";
			if($array['me_level']==$i){
				$selected_var = "selected";
			}
			$option .= "<option value=\"".$i."\" ".$selected_var.">".$i." (".$member_type_var[$i].")</option>\n";
		}
		return $option;
	}
	function idCheck_func(){
		global $array;
		if($array['me_idCheck']=="Y"){
			return "완료";
		}else{
			return "미완료";
		}
	}

	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[idno_value]",$act);
	$tpl->skin_modeling("[id]",$array['me_id']);
	$tpl->skin_modeling("[nick_value]",$array['me_nick']);
	$tpl->skin_modeling("[sex_checked_value_M]",sex_checked_value_func("M"));
	$tpl->skin_modeling("[sex_checked_value_F]",sex_checked_value_func("F"));
	
	$tpl->skin_modeling("[idCheck]",idCheck_func());
	$tpl->skin_modeling("[idCheck_checked_value_Y]",idCheck_checked_value_func("Y"));
	$tpl->skin_modeling("[idCheck_checked_value_N]",idCheck_checked_value_func("N"));
	
	$tpl->skin_modeling("[phone_value]",$array['me_phone']);
	$tpl->skin_modeling("[telephone_value]",$array['me_telephone']);
	$tpl->skin_modeling("[point_value]",$array['me_point']);
	$tpl->skin_modeling("[level_selectbox_options]",level_selectbox_options());
	$tpl->skin_modeling("[regdate]",$array['me_regdate']);
	$tpl->skin_modeling("[login_regdate]",$array['me_login_regdate']);
	$tpl->skin_modeling("[login_ip]",$array['me_login_ip']);
	
	echo $tpl->skin_echo();
?>