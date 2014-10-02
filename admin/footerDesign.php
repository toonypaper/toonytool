<?php
	$tpl = new skinController();
	$mysql = new mysqlConnection();
	$method->method_param("GET","vtype");
	
	/*
	변수 처리
	*/
	if(!$vtype||($vtype!="p"&&$vtype!="m")){
		$vtype = "p";
	}
	
	/*
	기본 정보 로드
	*/
	$mysql->select("
		SELECT *
		FROM toony_admin_design_footer
		WHERE vtype='$vtype'
	");
	$mysql->htmlspecialchars = 0;
	$mysql->nl2br = 0;
	$mysql->fetchArray("scriptCode,sourceCode");
	$array = $mysql->array;
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/footerDesign.html");
	
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
	
	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[tab_active_p]",tab_active("p"));
	$tpl->skin_modeling("[tab_active_m]",tab_active("m"));
	$tpl->skin_modeling("[vtype_value]",$vtype);
	$tpl->skin_modeling("[scriptCode]",$array['scriptCode']);
	$tpl->skin_modeling("[sourceCode]",$array['sourceCode']);
	
	echo $tpl->skin_echo();
?>