<?php
	$tpl = new skinController();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("GET","act,type,vtype");
	
	/*
	변수 처리
	*/
	if(!$vtype||($vtype!="p"&&$vtype!="m")){
		$vtype = "p";
	}
	
	/*
	수정 모드인 경우 기본 정보 로드
	*/
	if($type=="modify"){
		$mysql->select("
			SELECT *
			FROM toony_page_list
			WHERE idno='$act'
		");
		$mysql->fetchArray("name,memo,idno,level,vtype");
		$array = $mysql->array;
		$mysql->htmlspecialchars = 0;
		$mysql->nl2br = 0;
		$array[source] = $mysql->fetch("source");
		$array[scriptCode] = $mysql->fetch("scriptCode");
	}
	
	/*
	검사
	*/
	if($type=="modify"&&$mysql->numRows()<1){
		$lib->error_alert_location("페이지가 존재하지 않습니다.",$site_config['ad_site_url'],"A");
	}
	if(!$type){
		$lib->error_alert_location("호출 값이 없습니다.",$site_config['ad_site_url'],"A");
	}
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/pageList_modify.html");
	
	/*
	템플릿 함수
	*/
	function level_selectbox_options(){
		global $array,$member_type_var,$type;
		for($i=1;$i<=10;$i++){
			$selected_var = "";
			switch($type){
				case "new" :
					if($i==10){
						$selected_var = "selected";
					}
					break;
				case "modify" :
					if($array[level]==$i){
						$selected_var = "selected";
					}
					break;
			}
			$option .= "<option value=\"".$i."\" ".$selected_var.">".$i." (".$member_type_var[$i].")</option>\n";
		}
		return $option;
	}
	function vtypeUrl_func(){
		global $vtype;
		if($vtype=="m"){
			return "m/";
		}else{
			return "";
		}
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
	$tpl->skin_modeling("[scriptCode]",$array['scriptCode']);
	$tpl->skin_modeling("[vtypeUrl]",vtypeUrl_func());
	$tpl->skin_modeling("[type_value]",$type);
	$tpl->skin_modeling("[vtype_value]",$vtype);
	$tpl->skin_modeling("[sourceCode]",$array['source']);
	$tpl->skin_modeling("[idno_value]",$array['idno']);
	$tpl->skin_modeling("[name]",$array[name]);
	$tpl->skin_modeling("[memo_value]",$array['memo']);
	$tpl->skin_modeling("[level_selectbox_options]",level_selectbox_options());
	
	echo $tpl->skin_echo();
?>