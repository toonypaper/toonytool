<?php
	$globalMysql = new mysqlConnection();
	$session = new sessionController();
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	
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
		$member['me_id'] = NULL;
		$member['me_nick'] = NULL;
		$member['me_phone'] = NULL;
		$member['me_admin'] = NULL;
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
	$globalMysql->fetchArray("ad_site_layout,ad_msite_layout,ad_site_name,ad_site_url,ad_msite_url,ad_use_msite,ad_site_title,ad_email,ad_phone,ad_pavicon,ad_logo,ad_use_smtp,ad_smtp_server,ad_smtp_port,ad_smtp_id,ad_smtp_pwd");
	$site_config = $globalMysql->array;
	
	/*
	방문자 분석을 위한 함수 시작
	*/
	$lib->func_visiter_counter_status(); //방문자 수 기록
	$lib->func_member_online_status(); //현재 접속자 구하기 위한 기록
	$lib->func_index_security(); //블랙리스트 회원 차단을 위한 검사
	
	/*
	관리자 정보가 생성 되었되어 있는지 검사 (없다면 설치 2단계로 이동)
	*/
	$mysql->select("
		SELECT *
		FROM toony_member_list
		WHERE me_admin='Y' AND me_drop_regdate IS NULL
	");
	if($mysql->numRows()<1){
		$lib->error_location(__URL_PATH__."install/step2.php","A");
	}
	
	/*
	모든 모듈의 global.php 를 인클루드,
	모듈 리스트를 Array변수에 저장
	*/
	if($globalMysql->is_table("toony_admin_siteconfig")){
		$modulePath = opendir(__DIR_PATH__."modules/");
		$path_count = 0;
		while($dir = readdir($modulePath)){
			if(($dir!="."&&$dir!="..")){
				$modulesDir[$path_count] = $dir;
				$path_count++;
				include_once __DIR_PATH__."modules/".$dir."/include/global.php";
			}
		}
	}
?>