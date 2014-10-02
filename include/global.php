<?php
	$globalMysql = new mysqlConnection();
	$session = new sessionController();
	$lib = new libraryClass();
	
	/*
	PC모드인지 Mobile모드인지 확인
	*/
	if($viewType=="p"){
		$viewDir = "";
	}else{
		$viewDir = "m/";
	}
	
	/*
	회원의 기본 정보를 가져옴
	*/
	$__toony_member_idno = $session->session_selector("__toony_member_idno");
	if(isset($__toony_member_idno)){
		$globalMysql->select("
			SELECT *
			FROM toony_member_list
			WHERE me_idno=$__toony_member_idno
		");
		$globalMysql->fetchArray("me_idno,me_id,me_password,me_nick,me_name,me_level,me_sex,me_phone,me_telephone,me_regdate,me_login_regdate,me_login_ip,me_point,me_admin");
		$member = $globalMysql->array;
	}else{
		$member['me_level'] = 10;
		$member['me_idno'] = NULL;
	}
	
	/*
	회원 레벨별 명칭을 불러옴
	*/
	$globalMysql->select("
		SELECT ad_member_type
		FROM toony_admin_siteconfig
		LIMIT 1
	");
	for($MT_vars_i=0;$MT_vars_i<=8;$MT_vars_i++){
		$vars = explode(",",htmlspecialchars(stripslashes($globalMysql->fetch("ad_member_type"))));
	}
	for($MT_vars_i=1;$MT_vars_i<=9;$MT_vars_i++){
		$member_type_var[$MT_vars_i] = $vars[$MT_vars_i-1];
	}
	$member_type_var['10'] = "비회원"; 
	
	/*
	사이트 기본 정보 설정 불러옴
	*/
	$globalMysql->select("
		SELECT *
		FROM toony_admin_siteconfig
	");
	$globalMysql->fetchArray("ad_site_name,ad_site_url,ad_msite_url,ad_use_msite,ad_site_title,ad_email,ad_phone,ad_pavicon,ad_logo");
	$site_config = $globalMysql->array;
	
	/*
	방문자 분석을 위한 함수 시작
	*/
	$lib->func_visiter_counter_status(); //방문자 수 기록
	$lib->func_member_online_status(); //현재 접속자 구하기 위한 기록
	$lib->func_index_security(); //블랙리스트 회원 차단을 위한 검사
?>