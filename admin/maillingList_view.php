<?php
	$tpl = new skinController();
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("GET","act");
	
	/*
	DB 조건 변수 처리
	*/
	if($act){
		$where = "idno={$act}";
	}else{
		$where = "1";
	}
	
	/*
	메일링 정보 로드
	*/
	$mysql->select("
		SELECT A.*,B.*
		FROM toony_admin_mailling A
		LEFT OUTER JOIN toony_member_list B
		ON A.me_idno=B.me_idno
		WHERE $where
		ORDER BY regdate DESC
		LIMIT 1
	");
	$mysql->fetchArray("idno,min_level,max_level,subject,regdate,me_idno,me_nick");
	$array = $mysql->array;
	$mysql->htmlspecialchars = 0;
	$mysql->nl2br = 0;
	$array['memo'] = $mysql->fetch("memo");
	
	/*
	검사
	*/
	if($mysql->numRows()<1){
		$lib->error_alert_location("존재하지 않는 메일링 입니다.",$site_config['ad_site_url'],"A");
	}
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/maillingList_view.html");
	
	/*
	템플릿 함수
	*/
	//수신 범위 출력
	function receive_func(){
		global $array;
		if($array['me_idno']==""){
			return "레벨{$array['min_level']} ~ 레벨{$array['max_level']}";
		}else{
			return "<a href=\"".__URL_PATH__."admin/?p=memberList_modify&act=".$array['me_idno']."\" target=\"_blank\"><strong>".$array['me_nick']."</strong></a>";;
		}
	}
	
	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[receive]",receive_func());
	$tpl->skin_modeling("[subject]",$array['subject']);
	$tpl->skin_modeling("[memo]",$array['memo']);
	$tpl->skin_modeling("[regdate]","<span title=\"".$array['regdate']."\">".date("Y.m.d H:i",strtotime($array['regdate']))."</span>");
	
	echo $tpl->skin_echo();
?>