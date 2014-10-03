<?php
	$tpl = new skinController();
	$header = new skinController();
	$loop = new skinController();
	$footer = new skinController();
	$mysql = new mysqlConnection();
	$lib = new libraryClass();
	$paging = new pagingClass();
	$method = new methodController();
	
	$method->method_param("GET","page,keyword,where,order,orderby");
	
	/*
	정렬 기준 설정
	*/
	if(!$order){ $order = "A.regdate"; }
	if(!$orderby){ $orderby = "DESC"; }
	$array_order = $order." ".$orderby;
	
	/*
	페이징 설정
	*/
	$paging_query = "
		SELECT A.*,B.*,C.re_idno re_idno
		FROM toony_customer_qna A
		LEFT OUTER JOIN toony_member_list B
		ON A.me_idno=B.me_idno
		LEFT OUTER JOIN toony_customer_qna C
		ON A.idno=C.re_idno
		WHERE A.re_idno=0
		ORDER BY $array_order
	";
	$mysql->select($paging_query);
	$paging_query_no = $mysql->numRows();
	$paging->page_param($page);
	$total_num = $paging->setTotal($paging_query_no);
	$paging->setListPerPage(10);
	$sql = $paging->getPaggingQuery($paging_query);
	$mysql->select($sql);
	$array_total = $mysql->numRows();
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/questionList.html");
	$header->skin_html_load($tpl->skin);
	$header->skin_loop_header("[{loop_start}]");
	$loop->skin_html_load($tpl->skin);
	$loop->skin_loop_array("[{loop_start}]","[{loop_end}]");
	$footer->skin_html_load($tpl->skin);
	$footer->skin_loop_footer("[{loop_end}]");

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
	//답변 여부를 출력
	function func_answer($re_idno){
		if($re_idno!=""){
			return "<span style='color:#EA3959; font-size:11px; letter-spacing:-1px;'><strong>완료</strong></span>";
		}else{
			return "<span style='color:#999999; font-size:11px; letter-spacing:-1px;'>대기</span>";
		}
	}
	
	/*
	템플릿 치환
	*/
	//header
	$header->skin_modeling("[order_value]",$order);
	$header->skin_modeling("[orderby_value]",$orderby);
	echo $header->skin_echo();
	//loop
	if($array_total>0){
		$i = 0;
		do{
			$mysql->fetchArray("re_idno,idno,memo,regdate,me_idno,me_id,me_level,me_nick,me_regdate,cst_email,cst_name,cst_phone");
			$array = $mysql->array;
			$loop->skin_modeling("[number]",$paging->getNo($i));$i++;
			$loop->skin_modeling("[member_type]",member_type_func());
			$loop->skin_modeling("[name]",name_func());
			$loop->skin_modeling("[regdate]","<span title=\"".$array['regdate']."\">".date("Y.m.d",strtotime($array['regdate']))."</span>");
			$loop->skin_modeling("[answer]",func_answer($array['re_idno']));
			$loop->skin_modeling("[memo]","<a href=\"".__URL_PATH__."admin/?p=questionList_view&act=".$array['idno']."\">".$lib->func_length_limit(strip_tags($lib->htmlDecode($array['memo'])),0,20)."</a>");
			echo $loop->skin_echo();
		}while($mysql->nextRec());
	}
	//footer
	if($array_total>0){
		$footer->skin_modeling_hideArea("[{not_content_start}]","[{not_content_end}]","hide");
	}else{
		$footer->skin_modeling_hideArea("[{not_content_start}]","[{not_content_end}]","show");
	}
	$footer->skin_modeling("[paging_area]",$paging->Show(__URL_PATH__."admin/?p=questionList&order={$order}&orderby={$orderby}"));
	
	echo $footer->skin_echo();
?>