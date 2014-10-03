<?php
	$tpl = new skinController();
	$header = new skinController();
	$loop = new skinController();
	$footer = new skinController();
	$mysql = new mysqlConnection();
	$lib = new libraryClass();
	$paging = new pagingClass();
	$method = new methodController();
	
	$method->method_param("GET","page");
	
	/*
	페이징 설정
	*/
	$paging_query = "
		SELECT A.*,B.*
		FROM toony_admin_mailling A
		LEFT OUTER JOIN toony_member_list B
		ON A.me_idno=B.me_idno
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
	$tpl->skin_file_path("admin/_tpl/maillingList.html");
	$header->skin_html_load($tpl->skin);
	$header->skin_loop_header("[{loop_start}]");
	$loop->skin_html_load($tpl->skin);
	$loop->skin_loop_array("[{loop_start}]","[{loop_end}]");
	$footer->skin_html_load($tpl->skin);
	$footer->skin_loop_footer("[{loop_end}]");

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
	//header
	echo $header->skin_echo();
	//loop
	if($array_total>0){
		$i = 0;
		do{
			$mysql->htmlspecialchars = 1;
			$mysql->nl2br = 1;
			$mysql->fetchArray("idno,min_level,max_level,memo,regdate,me_idno,me_nick");
			$array = $mysql->array;
			$loop->skin_modeling("[number]",$paging->getNo($i));$i++;
			$loop->skin_modeling("[receive]",receive_func());
			$loop->skin_modeling("[memo]","<a href=\"".__URL_PATH__."admin/?p=maillingList_view&act=".$array['idno']."\">".$lib->func_length_limit(strip_tags($lib->htmldecode($array['memo'])),0,40)."</a>");
			$loop->skin_modeling("[regdate]","<span title=\"".$array['regdate']."\">".date("Y.m.d H:i",strtotime($array['regdate']))."</span>");
			$loop->skin_modeling("[view_btn]","<a href=\"".__URL_PATH__."admin/?p=maillingList_view&act=".$array['idno']."\" class=\"__btn_s_detail\" title=\"상세 보기\"></a>");
			echo $loop->skin_echo();
		}while($mysql->nextRec());
	}
	//footer
	if($array_total>0){
		$footer->skin_modeling_hideArea("[{not_content_start}]","[{not_content_end}]","hide");
	}else{
		$footer->skin_modeling_hideArea("[{not_content_start}]","[{not_content_end}]","show");
	}
	$footer->skin_modeling("[paging_area]",$paging->Show(__URL_PATH__."admin/?p=maillingList"));
	
	echo $footer->skin_echo();
?>