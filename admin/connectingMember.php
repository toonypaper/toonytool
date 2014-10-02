<?php
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
		SELECT B.*,B.me_login_ip AS status_ip,A.guest_ip
		FROM toony_admin_member_online A
		LEFT OUTER JOIN toony_member_list B
		ON A.me_idno=B.me_idno
		WHERE A.visitdate > DATE_SUB(now(), INTERVAL 55 MINUTE) AND (B.me_admin!='Y' OR B.me_admin IS NULL) AND B.me_drop_regdate IS NULL
		ORDER BY A.visitdate DESC
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
	$header->skin_file_path("admin/_tpl/connectingMember.html");
	$header->skin_loop_header("[{loop_start}]");
	$loop->skin_file_path("admin/_tpl/connectingMember.html");
	$loop->skin_loop_array("[{loop_start}]","[{loop_end}]");
	$footer->skin_file_path("admin/_tpl/connectingMember.html");
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
			$mysql->fetchArray("me_idno,me_id,me_level,me_musicion,me_nick,me_facebook,me_point,me_regdate,status_ip,guest_ip");
			$array = $mysql->array;
			$loop->skin_modeling("[number]",$paging->getNo($i));$i++;
			$loop->skin_modeling("[name]",$array['me_nick']);
			if($array['me_idno']){
				$loop->skin_modeling("[id]","<a href=\"".__URL_PATH__."admin/?p=memberList_modify&act=".$array['me_idno']."\">".$array['me_id']."</a>");
				$loop->skin_modeling("[mail_btn]","<a href=\"".__URL_PATH__."admin/?p=mailling&act=".$array['me_id']."\" class=\"__btn_s_mail\" title=\"메일 발송\"></a>");
				$loop->skin_modeling("[modify_btn]","<a href=\"".__URL_PATH__."admin/?p=memberList_modify&act=".$array['me_idno']."\" class=\"__btn_s_detail\" title=\"상세 보기\"></a>");
				$loop->skin_modeling("[regdate]","<span title=\"".$array['me_regdate']."\">".date("Y.m.d",strtotime($array['me_regdate']))."</span>");
				$loop->skin_modeling("[member_type]",$member_type_var[$array['me_level']]." ({$array['me_level']})");
				$loop->skin_modeling("[point]",number_format($array['me_point']));
			}else{
				$loop->skin_modeling("[id]",$array['guest_ip']);
				$loop->skin_modeling("[mail_btn]","");
				$loop->skin_modeling("[modify_btn]","");
				$loop->skin_modeling("[mail_btn]","");
				$loop->skin_modeling("[modify_btn]","");
				$loop->skin_modeling("[member_type]","비회원");
				$loop->skin_modeling("[regdate]","");
				$loop->skin_modeling("[point]",$array['me_admin']);
				
			}
			echo $loop->skin_echo();
		}while($mysql->nextRec());
	}
	//footer
	if($array_total>0){
		$footer->skin_modeling_hideArea("[{not_content_start}]","[{not_content_end}]","hide");
	}else{
		$footer->skin_modeling_hideArea("[{not_content_start}]","[{not_content_end}]","show");
	}
	$footer->skin_modeling("[paging_area]",$paging->Show(__URL_PATH__."admin/?p=connectingMember&where={$where}&keyword={$keyword}"));

	echo $footer->skin_echo();
?>