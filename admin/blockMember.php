<?php
	$tpl = new skinController();
	$header = new skinController();
	$loop = new skinController();
	$footer = new skinController();
	$mysql = new mysqlConnection();
	$lib = new libraryClass();
	$paging = new pagingClass();
	$method = new methodController();
	
	$method->method_param("GET","page,where,keyword");
	
	/*
	검색 키워드 설정
	*/
	if(trim($keyword)!=""){
		$array_where = $where." LIKE '%".$keyword."%'";
	}else{
		$array_where = "1";
	}
	
	/*
	페이징 설정
	*/
	$paging_query = "
		SELECT *
		FROM toony_admin_security_member
		WHERE $array_where
		ORDER BY regdate DESC
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
	$tpl->skin_file_path("admin/_tpl/blockMember.html");
	$header->skin_html_load($tpl->skin);
	$header->skin_loop_header("[{loop_start}]");
	$loop->skin_html_load($tpl->skin);
	$loop->skin_loop_array("[{loop_start}]","[{loop_end}]");
	$footer->skin_html_load($tpl->skin);
	$footer->skin_loop_footer("[{loop_end}]");
	
	/*
	템플릿 함수
	*/
	function keyword_value_func(){
		global  $where,$keyword;
		if($where=="me_id"||$where=="memo"){
			return $keyword;
		}else{
			return "";
		}
	}
	function where_value_func($val){
		global $where;
		if($where==$val){
			return "selected";
		}else{
			return "";
		}
	}
	
	/*
	템플릿 치환
	*/
	//header
	$header->skin_modeling("[keyword_value]",keyword_value_func());
	$header->skin_modeling("[keyword_link_value]",urlencode($keyword));
	$header->skin_modeling("[where_value]",$where);
	$header->skin_modeling("[where_link_value]",urlencode($where));
	$header->skin_modeling("[where_value_me_id]",where_value_func("me_id"));
	$header->skin_modeling("[where_value_memo]",where_value_func("memo"));
	echo $header->skin_echo();
	//loop
	if($array_total>0){
		do{
			$mysql->htmlspecialchars = 1;
			$mysql->nl2br = 1;
			$mysql->fetchArray("idno,me_id,me_idno,memo,regdate");
			$array = $mysql->array;
			$array['memo'] = htmlspecialchars($array['memo']);
			$loop->skin_modeling("[idno]",$array['idno']);
			$loop->skin_modeling("[id]","<a href=\"".__URL_PATH__."admin/?p=memberList_modify&act=".$array['me_idno']."\" target=\"_blank\">".$array['me_id']."</a>");
			$loop->skin_modeling("[memo]",$lib->func_length_limit($array['memo'],0,20));
			$loop->skin_modeling("[regdate]","<span title=\"".$array['regdate']."\">".date("Y.m.d",strtotime($array['regdate']))."</span>");
			echo $loop->skin_echo();
		}while($mysql->nextRec());
	}
	//footer
	if($array_total>0){
		$footer->skin_modeling_hideArea("[{not_content_start}]","[{not_content_end}]","hide");
	}else{
		$footer->skin_modeling_hideArea("[{not_content_start}]","[{not_content_end}]","show");
	}
	$footer->skin_modeling("[paging_area]",$paging->Show(__URL_PATH__."admin/?p=blockMember&where={$where}&keyword={$keyword}"));
	
	echo $footer->skin_echo();
?>