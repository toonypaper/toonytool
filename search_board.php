<?php
	include_once "include/pageJustice.inc.php";
	
	$tpl = new skinController();
	$header = new skinController();
	$tab = new skinController();
	$middle = new skinController();
	$loop = new skinController();
	$data_loop = new skinController();
	$footer = new skinController();
	$method = new methodController();
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	
	$method->method_param("GET","chk_where,keyword,tab_value");

	/*
	Urldecode 처리
	*/
	$keyword = htmlspecialchars(urldecode($keyword));
	$tab_value = htmlspecialchars_decode(urldecode($tab_value));
	
	/*
	keyword AND 처리
	*/
	$keywordEx = explode(" ",$keyword);
	$search_where = "subject=''";
	if(!$chk_where){
		$chk_where = "all";
	}
	for($i=0;$i<count($keywordEx);$i++){
		if($keywordEx[$i]!=""){
			if($chk_where=="all"){
				$search_where .= " OR (subject like '%".$keywordEx[$i]."%' OR ment like '%".$keywordEx[$i]."%')";
			}else if($chk_where=="subject"){
				$search_where .= " OR (subject like '%".$keywordEx[$i]."%')";
			}else if($chk_where=="ment"){
				$search_where .= " OR (ment like '%".$keywordEx[$i]."%')";
			}else{
				$search_where .= " OR (subject like '%".$keywordEx[$i]."%' OR ment like '%".$keywordEx[$i]."%')";
			}
		}
	}
	
	/*
	Tab 클릭한 경우 limit 갯수 조정
	*/
	if($tab_value){
		$search_limit = "";
	}else{
		$search_limit = "LIMIT 5";
	}
	
	/*
	템플릿 로드
	*/
	$tpl->skin_file_path("_tpl/{$viewDir}search_board.html");
	$header->skin_html_load($tpl->skin);
	$header->skin_loop_header("[{tab_start}]");
	$tab->skin_html_load($tpl->skin);
	$tab->skin_loop_array("[{tab_start}]","[{tab_end}]");
	$middle->skin_html_load($tpl->skin);
	$middle->skin_loop_middle("[{tab_end}]","[{loop_start}]");
	$loop->skin_html_load($tpl->skin);
	$loop->skin_loop_array("[{loop_start}]","[{loop_end}]");
	$data_loop->skin_html_load($tpl->skin);
	$data_loop->skin_loop_array("[{data_loop_start}]","[{data_loop_end}]");
	$footer->skin_html_load($tpl->skin);
	$footer->skin_loop_footer("[{loop_end}]");
	
	/*
	템플릿 치환
	*/
	//header
	$header->skin_modeling("[tab_value]",urlencode($tab_value));
	$header->skin_modeling("[tab_value_txt]",$tab_value);
	$header->skin_modeling("[keyword]",urlencode($keyword));
	$header->skin_modeling("[keyword_txt]",$keyword);
	$header->skin_modeling("[chk_where_value]",$chk_where);
	echo $header->skin_echo();
	//tab
	$boardSql = new mysqlConnection();
	$boardSql->select("
		SELECT *
		FROM toony_admin_menuInfo
		WHERE href='pm' AND link like '?m=board%' AND vtype='$viewType' AND drop_regdate IS NULL
		ORDER BY name DESC
	");
	if($boardSql->numRows()>0){
		do{
			$boardSql->fetchArray("name,link,callName");
			$boardArray = $boardSql->array;
			$tab->skin_modeling("[tab_name]",$boardArray['name']);
			$tab->skin_modeling("[tab_link]",$boardArray['name']);
			$tab->skin_modeling("[tab_link_name]",urlencode($boardArray['name']));
			$tab->skin_modeling("[keyword]",urlencode($keyword));
			$tab->skin_modeling("[chk_where_value]",$chk_where);
			echo $tab->skin_echo();
		}while($boardSql->nextRec());
	}
	//middle
	echo $middle->skin_echo();
	//loop
	if($tab_value==""||!$tab_value){
		$data_where = "";
	}else{
		$data_where = "AND name='".$tab_value."'";
	}
	$resultSql = new mysqlConnection();
	$resultSql->select("
		SELECT *
		FROM toony_admin_menuInfo
		WHERE href='pm' AND link like '?m=board%' AND vtype='$viewType' AND drop_regdate IS NULL $data_where
		ORDER BY name DESC
	");
	if($resultSql->numRows()>0){
		do{
			$resultSql->fetchArray("count,name,link,callName");
			$resultArray = $resultSql->array;
			$loop->skin_modeling("[keyword]",urlencode($keyword));
			$loop->skin_modeling("[article_name]",urlencode($resultArray['name']));
			$loop->skin_modeling("[article_name_txt]",$resultArray['name']);
			$loop->skin_modeling("[article_link]",__URL_PATH__."{$viewDir}?article=".$resultArray['callName']);
			//link 에서 파라미터를 변수화
			$parseUrl = parse_url($lib->htmldecode($resultArray['link']));
			$parseStr = $parseUrl['query'];
			parse_str($parseStr,$arrs);
			foreach($arrs as $val=>$key){
				global $$val;
				$$val = $key;
			}
			//검색 결과 타이틀 노출
			$dataSql = new mysqlConnection();
			$dataSql->select("
				SELECT *,
				(
					SELECT count(*) dataCount
					FROM toony_module_board_data_$board_id
					WHERE $search_where
				) dataCount
				FROM toony_module_board_data_$board_id
				WHERE $search_where
				$search_limit
			");
			$loop->skin_modeling_hideArea("[{data_loop_start}]","[{data_loop_end}]","hide");
			$loop->skin_modeling("[total_count]",number_format($dataSql->fetch("dataCount")));
			$loop->skin_modeling("[tab_value]",$tab_value);
			echo $loop->skin_echo();
			//검색 결과 리스트 노출
			if($dataSql->numRows()>0){
				do{
					$dataSql->htmlspecialchars = 1;
					$dataSql->fetchArray("subject,dataCount,idno");
					$dataArray = $dataSql->array;
					$dataSql->htmlspecialchars = 0;
					$dataSql->fetchArray("ment");
					$dataArray = $dataSql->array;
					$data_loop->skin_modeling("[data_title]",$dataArray['subject']);
					$data_loop->skin_modeling("[data_link]",__URL_PATH__."{$viewDir}?article=".$resultArray['callName']."&p=read&read=".$dataArray['idno']);
					$data_loop->skin_modeling("[memo]",$lib->func_length_limit(strip_tags($dataArray['ment']),0,150));
					echo $data_loop->skin_echo();
				}while($dataSql->nextRec());
			}
		}while($resultSql->nextRec());
	}
	//footer
	echo $footer->skin_echo();
?>