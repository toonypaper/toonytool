<?php
	include_once __DIR_PATH__."modules/board/install/installCheck.php";
	
	$tpl = new skinController();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("GET","act,type");
	
	/*
	변수 처리
	*/
	if(!$type){
		$type = "new";
	}
	
	/*
	수정 모드인 경우 기본 정보 로드
	*/
	if($type=="modify"){
		$mysql->select("
			SELECT *
			FROM toony_module_board_config
			WHERE board_id='$act'
		");
		$mysql->fetchArray("write_point,read_point,skin,board_id,name,list_limit,length_limit,use_secret,use_comment,use_likes,use_category,category,use_reply,use_vote,use_file1,use_file2,use_list,file_limit,void_html,controll_level,write_level,read_level,secret_level,comment_level,array_level,reply_level,delete_level,top_file,bottom_file,thumb_width,thumb_height,articleIMG_width,articleIMG_height,article_length,ico_file,ico_mobile,ico_secret,ico_secret_def,ico_new,ico_new_def,ico_hot,ico_hot_def,tc_1,tc_2,tc_3,tc_4,tc_5");
		$array = $mysql->array;
		$mysql->htmlspecialchars = 0;
		$mysql->nl2br = 0;
		$array['top_source'] = $mysql->fetch("top_source");
		$array['bottom_source'] = $mysql->fetch("bottom_source");
	}
	
	/*
	홈페이지&모바일페이지 설정 값이 함께 기록되는 필드인 경우 분리
	*/
	$use_list_exp = explode("|",$array['use_list']);
	$array['use_list'] = $use_list_exp[0];
	$array['use_m_list'] = $use_list_exp[1];
	$list_limit_exp = explode("|",$array['list_limit']);
	$array['list_limit'] = $list_limit_exp[0];
	$array['list_m_limit'] = $list_limit_exp[1];
	$length_limit_exp = explode("|",$array['length_limit']);
	$array['length_limit'] = $length_limit_exp[0];
	$array['length_m_limit'] = $length_limit_exp[1];
	$article_length_exp = explode("|",$array['article_length']);
	$array['article_length'] = $article_length_exp[0];
	$array['article_m_length'] = $article_length_exp[1];
	$thumb_width_exp = explode("|",$array['thumb_width']);
	$array['thumb_width'] = $thumb_width_exp[0];
	$array['thumb_m_width'] = $thumb_width_exp[1];
	$thumb_height_exp = explode("|",$array['thumb_height']);
	$array['thumb_height'] = $thumb_height_exp[0];
	$array['thumb_m_height'] = $thumb_height_exp[1];
	$articleIMG_width_exp = explode("|",$array['articleIMG_width']);
	$array['articleIMG_width'] = $articleIMG_width_exp[0];
	$array['articleIMG_m_width'] = $articleIMG_width_exp[1];
	$articleIMG_height_exp = explode("|",$array['articleIMG_height']);
	$array['articleIMG_height'] = $articleIMG_height_exp[0];
	$array['articleIMG_m_height'] = $articleIMG_height_exp[1];
	$top_file_exp = explode("{||||||||||}",$array['top_file']);
	$array['top_file'] = $top_file_exp[0];
	$array['top_m_file'] = $top_file_exp[1];
	$bottom_file_exp = explode("{||||||||||}",$array['bottom_file']);
	$array['bottom_file'] = $bottom_file_exp[0];
	$array['bottom_m_file'] = $bottom_file_exp[1];
	$top_source_exp = explode("{||||||||||}",$array['top_source']);
	$array['top_source'] = $top_source_exp[0];
	$array['top_m_source'] = $top_source_exp[1];
	$bottom_source_exp = explode("{||||||||||}",$array['bottom_source']);
	$array['bottom_source'] = $bottom_source_exp[0];
	$array['bottom_m_source'] = $bottom_source_exp[1];
	$ico_file_exp = explode("|",$array['ico_file']);
	$array['use_ico_file_p'] = $ico_file_exp[0];
	$array['use_ico_file_m'] = $ico_file_exp[1];
	$ico_mobile_exp = explode("|",$array['ico_mobile']);
	$array['use_ico_mobile_p'] = $ico_mobile_exp[0];
	$array['use_ico_mobile_m'] = $ico_mobile_exp[1];
	$ico_secret_exp = explode("|",$array['ico_secret']);
	$array['use_ico_secret_p'] = $ico_secret_exp[0];
	$array['use_ico_secret_m'] = $ico_secret_exp[1];
	$array['use_ico_secret_def'] = $array['ico_secret_def'];
	$ico_new_exp = explode("|",$array['ico_new']);
	$array['use_ico_new_p'] = $ico_new_exp[0];
	$array['use_ico_new_m'] = $ico_new_exp[1];
	$ico_hot_exp = explode("|",$array['ico_hot']);
	$array['use_ico_hot_p'] = $ico_hot_exp[0];
	$array['use_ico_hot_m'] = $ico_hot_exp[1];
	$ico_hot_def_exp = explode("|",$array['ico_hot_def']);
	$array['ico_hot_def0'] = $ico_hot_def_exp[0];
	$array['ico_hot_def1'] = $ico_hot_def_exp[1];
	$array['ico_hot_def2'] = $ico_hot_def_exp[2];
	
	/*
	검사
	*/
	if($type=="modify"&&$mysql->numRows()<1){
		$lib->error_alert_location("게시판이 존재하지 않습니다.",$site_config['ad_site_url'],"A");
	}
	if(!$type){
		$lib->error_alert_location("호출 값이 없습니다.",$site_config['ad_site_url'],"A");
	}
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("modules/board/admin/_tpl/boardList_modify.html");
	
	/*
	템플릿 함수
	*/
	function use_checked($var,$fieldName){
		global $array;
		switch($array['use_'."$fieldName"]){
			case "$var" : 
				return "checked";
				break;
			default :
		}
	}
	function level_option_value($fieldName,$slt){
		global $array,$member_type_var;
		$option = "";
		if($fieldName=="undefined"){
			for($i=1;$i<=10;$i++){
				$selected_var = "";
				if($slt==$i){
					$selected_var = "selected";
				}
				$option .= "<option value=\"".$i."\" ".$selected_var.">".$i." (".$member_type_var[$i].")</option>\n";
			}
		}else{
			for($i=1;$i<=10;$i++){
				$selected_var = "";
				if($array[$fieldName."_level"]==$i){
					$selected_var = "selected";
				}
				$option .= "<option value=\"".$i."\" ".$selected_var.">".$i." (".$member_type_var[$i].")</option>\n";
			}
		}
		return $option;
	}
	function skin_option_value($fieldName){
		global $array;
		$path = opendir(__DIR_PATH__."modules/board/skin/");
		$i = 0;
		while($dir = readdir($path)){
			if($dir!="."&&$dir!=".."){
				$skins[$i] = $dir;
			}
			$i++;
		}
		$option = "";
		foreach($skins as $key=>$val){
			$selected_var = "";
			if($val==$array[$fieldName]){
				$selected_var = "selected";
			}
			$option .= "<option value=\"".$val."\" ".$selected_var.">".$val."</option>\n";
		}
		return $option;
	}
	
	function ico_hot_def_options_value(){
		global $ico_hot_def_exp;
		if($ico_hot_def_exp[1]=="OR"){
			$selected_var_or = "selected";
			$selected_var_and = "";
		}else if($ico_hot_def_exp[1]=="AND"){
			$selected_var_or = "";
			$selected_var_and = "selected";
		}else{
			$selected_var_or = "";
			$selected_var_and = "";
		}
		return "
			<option value=\"OR\" $selected_var_or>또는</option>\n
			<option value=\"AND\" $selected_var_and>그리고</option>
		";
	}

	/*
	템플릿 치환
	*/
	if($type=="modify"){
		$tpl->skin_modeling_hideArea("[{name_new_start}]","[{name_new_end}]","hide");
		$tpl->skin_modeling_hideArea("[{name_modify_start}]","[{name_modify_end}]","show");
		$tpl->skin_modeling_hideArea("[{deleteBtn_start}]","[{deleteBtn_end}]","show");
	}else if($type=="new"){
		$tpl->skin_modeling_hideArea("[{name_new_start}]","[{name_new_end}]","show");
		$tpl->skin_modeling_hideArea("[{name_modify_start}]","[{name_modify_end}]","hide");
		$tpl->skin_modeling_hideArea("[{deleteBtn_start}]","[{deleteBtn_end}]","hide");
	}
	if($type=="modify"){
		$tpl->skin_modeling("[use_comment_checked_Y]",use_checked("Y","comment"));
		$tpl->skin_modeling("[use_comment_checked_N]",use_checked("N","comment"));
		$tpl->skin_modeling("[use_secret_checked_Y]",use_checked("Y","secret"));
		$tpl->skin_modeling("[use_secret_checked_N]",use_checked("N","secret"));
		$tpl->skin_modeling("[use_likes_checked_Y]",use_checked("Y","likes"));
		$tpl->skin_modeling("[use_likes_checked_N]",use_checked("N","likes"));
		$tpl->skin_modeling("[use_reply_checked_Y]",use_checked("Y","reply"));
		$tpl->skin_modeling("[use_reply_checked_N]",use_checked("N","reply"));
		$tpl->skin_modeling("[use_vote_checked_Y]",use_checked("Y","vote"));
		$tpl->skin_modeling("[use_vote_checked_N]",use_checked("N","vote"));
		$tpl->skin_modeling("[use_file1_checked_Y]",use_checked("Y","file1"));
		$tpl->skin_modeling("[use_file1_checked_N]",use_checked("N","file1"));
		$tpl->skin_modeling("[use_file2_checked_Y]",use_checked("Y","file2"));
		$tpl->skin_modeling("[use_file2_checked_N]",use_checked("N","file2"));
		$tpl->skin_modeling("[use_list_checked_Y]",use_checked("Y","list"));
		$tpl->skin_modeling("[use_list_checked_N]",use_checked("N","list"));
		$tpl->skin_modeling("[use_m_list_checked_Y]",use_checked("Y","m_list"));
		$tpl->skin_modeling("[use_m_list_checked_N]",use_checked("N","m_list"));
		$tpl->skin_modeling("[use_category_checked_Y]",use_checked("Y","category"));
		$tpl->skin_modeling("[use_category_checked_N]",use_checked("N","category"));
		$tpl->skin_modeling("[category_value]",$array['category']);
		$tpl->skin_modeling("[list_limit_value]",$array['list_limit']);
		$tpl->skin_modeling("[list_m_limit_value]",$array['list_m_limit']);
		$tpl->skin_modeling("[length_limit_value]",$array['length_limit']);
		$tpl->skin_modeling("[length_m_limit_value]",$array['length_m_limit']);
		$tpl->skin_modeling("[file_limit_value]",$array['file_limit']);
		$tpl->skin_modeling("[skin_option_value]",skin_option_value("skin"));
		$tpl->skin_modeling("[controll_level_option_value]",level_option_value("controll",""));
		$tpl->skin_modeling("[write_level_option_value]",level_option_value("write",""));
		$tpl->skin_modeling("[read_level_option_value]",level_option_value("read",""));
		$tpl->skin_modeling("[secret_level_option_value]",level_option_value("secret",""));
		$tpl->skin_modeling("[comment_level_option_value]",level_option_value("comment",""));
		$tpl->skin_modeling("[array_level_option_value]",level_option_value("array",""));
		$tpl->skin_modeling("[reply_level_option_value]",level_option_value("reply",""));
		$tpl->skin_modeling("[delete_level_option_value]",level_option_value("delete",""));
		$tpl->skin_modeling("[write_point_value]",$array['write_point']);
		$tpl->skin_modeling("[read_point_value]",$array['read_point']);
		$tpl->skin_modeling("[top_file]",$array['top_file']);
		$tpl->skin_modeling("[top_m_file]",$array['top_m_file']);
		$tpl->skin_modeling("[top_source]",$array['top_source']);
		$tpl->skin_modeling("[top_m_source]",$array['top_m_source']);
		$tpl->skin_modeling("[bottom_file]",$array['bottom_file']);
		$tpl->skin_modeling("[bottom_m_file]",$array['bottom_m_file']);
		$tpl->skin_modeling("[bottom_source]",$array['bottom_source']);
		$tpl->skin_modeling("[bottom_m_source]",$array['bottom_m_source']);
		$tpl->skin_modeling("[thumb_width_value]",$array['thumb_width']);
		$tpl->skin_modeling("[thumb_m_width_value]",$array['thumb_m_width']);
		$tpl->skin_modeling("[thumb_height_value]",$array['thumb_height']);
		$tpl->skin_modeling("[thumb_m_height_value]",$array['thumb_m_height']);
		$tpl->skin_modeling("[articleIMG_width_value]",$array['articleIMG_width']);
		$tpl->skin_modeling("[articleIMG_m_width_value]",$array['articleIMG_m_width']);
		$tpl->skin_modeling("[articleIMG_height_value]",$array['articleIMG_height']);
		$tpl->skin_modeling("[articleIMG_m_height_value]",$array['articleIMG_m_height']);
		$tpl->skin_modeling("[article_length_value]",$array['article_length']);
		$tpl->skin_modeling("[article_m_length_value]",$array['article_m_length']);
		$tpl->skin_modeling("[ico_secret_def_checked_Y]",use_checked("Y","ico_secret_def"));
		$tpl->skin_modeling("[ico_secret_def_checked_N]",use_checked("N","ico_secret_def"));
		$tpl->skin_modeling("[ico_file_p_checked_Y]",use_checked("Y","ico_file_p"));
		$tpl->skin_modeling("[ico_file_p_checked_N]",use_checked("N","ico_file_p"));
		$tpl->skin_modeling("[ico_file_m_checked_Y]",use_checked("Y","ico_file_m"));
		$tpl->skin_modeling("[ico_file_m_checked_N]",use_checked("N","ico_file_m"));
		$tpl->skin_modeling("[ico_secret_p_checked_Y]",use_checked("Y","ico_secret_p"));
		$tpl->skin_modeling("[ico_secret_p_checked_N]",use_checked("N","ico_secret_p"));
		$tpl->skin_modeling("[ico_secret_m_checked_Y]",use_checked("Y","ico_secret_m"));
		$tpl->skin_modeling("[ico_secret_m_checked_N]",use_checked("N","ico_secret_m"));
		$tpl->skin_modeling("[ico_new_def_value]",$array['ico_new_def']);
		$tpl->skin_modeling("[ico_new_p_checked_Y]",use_checked("Y","ico_new_p"));
		$tpl->skin_modeling("[ico_new_p_checked_N]",use_checked("N","ico_new_p"));
		$tpl->skin_modeling("[ico_new_m_checked_Y]",use_checked("Y","ico_new_m"));
		$tpl->skin_modeling("[ico_new_m_checked_N]",use_checked("N","ico_new_m"));
		$tpl->skin_modeling("[ico_hot_def_read_value]",$array['ico_hot_def2']);
		$tpl->skin_modeling("[ico_hot_def_options_value]",ico_hot_def_options_value());
		$tpl->skin_modeling("[ico_hot_def_vote_value]",$array['ico_hot_def0']);
		$tpl->skin_modeling("[ico_hot_p_checked_Y]",use_checked("Y","ico_hot_p"));
		$tpl->skin_modeling("[ico_hot_p_checked_N]",use_checked("N","ico_hot_p"));
		$tpl->skin_modeling("[ico_hot_m_checked_Y]",use_checked("Y","ico_hot_m"));
		$tpl->skin_modeling("[ico_hot_m_checked_N]",use_checked("N","ico_hot_m"));
		$tpl->skin_modeling("[ico_mobile_p_checked_Y]",use_checked("Y","ico_mobile_p"));
		$tpl->skin_modeling("[ico_mobile_p_checked_N]",use_checked("N","ico_mobile_p"));
		$tpl->skin_modeling("[ico_mobile_m_checked_Y]",use_checked("Y","ico_mobile_m"));
		$tpl->skin_modeling("[ico_mobile_m_checked_N]",use_checked("N","ico_mobile_m"));
		$tpl->skin_modeling("[tc_1]",$array['tc_1']);
		$tpl->skin_modeling("[tc_2]",$array['tc_2']);
		$tpl->skin_modeling("[tc_3]",$array['tc_3']);
		$tpl->skin_modeling("[tc_4]",$array['tc_4']);
		$tpl->skin_modeling("[tc_5]",$array['tc_5']);
		
	}else{
		$tpl->skin_modeling("[use_comment_checked_Y]","checked");
		$tpl->skin_modeling("[use_comment_checked_N]","");
		$tpl->skin_modeling("[use_secret_checked_Y]","checked");
		$tpl->skin_modeling("[use_secret_checked_N]","");
		$tpl->skin_modeling("[use_likes_checked_Y]","checked");
		$tpl->skin_modeling("[use_likes_checked_N]","");
		$tpl->skin_modeling("[use_reply_checked_Y]","checked");
		$tpl->skin_modeling("[use_reply_checked_N]","");
		$tpl->skin_modeling("[use_vote_checked_Y]","checked");
		$tpl->skin_modeling("[use_vote_checked_N]","");
		$tpl->skin_modeling("[use_file1_checked_Y]","checked");
		$tpl->skin_modeling("[use_file1_checked_N]","");
		$tpl->skin_modeling("[use_file2_checked_Y]","");
		$tpl->skin_modeling("[use_file2_checked_N]","checked");
		$tpl->skin_modeling("[use_list_checked_Y]","checked");
		$tpl->skin_modeling("[use_list_checked_N]","");
		$tpl->skin_modeling("[length_m_limit_value]","20");
		$tpl->skin_modeling("[list_m_limit_value]","5");
		$tpl->skin_modeling("[use_m_list_checked_Y]","");
		$tpl->skin_modeling("[use_m_list_checked_N]","checked");
		$tpl->skin_modeling("[use_category_checked_Y]","");
		$tpl->skin_modeling("[use_category_checked_N]","checked");
		$tpl->skin_modeling("[category_value]","");
		$tpl->skin_modeling("[list_limit_value]","15");
		$tpl->skin_modeling("[length_limit_value]","50");
		$tpl->skin_modeling("[file_limit_value]","5242880");
		$tpl->skin_modeling("[skin_option_value]",skin_option_value("undefined"));
		$tpl->skin_modeling("[controll_level_option_value]",level_option_value("undefined","3"));
		$tpl->skin_modeling("[write_level_option_value]",level_option_value("undefined","9"));
		$tpl->skin_modeling("[read_level_option_value]",level_option_value("undefined","9"));
		$tpl->skin_modeling("[secret_level_option_value]",level_option_value("undefined","1"));
		$tpl->skin_modeling("[comment_level_option_value]",level_option_value("undefined","9"));
		$tpl->skin_modeling("[array_level_option_value]",level_option_value("undefined","9"));
		$tpl->skin_modeling("[reply_level_option_value]",level_option_value("undefined","9"));
		$tpl->skin_modeling("[delete_level_option_value]",level_option_value("undefined","9"));
		$tpl->skin_modeling("[write_point_value]","10");
		$tpl->skin_modeling("[read_point_value]","0");
		$tpl->skin_modeling("[top_file]","");
		$tpl->skin_modeling("[top_m_file]","");
		$tpl->skin_modeling("[top_source]","");
		$tpl->skin_modeling("[top_m_source]","");
		$tpl->skin_modeling("[bottom_file]","");
		$tpl->skin_modeling("[bottom_m_file]","");
		$tpl->skin_modeling("[bottom_source]","");
		$tpl->skin_modeling("[bottom_m_source]","");
		$tpl->skin_modeling("[thumb_width_value]","120");
		$tpl->skin_modeling("[thumb_m_width_value]","200");
		$tpl->skin_modeling("[thumb_height_value]","100");
		$tpl->skin_modeling("[thumb_m_height_value]","80");
		$tpl->skin_modeling("[article_length_value]","90");
		$tpl->skin_modeling("[article_m_length_value]","50");
		$tpl->skin_modeling("[articleIMG_width_value]","400");
		$tpl->skin_modeling("[articleIMG_m_width_value]","250");
		$tpl->skin_modeling("[articleIMG_height_value]","400");
		$tpl->skin_modeling("[articleIMG_m_height_value]","250");
		$tpl->skin_modeling("[ico_secret_def_checked_Y]","");
		$tpl->skin_modeling("[ico_secret_def_checked_N]","checked");
		$tpl->skin_modeling("[ico_file_p_checked_Y]","checked");
		$tpl->skin_modeling("[ico_file_p_checked_N]","");
		$tpl->skin_modeling("[ico_file_m_checked_Y]","checked");
		$tpl->skin_modeling("[ico_file_m_checked_N]","");
		$tpl->skin_modeling("[ico_secret_p_checked_Y]","checked");
		$tpl->skin_modeling("[ico_secret_p_checked_N]","");
		$tpl->skin_modeling("[ico_secret_m_checked_Y]","checked");
		$tpl->skin_modeling("[ico_secret_m_checked_N]","");
		$tpl->skin_modeling("[ico_new_def_value]","4320");
		$tpl->skin_modeling("[ico_new_p_checked_Y]","checked");
		$tpl->skin_modeling("[ico_new_p_checked_N]","");
		$tpl->skin_modeling("[ico_new_m_checked_Y]","checked");
		$tpl->skin_modeling("[ico_new_m_checked_N]","");
		$tpl->skin_modeling("[ico_hot_def_read_value]","50");
		$tpl->skin_modeling("[ico_hot_def_options_value]",ico_hot_def_options_value());
		$tpl->skin_modeling("[ico_hot_def_vote_value]","10");
		$tpl->skin_modeling("[ico_hot_p_checked_Y]","");
		$tpl->skin_modeling("[ico_hot_p_checked_N]","checked");
		$tpl->skin_modeling("[ico_hot_m_checked_Y]","");
		$tpl->skin_modeling("[ico_hot_m_checked_N]","checked");
		$tpl->skin_modeling("[ico_mobile_p_checked_Y]","checked");
		$tpl->skin_modeling("[ico_mobile_p_checked_N]","");
		$tpl->skin_modeling("[ico_mobile_m_checked_Y]","checked");
		$tpl->skin_modeling("[ico_mobile_m_checked_N]","");
		$tpl->skin_modeling("[tc_1]","");
		$tpl->skin_modeling("[tc_2]","");
		$tpl->skin_modeling("[tc_3]","");
		$tpl->skin_modeling("[tc_4]","");
		$tpl->skin_modeling("[tc_5]","");
	}
	$tpl->skin_modeling("[board_id]",$array['board_id']);
	$tpl->skin_modeling("[type_value]",$type);
	$tpl->skin_modeling("[name]",$array['name']);
	$tpl->skin_modeling("[max_file_limit]",ini_get('upload_max_filesize')."  ( ".(ini_get('upload_max_filesize')*1024*1024)." byte )");
	
	echo $tpl->skin_echo();
?>