<?php
	include_once __DIR_PATH__."modules/board/install/installCheck.php";
	
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
		FROM toony_module_board_config
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
	function array_board_text_limit($text,$num){
		$lib = new libraryClass();
		return htmlspecialchars(stripslashes($lib->func_length_limit($text,0,$num)));
	}
	function array_board_data_count($board){
		$query = new mysqlConnection();
		$query->select("
			SELECT COUNT(*) data_count
			FROM toony_module_board_data_$board
			ORDER BY regdate DESC
		");
		return $query->fetch("data_count");
	}
	
	/*
	템플릿 로드
	*/
	$header->skin_file_path("modules/board/admin/_tpl/boardList.html");
	$header->skin_loop_header("[{loop_start}]");
	$loop->skin_file_path("modules/board/admin/_tpl/boardList.html");
	$loop->skin_loop_array("[{loop_start}]","[{loop_end}]");
	$footer->skin_file_path("modules/board/admin/_tpl/boardList.html");
	$footer->skin_loop_footer("[{loop_end}]");

	/*
	템플릿 치환
	*/
	//header
	echo $header->skin_echo();
	//loop
	if($array_total>0){
		$i = 0;
		do{
			$mysql->fetchArray("board_id,title,name,regdate,skin");
			$array = $mysql->array;
			$loop->skin_modeling("[title]","<a href=\"".__URL_PATH__."admin/?m=board&p=boardList_modify&type=modify&act=".$array['board_id']."\">".$array['name']."</a>");
			$loop->skin_modeling("[name]",$array['board_id']);
			$loop->skin_modeling("[number]",$paging->getNo($i));$i++;
			$loop->skin_modeling("[data_count]",array_board_data_count($array['board_id']));
			$loop->skin_modeling("[title]",array_board_text_limit($array['title'],25));
			$loop->skin_modeling("[ment]",array_board_text_limit($array['name'],25));
			$loop->skin_modeling("[board_id]",$array['board_id']);
			$loop->skin_modeling("[skin]",$array['skin']);
			$loop->skin_modeling("[modify_btn]","<a href=\"".__URL_PATH__."admin/?m=board&p=boardList_modify&type=modify&act=".$array['board_id']."\" class=\"__btn_s_setting\" title=\"설정 변경\"></a>");
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
	$footer->skin_modeling("[paging_area]",$paging->Show(__URL_PATH__."admin/?m=board&p=boardList"));
	
	
	echo $footer->skin_echo();
?>