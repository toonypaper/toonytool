<?php
	$tpl = new skinController();
	$mysql = new mysqlConnection();
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/main.html");

	/*
	템플릿 함수
	*/
	//전체 방문자 수를 구함
	function total_visited(){
		global $mysql;
		$mysql->select("
			SELECT *
			FROM toony_admin_counter
			WHERE 1
		");
		return $mysql->numRows();
	}
	//전체 회원 수를 구함
	function total_member(){
		global $mysql;
		$mysql->select("
			SELECT *
			FROM toony_member_list
			WHERE me_drop_regdate IS NULL AND me_admin!='Y'
		");
		return $mysql->numRows();
	}
	//모바일 사용 유무를 텍스트로 치환
	function ad_use_msite_replace(){
		global $site_config;
		if($site_config['ad_use_msite']=="Y"){
			return "모바일 사용함";
		}else{
			return "모바일 사용 안함";
		}
	}
	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[gotoHomepageUrl]",$site_config['ad_site_url']);
	$tpl->skin_modeling("[total_visited]",number_format(total_visited()));
	$tpl->skin_modeling("[total_member]",number_format(total_member()));
	$tpl->skin_modeling("[ad_site_name]",$site_config['ad_site_name']);
	$tpl->skin_modeling("[ad_site_title]",$site_config['ad_site_title']);
	$tpl->skin_modeling("[ad_email]",$site_config['ad_email']);
	$tpl->skin_modeling("[ad_phone]",$site_config['ad_phone']);
	$tpl->skin_modeling("[ad_site_url]",$site_config['ad_site_url']);
	$tpl->skin_modeling("[ad_msite_url]",$site_config['ad_msite_url']);
	$tpl->skin_modeling("[ad_use_msite]",ad_use_msite_replace());
	echo $tpl->skin_echo();
?>