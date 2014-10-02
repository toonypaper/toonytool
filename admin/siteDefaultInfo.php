<?php
	$tpl = new skinController();
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/siteDefaultInfo.html");
	
	/*
	템플릿 함수
	*/
	function logo_file_name(){
		global $site_config;
		if($site_config['ad_logo']==""){
			$path = __URL_PATH__."admin/images/siteDefaultInfo_logo.jpg";
		}else{
			$path = __URL_PATH__."upload/siteInformations/".$site_config['ad_logo'];
		}
		return "<img src=\"{$path}\" />";
	}
	
	/*
	템플릿 함수
	*/
	function use_msite_func(){
		global $site_config;
		if($site_config['ad_use_msite']=="Y"){
			return "checked";
		}else{
			return "";
		}
	}
	
	/*
	템플릿 치환
	*/
	if($site_config['ad_pavicon']!=""){
		$tpl->skin_modeling_hideArea("[pavicon_hidden_start]","[pavicon_hidden_end]","show");
	}else{
		$tpl->skin_modeling_hideArea("[pavicon_hidden_start]","[pavicon_hidden_end]","hide");
	}
	$tpl->skin_modeling("[pavicon_file_name]",$site_config['ad_pavicon']);
	$tpl->skin_modeling("[logo_file_name]",logo_file_name());
	$tpl->skin_modeling("[site_name_value]",$site_config['ad_site_name']);
	$tpl->skin_modeling("[site_url_value]",$site_config['ad_site_url']);
	$tpl->skin_modeling("[msite_url_value]",$site_config['ad_msite_url']);
	$tpl->skin_modeling("[use_msite_checked]",use_msite_func());
	$tpl->skin_modeling("[site_title_value]",$site_config['ad_site_title']);
	$tpl->skin_modeling("[ad_email_value]",$site_config['ad_email']);
	$tpl->skin_modeling("[ad_phone_value]",$site_config['ad_phone']);
	
	echo $tpl->skin_echo();
?>