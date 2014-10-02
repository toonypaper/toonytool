<?php
	include_once "include/pageJustice.inc.php";
	
	$method = new methodController();
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$paging = new pagingClass();
	
	$method->method_param("GET","page");
	
	/*
	검사
	*/
	$lib->func_page_level(__URL_PATH__."{$viewDir}?article=login&redirect=".urlencode("?article=").$article,9);
	
	/*
	페이징 설정
	*/
	$paging_query = "
		SELECT *
		FROM toony_member_point
		WHERE me_idno='{$member['me_idno']}'
		ORDER BY regdate DESC
	";
	$mysql->select($paging_query);
	$paging_query_no = $mysql->numRows();
	$paging->page_param($page);
	$total_num = $paging->setTotal($paging_query_no);
	$paging->setListPerPage(15);
	$sql = $paging->getPaggingQuery($paging_query);
	$mysql->select($sql);
	$array_total = $mysql->numRows();
	
	/*
	템플릿 로드
	*/
	//Header
	$header = new skinController();
	$header->skin_file_path("_tpl/{$viewDir}myPoint.html");
	$header->skin_loop_header("[{loop_start}]");
	//Loop
	$loop = new skinController();
	$loop->skin_file_path("_tpl/{$viewDir}myPoint.html");
	$loop->skin_loop_array("[{loop_start}]","[{loop_end}]");
	//Footer
	$footer = new skinController();
	$footer->skin_file_path("_tpl/{$viewDir}myPoint.html");
	$footer->skin_loop_footer("[{loop_end}]");
	
	/*
	템플릿 함수
	*/
	function sex_checked_value_func($obj){
		global $array;
		switch($array['me_sex']){
			case "M" :
				if($obj=="M"){
					return "checked";
				}else{
					return "";
				}
				break;
			case "F" :
				if($obj=="F"){
					return "checked";
				}else{
					return "";
				}
				break;
		}
	}
	
	/*
	템플릿 치환
	*/
	//header
	$header->skin_modeling("[total]",number_format($member['me_point']));
	echo $header->skin_echo();
	//loop
	if($array_total>0){
		do{
			$mysql->fetchArray("point_in,point_out,memo,regdate");
			$array = $mysql->array;
			if(!$array['point_in']){
				$array['point_in'] = 0;
			}
			if(!$array['point_out']){
				$array['point_out'] = 0;
			}
			$loop->skin_modeling("[date]",date("Y.m.d",strtotime($array['regdate'])));
			$loop->skin_modeling("[in]",number_format($array['point_in']));
			$loop->skin_modeling("[out]",number_format($array['point_out']));
			$loop->skin_modeling("[memo]",$array['memo']);
			echo $loop->skin_echo();
		}while($mysql->nextRec());
	}
	//footer
	if($array_total>0){
		$footer->skin_modeling_hideArea("[{not_loop_start}]","[{not_loop_end}]","hide");
	}else{
		$footer->skin_modeling_hideArea("[{not_loop_start}]","[{not_loop_end}]","show");
	}
	$footer->skin_modeling("[paging_area]",$paging->Show(__URL_PATH__."{$viewDir}?article={$article}"));
	
	echo $footer->skin_echo();
?>