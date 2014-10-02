<?php
	$tpl = new skinController();
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("GET","vtype");
	
	/*
	변수 처리
	*/
	if(!$vtype||($vtype!="p"&&$vtype!="m")){
		$vtype = "p";
	}
	
	/*
	기본 설정값 로드
	*/
	$mysql->select("
		SELECT *
		FROM toony_admin_design_bodyStyle
		WHERE vtype='$vtype'
	");
	$mysql->fetchArray("body_bgColor,body_txtColor,body_txtSize,link_txtColor,link_hoverColor,link_activeColor,link_visitedColor,link_txtSize,input_txtColor,input_txtSize,usedefault");
	$array = $mysql->array;
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/bodyStyle.html");
	
	/*
	템플릿 함수
	*/
	function tab_active($tab_vtype){
		global $vtype;
		if($vtype==$tab_vtype){
			return " class=\"active\"";
		}else{
			return "";
		}
	}
	function usedefault_checked(){
		global $array;
		if($array['usedefault']=="Y"){
			return "checked";
		}else{
			return "";
		}
	}
	
	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[tab_active_p]",tab_active("p"));
	$tpl->skin_modeling("[tab_active_m]",tab_active("m"));
	$tpl->skin_modeling("[vtype_value]",$vtype);
	$tpl->skin_modeling("[body_bgColor_value]",$array['body_bgColor']);
	$tpl->skin_modeling("[body_txtColor_value]",$array['body_txtColor']);
	$tpl->skin_modeling("[body_txtSize_value]",$array['body_txtSize']);
	$tpl->skin_modeling("[link_txtColor_value]",$array['link_txtColor']);
	$tpl->skin_modeling("[link_hoverColor_value]",$array['link_hoverColor']);
	$tpl->skin_modeling("[link_activeColor_value]",$array['link_activeColor']);
	$tpl->skin_modeling("[link_visitedColor_value]",$array['link_visitedColor']);
	$tpl->skin_modeling("[link_txtSize_value]",$array['link_txtSize']);
	$tpl->skin_modeling("[input_txtColor_value]",$array['input_txtColor']);
	$tpl->skin_modeling("[input_txtSize_value]",$array['input_txtSize']);
	$tpl->skin_modeling("[usedefault_checked]",usedefault_checked());
	
	echo $tpl->skin_echo();
?>