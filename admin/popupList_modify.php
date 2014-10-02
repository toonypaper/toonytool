<?php
	$tpl = new skinController();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$lib = new libraryClass();
	
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
			FROM toony_admin_popupconfig
			WHERE name='$act'
		");
		$mysql->fetchArray("name,img,memo,void_use,void_link,link,bleft,btop,target,pop_article,pop_article_txt,start_level,end_level");
		$array = $mysql->array;
	}
	
	/*
	검사
	*/
	if($type=="modify"&&$mysql->numRows()<1){
		$lib->error_alert_location("팝업이 존재하지 않습니다.",$site_config['ad_site_url'],"A");
	}
	if(!$type){
		$lib->error_alert_location("호출 값이 없습니다.",$site_config['ad_site_url'],"A");
	}
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/popupList_modify.html");
	
	/*
	템플릿 함수
	*/
	function target_radio_checked($obj){
		global $array,$type;
		if($type=="new"&&$obj=="_self"){
			return "checked";
		}else{
			if($array['target']==$obj){
				return "checked";
			}else{
				return "";
			}
		}
	}
	function link_radio_checked($obj){
		global $array,$type;
		if($type=="new"&&$obj=="Y"){
			return "checked";
		}else{
			if($array['void_link']==$obj){
				return "checked";
			}else{
				return "";
			}
		}
	}
	function use_radio_checked($obj){
		global $array,$type;
		if($type=="new"&&$obj=="Y"){
			return "checked";
		}else{
			if($array['void_use']==$obj){
				return "checked";
			}else{
				return "";
			}
		}
	}
	function memeber_level_option_value($type){
		global $member_type_var,$array;
		$member_type_var['10'] = "비회원";
		switch($type){
			case "start_level" :
				for($i=1;$i<=10;$i++){
					if(!$array['start_level']){
						$array['start_level'] = 10;
					}
					if($array['start_level']==$i){
						$selected = "selected";
					}else{
						$selected = "";
					}
					$option .= "<option value=\"".$i."\" ".$selected_var." ".$selected.">".$i." (".$member_type_var[$i].")</option>\n";
					
				}
				break;
			case "end_level" :
				for($i=1;$i<=10;$i++){
					if(!$array['end_level']){
						$array['end_level'] = 1;
					}
					if($array['end_level']==$i){
						$selected = "selected";
					}else{
						$selected = "";
					}
					$option .= "<option value=\"".$i."\" ".$selected_var." ".$selected.">".$i." (".$member_type_var[$i].")</option>\n";
					
				}
				break;
		}
		
		return $option;
	}
	function pop_article_option_value(){
		global $array;
		$marray = array("main","all","select");
		$marrayTxt = array("메인 페이지","모든 페이지","직접 선택");
		for($i=0;$i<sizeof($marray);$i++){
			if($marray[$i]==$array['pop_article']){
				$selected = "selected";
			}else{
				$selected = "";
			}
			$option .= "<option value=\"".$marray[$i]."\" ".$selected.">".$marrayTxt[$i]."</option>";
		}
		return $option;
	}

	/*
	템플릿 치환
	*/
	if($type=="modify"){
		$tpl->skin_modeling_hideArea("[{name_new_start}]","[{name_new_end}]","hide");
		$tpl->skin_modeling_hideArea("[{name_modify_start}]","[{name_modify_end}]","show");
		$tpl->skin_modeling_hideArea("[{deleteBtn_start}]","[{deleteBtn_end}]","show");
		$tpl->skin_modeling_hideArea("[{popup_modify_start}]","[{popup_modify_end}]","show");
		$tpl->skin_modeling("[btop]",$array['btop']);
		$tpl->skin_modeling("[bleft]",$array['bleft']);
		$tpl->skin_modeling("[link]",$array['link']);
		$tpl->skin_modeling("[pop_article_txt]",$array['pop_article_txt']);
	}else if($type=="new"){
		$tpl->skin_modeling_hideArea("[{name_new_start}]","[{name_new_end}]","show");
		$tpl->skin_modeling_hideArea("[{name_modify_start}]","[{name_modify_end}]","hide");
		$tpl->skin_modeling_hideArea("[{deleteBtn_start}]","[{deleteBtn_end}]","hide");
		$tpl->skin_modeling_hideArea("[{popup_modify_start}]","[{popup_modify_end}]","hide");
		$tpl->skin_modeling("[btop]",200);
		$tpl->skin_modeling("[bleft]",300);
		$tpl->skin_modeling("[link]","");
		$tpl->skin_modeling("[pop_article_txt]","");
	}
	$tpl->skin_modeling("[site_url]",$site_config['ad_site_url']);
	$tpl->skin_modeling("[name]",$array['name']);
	$tpl->skin_modeling("[memo]",$array['memo']);
	$tpl->skin_modeling("[img_value]",$array['img']);
	$tpl->skin_modeling("[bleft]",$array['bleft']);
	$tpl->skin_modeling("[btop]",$array['btop']);
	$tpl->skin_modeling("[link]",$array['link']);
	$tpl->skin_modeling("[target_radio_self_checked]",target_radio_checked("_self"));
	$tpl->skin_modeling("[target_radio_blank_checked]",target_radio_checked("_blank"));
	$tpl->skin_modeling("[link_radio_Y_checked]",link_radio_checked("Y"));
	$tpl->skin_modeling("[link_radio_N_checked]",link_radio_checked("N"));
	$tpl->skin_modeling("[use_radio_Y_checked]",use_radio_checked("Y"));
	$tpl->skin_modeling("[use_radio_N_checked]",use_radio_checked("N"));
	$tpl->skin_modeling("[img_file_name]",$lib->func_img_resize("upload/siteInformations/",$array['img'],300,300,0,1));
	$tpl->skin_modeling("[type_value]",$type);
	$tpl->skin_modeling("[pop_article_option_value]",pop_article_option_value());
	$tpl->skin_modeling("[memeber_level_option_value]",memeber_level_option_value("start_level"));
	$tpl->skin_modeling("[memeber_level_option_value_end]",memeber_level_option_value("end_level"));
	echo $tpl->skin_echo();
?>