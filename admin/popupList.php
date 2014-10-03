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
		SELECT * 
		FROM toony_admin_popupconfig
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
	$tpl->skin_file_path("admin/_tpl/popupList.html");
	$header->skin_html_load($tpl->skin);
	$header->skin_loop_header("[{loop_start}]");
	$loop->skin_html_load($tpl->skin);
	$loop->skin_loop_array("[{loop_start}]","[{loop_end}]");
	$footer->skin_html_load($tpl->skin);
	$footer->skin_loop_footer("[{loop_end}]");

	/*
	템플릿 함수
	*/
	function use_void(){
		global $array;
		if($array['void_use']=="Y"){
			return "<span style='color:#EA3959; font-size:11px; letter-spacing:-1px;'><strong>사용</strong></span>";
		}else{
			return "<span style='color:#999999; font-size:11px; letter-spacing:-1px;'>안함</span>";
		}
	}
	function link_func(){
		global $array;
		if($array['link']){
			return "<a href=\"{$array['link']}\" target=\"_blank\" style=\"font-weight:normal;\">{$array['link']}</a>";
		}else{
			return "링크 설정이 되지 않았습니다.";
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
			$mysql->fetchArray("img,name,memo,void_use,link");
			$array = $mysql->array;
			$loop->skin_modeling("[number]",$paging->getNo($i));$i++;
			$loop->skin_modeling("[memo]",$array['memo']);
			$loop->skin_modeling("[link]",link_func());
			$loop->skin_modeling("[use_void]",use_void());
			$loop->skin_modeling("[thumbnail]",$lib->func_img_resize("upload/siteInformations/",$array['img'],120,60,0,0));
			$loop->skin_modeling("[modify_btn]","<a href=\"".__URL_PATH__."admin/?p=popupList_modify&type=modify&act=".$array['name']."\" class=\"__btn_s_setting\" title=\"설정 변경\"></a>");
			echo $loop->skin_echo();
		}while($mysql->nextRec());
	}
	//footer
	if($array_total>0){
		$footer->skin_modeling_hideArea("[{not_content_start}]","[{not_content_end}]","hide");
	}else{
		$footer->skin_modeling_hideArea("[{not_content_start}]","[{not_content_end}]","show");
	}
	$footer->skin_modeling("[paging_area]",$paging->Show(__URL_PATH__."admin/?p=poopupList"));
	
	
	echo $footer->skin_echo();
?>