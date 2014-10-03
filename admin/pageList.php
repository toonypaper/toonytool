<?php
	$tpl = new skinController();
	$header = new skinController();
	$loop = new skinController();
	$footer = new skinController();
	$mysql = new mysqlConnection();
	$lib = new libraryClass();
	$paging = new pagingClass();
	$method = new methodController();
	
	$method->method_param("GET","page,vtype");
	
	/*
	변수 처리
	*/
	if(!$vtype||($vtype!="p"&&$vtype!="m")){
		$vtype = "p";
	}
	
	/*
	페이징 설정
	*/
	$paging_query = "
		SELECT * 
		FROM toony_page_list
		WHERE vtype='$vtype'
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
	템플릿 함수
	*/
	function tab_active($tab_vtype){
		global $vtype;
		if($vtype==$tab_vtype){
			return " class=\"active\"";
		}else{
			return "";
		}
	}
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/pageList.html");
	$header->skin_html_load($tpl->skin);
	$header->skin_loop_header("[{loop_start}]");
	$loop->skin_html_load($tpl->skin);
	$loop->skin_loop_array("[{loop_start}]","[{loop_end}]");
	$footer->skin_html_load($tpl->skin);
	$footer->skin_loop_footer("[{loop_end}]");

	/*
	템플릿 치환
	*/
	//header
	$header->skin_modeling("[tab_active_p]",tab_active("p"));
	$header->skin_modeling("[tab_active_m]",tab_active("m"));
	echo $header->skin_echo();
	//loop
	if($array_total>0){
		$i = 0;
		do{
			$mysql->fetchArray("idno,name,memo");
			$array = $mysql->array;
			$loop->skin_modeling("[number]",$paging->getNo($i));$i++;
			$loop->skin_modeling("[name]","<a href=\"".__URL_PATH__."admin/?p=pageList_modify&vtype={$vtype}&type=modify&act=".$array['idno']."\">".$array['name']."</a>");
			$loop->skin_modeling("[memo]",$array[memo]);
			$loop->skin_modeling("[modify_btn]","<a href=\"".__URL_PATH__."admin/?p=pageList_modify&vtype={$vtype}&type=modify&act=".$array['idno']."\" class=\"__btn_s_setting\" title=\"설정 변경\"></a>");
			echo $loop->skin_echo();
		}while($mysql->nextRec());
	}
	//footer
	$footer->skin_modeling("[vtype]",$vtype);
	if($array_total>0){
		$footer->skin_modeling_hideArea("[{not_content_start}]","[{not_content_end}]","hide");
	}else{
		$footer->skin_modeling_hideArea("[{not_content_start}]","[{not_content_end}]","show");
	}
	$footer->skin_modeling("[paging_area]",$paging->Show(__URL_PATH__."admin/?p=pageList"));
	echo $footer->skin_echo();
?>