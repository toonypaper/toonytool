<?php
	$tpl = new skinController();
	$mysql = new mysqlConnection();
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("_tpl/{$viewDir}footer.inc.html");

	/*
	템플릿 함수
	*/
	function footer_status_func(){
		global $site_config,$member,$viewDir;
		$btn = "";
		if($member['me_level']>9){
			//현재 페이지의 uri를 변수에 저장
			$nowUri = urlencode("?".$_SERVER['QUERY_STRING']);
			
			$btn = "<li><a href=\"".__URL_PATH__."{$viewDir}?article=login&redirect={$nowUri}\">회원로그인</a></li>";
			$btn .= "<li><a href=\"".__URL_PATH__."{$viewDir}?article=account\">신규 회원가입</a></li>";
			$btn .= "<li><a href=\"".__URL_PATH__."{$viewDir}?article=findPassword\">비밀번호 찾기</a></li>";
		}else{
			$btn .= "<li><a href=\"".__URL_PATH__."{$viewDir}?article=mypage\">마이페이지</a></li>";
			$btn .= "<li><a href=\"".__URL_PATH__."{$viewDir}?article=member&p=logout.submit\">로그아웃</a></li>";
		}
		if($member['me_level']==1){
			$btn .= "<li><a href=\"".__URL_PATH__."admin/\" target=\"_blank\">관리모드</a></li>";
		}
		return $btn;
	}
	
	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[status]",footer_status_func());
	
	echo $tpl->skin_echo();
?>