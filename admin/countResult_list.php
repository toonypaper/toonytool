<?php
	include_once "../include/engine.inc.php";
	include_once __DIR_PATH__."include/global.php";
	
	$tpl = new skinController();
	$header = new skinController();
	$loop = new skinController();
	$footer = new skinController();
	$mysql = new mysqlConnection();
	$paging = new pagingClass();
	$method = new methodController();
	
	$method->method_param("GET","day,page,keyword");
	
	/*
	시간&키워드 초기화
	*/
	$day_var = $day;
	if(trim($keyword)!=""){
		$searchKeyword = "AND B.me_id LIKE '%{$keyword}%'";
	}else{
		$searchKeyword = "";
	}
	
	/*
	페이징 설정
	*/
	$paging_query = "
		SELECT A.me_idno AS g_me_idno,A.me_id AS g_me_id,A.ip,A.regdate,B.me_idno,B.me_id,B.me_nick,B.me_drop_regdate
		FROM toony_admin_counter A
		LEFT OUTER JOIN toony_member_list B
		ON A.me_idno=B.me_idno
		WHERE DATE_FORMAT(A.regdate,'%Y.%m.%d')='$day_var' $searchKeyword
		ORDER BY A.regdate DESC
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
	//회원 아이디 출력
	function list_id(){
		global $array;
		if($array['me_nick']!=""&&$array['me_drop_regdate']==""){
			return "<a href=\"".__URL_PATH__."admin/?p=memberList_modify&act=".$array['me_idno']."\" target=\"_blank\">".htmlspecialchars(stripslashes($array['me_id']))."</a>";
		}else{
			if($array['me_nick']!=""&&$array['me_drop_regdate']!=""){
				return "<span style='text-decoration:line-through;'>".$array['g_me_id']."</span>";
			}else{
				return "비회원";
			}
		}
	}
	//회원 닉네임 출력
	function list_nick(){
		global $array;
		if($array['me_nick']!=""&&$array['me_drop_regdate']==""){
			return htmlspecialchars($array['me_nick']);
		}else{
			if($array['me_nick']!=""&&$array['me_drop_regdate']!=""){
				return "탈퇴회원";
			}else{
				return "비회원";
			}
		}
	}
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("admin/_tpl/countResult_list.html");
	$header->skin_html_load($tpl->skin);
	$header->skin_loop_header("[{loop_start}]");
	$loop->skin_html_load($tpl->skin);
	$loop->skin_loop_array("[{loop_start}]","[{loop_end}]");
	$footer->skin_html_load($tpl->skin);
	$footer->skin_loop_footer("[{loop_end}]");

	/*
	템플릿 함수
	*/
	function month_select_option(){
		global $year_var,$mysql;
		$mysql->select("
			SELECT DATE_FORMAT(regdate,'%Y') month
			FROM toony_admin_counter 
			GROUP BY month
			ORDER BY month DESC;
		");
		do{
			if($year_var==$mysql->fetch("month")){ $op_selected = " selected"; }else{ $op_selected = ""; }
			$option.= "<option value=\"".$mysql->fetch("month")."\"".$op_selected.">".$mysql->fetch("month")."</option>\n";
		}while($mysql->nextRec());
		return $option;
	}

	/*
	템플릿 치환
	*/
	//Header
	echo $header->skin_echo();
	//loop
	if($array_total>0){
		$i = 0;
		do{
			$mysql->fetchArray("me_idno,request,g_me_idno,me_id,g_me_id,me_nick,regdate,ip,me_drop_regdate");
			$array = $mysql->array;
			$array['me_id'] = htmlspecialchars($array['me_id']);
			$array['g_me_id'] = htmlspecialchars($array['g_me_id']);
			$array['me_nick'] = htmlspecialchars($array['me_nick']);
			$loop->skin_modeling("[id]",list_id());
			$loop->skin_modeling("[nick]",list_nick());
			$loop->skin_modeling("[regdate]",date("H:i:s",strtotime($array['regdate'])));
			$loop->skin_modeling("[ip]",$array['ip']);
			echo $loop->skin_echo();
		}while($mysql->nextRec());
	}
	//footer
	$footer->skin_modeling("[paging_area]",$paging->Show_ajax(__URL_PATH__."admin/countResult_list.php?day=".$day_var."&keyword=".$keyword,"#RV_ajaxPagingCont"));
	echo $footer->skin_echo();
?>