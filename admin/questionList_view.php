<?php
	$tpl = new skinController();
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$method->method_param("GET","act");
	
	/*
	검사
	*/
	$mysql->select("
		SELECT *
		FROM toony_customer_qna
		WHERE idno='$act' AND re_idno=0;
	");
	if($mysql->numRows()<1){
		$lib->error_alert_location("존재하지 않는 문의 입니다.",$site_config['ad_site_url'],"A");
	}
	
	/*
	문의 정보 로드
	*/
	$mysql->select("
		SELECT A.*,B.*,C.re_idno re_idno,C.memo re_memo,C.regdate re_regdate
		FROM toony_customer_qna A
		LEFT OUTER JOIN toony_member_list B
		ON A.me_idno=B.me_idno
		LEFT OUTER JOIN toony_customer_qna C
		ON A.idno=C.re_idno
		WHERE A.idno='$act' AND A.re_idno=0
		ORDER BY A.regdate DESC
	");
	$mysql->fetchArray("re_idno,re_memo,re_regdate,idno,memo,regdate,me_idno,me_id,me_level,me_nick,me_regdate,me_phone,cst_name,cst_email,cst_phone");
	$array = $mysql->array;
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/questionList_view.html");
	
	/*
	템플릿 함수
	*/
	//회원,비회원을 구분하여 레벨을 출력
	function member_type_func(){
		global $array,$member_type_var;
		if($array['me_idno']){
			return $member_type_var[$array['me_level']]." ({$array['me_level']})";
		}else{
			return "비회원";
		}
	}
	//회원,비회원을 구분하여 이름을 출력
	function name_func(){
		global $array;
		if($array['me_idno']){
			return "<a href=\"".__URL_PATH__."admin/?p=memberList_modify&act=".$array['me_idno']."\" target=\"_blank\"><strong>".$array['me_nick']."</strong></a>";
		}else{
			return $array['cst_name'];
		}
	}
	function name_value_func(){
		global $array;
		if($array['me_idno']){
			return $array['me_nick'];
		}else{
			return $array['cst_name'];
		}
	}
	//회원,비회원을 구분하여 이메일을 출력
	function email_func(){
		global $array;
		if($array['me_idno']&&$array['cst_email']==""){
			return $array['me_id'];
		}else{
			return $array['cst_email'];
		}
	}
	//회원,비회원을 구분하여 연락처를 출력
	function phone_func(){
		global $array;
		if($array['me_idno']&&$array['cst_phone']==""){
			return $array['me_phone'];
		}else{
			return $array['cst_phone'];
		}
	}

	/*
	템플릿 치환
	*/
	$tpl->skin_modeling("[idno_value]",$array['idno']);
	$tpl->skin_modeling("[email]",email_func());
	$tpl->skin_modeling("[name]",name_func());
	$tpl->skin_modeling("[name_value]",name_value_func());
	$tpl->skin_modeling("[member_type]",member_type_func());
	$tpl->skin_modeling("[regdate]",date("Y.m.d H시i분s초",strtotime($array['regdate'])));
	$tpl->skin_modeling("[memo]",$array['memo']);
	$tpl->skin_modeling("[phone]",phone_func());
	if($array['re_idno']!=""){
		$tpl->skin_modeling_hideArea("[{answered_start}]","[{answered_end}]","show");
		$tpl->skin_modeling_hideArea("[{answer_start}]","[{answer_end}]","hide");
		$tpl->skin_modeling_hideArea("[{answerBtn_start}]","[{answerBtn_end}]","hide");
		$tpl->skin_modeling("[re_memo]",$lib->htmldecode($array['re_memo']));
		$tpl->skin_modeling("[re_regdate]",$array['re_regdate']);
	}else{
		$tpl->skin_modeling_hideArea("[{answered_start}]","[{answered_end}]","hide");
		$tpl->skin_modeling_hideArea("[{answer_start}]","[{answer_end}]","show");
		$tpl->skin_modeling_hideArea("[{answerBtn_start}]","[{answerBtn_end}]","show");
	}
	
	echo $tpl->skin_echo();
?>