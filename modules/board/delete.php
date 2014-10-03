<?php
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	$fileUploader = new fileUploader();
	$skin_delete_form = new skinController();
	
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	$method->method_param("POST","category,writer,comment,comment_modify,cidno,type,mode,board_id,read,page,where,keyword,value,veiwDir,article,s_password");
	
	/*
	게시물 설정 정보 로드
	*/
	$mysql->select("
		SELECT *
		FROM toony_module_board_config
		WHERE board_id='$board_id'
	");
	$mysql->fetchArray("skin,name,use_comment,use_list,use_reply,use_file1,use_file2,void_html,file_limit,list_limit,length_limit,array_level,write_level,secret_level,comment_level,delete_level,read_level,reply_level,controll_level,top_file,bottom_file,tc_1,tc_2,tc_3,tc_4,tc_5");
	$c_array = $mysql->array;
	$mysql->htmlspecialchars = 0;
	$mysql->nl2br = 0;
	$mysql->fetchArray("top_source,bottom_source");
	$c_array = $mysql->array;
	
	/*
	검사
	*/
	if(!$board_id){
		$lib->error_alert_back("게시판이 지정되지 않았습니다.","A");
	}
	if($mysql->numRows()<1){
		$lib->error_alert_back("존재하지 않는 게시판입니다.","A");
	}
	
	/*
	상단 파일&소스코드 출력
	*/
	if($c_array['top_file']){
		include $c_array['top_file'];
	}
	echo $c_array['top_source'];
	
	/*
	게시물 기본 정보 로드
	*/
	$mysql->select("
		SELECT *
		FROM toony_module_board_data_$board_id 
		WHERE idno=$read
	");
	$mysql->fetchArray("me_idno,password,ln,rn");
	$array = $mysql->array;
	
	/*
	권한 검사
	*/
	if($member['me_level']<=$c_array['controll_level']){
		$delete_true = 1;	
	}else{
		if($array['me_idno']==0&&!isset($__toony_member_idno)&&$member['me_level']<=$c_array['delete_level']){
			$delete_true = 2;
		}else if($array['me_idno']==$member['me_idno']&&$member['me_level']<=$c_array['delete_level']){
			$delete_true = 1;
		}else{
			$delete_true = 0;	
		}
	}
	
	/*
	패스워드가 submit 된 경우
	*/
	if($value==2){
		if($s_password==$array['password']){
			$delete_true=1;
		}else{
			echo '<!--error:notPassword-->';
			$lib->error_alert_location("비밀번호가 일치하지 않습니다.",__URL_PATH__.$viewDir."?article={$article}","A");
		}
	}
	
	/*
	비밀번호 입력폼 스킨 템플릿 로드
	*/
	$skin_delete_form->skin_file_path("modules/board/skin/{$c_array['skin']}/{$viewDir}delete.html");
	
	/*
	스킨 CSS로드
	*/
	echo "\n<link href=\"".__URL_PATH__."modules/board/skin/{$c_array['skin']}/{$viewDir}style.css\" rel=\"stylesheet\" type=\"text/css\" />";
	
	/*
	템플릿 치환
	*/
	if($delete_true==0){
		$lib->error_alert_back("삭제 권한이 없습니다.","A");
	}
	if($delete_true==2){
		$skin_delete_form->skin_modeling("[board_id_value]",$board_id);
		$skin_delete_form->skin_modeling("[read_value]",$read);
		$skin_delete_form->skin_modeling("[page_value]",$page);
		$skin_delete_form->skin_modeling("[where_value]",$where);
		$skin_delete_form->skin_modeling("[keyword_value]",$keyword);
		$skin_delete_form->skin_modeling("[article_value]",$article);
		$skin_delete_form->skin_modeling("[category_value]",$category);
		echo $skin_delete_form->skin_echo();
	}
	
	/*
	삭제처리
	*/
	if($delete_true==1){
		//최소/최대 ln값 구함
		$ln_min = (int)(ceil($array['ln']/1000)*1000)-1000;
		$ln_max = (int)(ceil($array['ln']/1000)*1000);
		//부모글인 경우 삭제 조건문 만듬
		if($array['rn']==0){
			$delete_where = "ln>$ln_min AND ln<=$ln_max";
		//자식글(답글)인 경우 삭제 조건문 만듬
		}else if($array['rn']>=1){
			//같은 레벨중 바로 아래 답글의 ln값을 불러옴
			$mysql->select("
				SELECT ln 
				FROM toony_module_board_data_$board_id
				WHERE ln>=$ln_min AND ln<{$array['ln']} AND rn={$array['rn']}
				ORDER BY ln DESC
				LIMIT 1
			");
			$earray[ln] = $mysql->fetch("ln");
			if($earray[ln]==""){
				$delete_where = "ln<={$array['ln']} AND ln>$ln_min AND rn>={$array['rn']}";
			}else{
				$delete_where = "ln<={$array['ln']} AND ln>{$earray['ln']} AND rn>={$array['rn']}";
			}
		}
		//첨부파일 삭제
		$fileUploader->savePath = __DIR_PATH__."modules/board/upload/".$board_id."/";
		$mysql->select("
			SELECT *
			FROM toony_module_board_data_$board_id 
			WHERE $delete_where
		");
		do{
			$mysql->fetchArray("file1,file2");
			$farray = $mysql->array;
			if($farray['file1']!=""){
				$fileUploader->fileDelete($farray['file1']);
			}
			if($farray['file2']!=""){
				$fileUploader->fileDelete($farray['file2']);
			}
		}while($mysql->nextRec());
		//댓글 삭제
		do{
			$mysql->fetchArray("idno");
			$farray = $mysql->array;
			$mysql->query("
				DELETE
				FROM toony_module_board_comment_$board_id
				WHERE bo_idno='{$farray['idno']}'
			");
		}while($mysql->nextRec());
		//게시글 DB 삭제
		$mysql->query("
			DELETE
			FROM toony_module_board_data_$board_id
			WHERE $delete_where
		");
		//삭제 후 페이지 이동
		$lib->func_location(__URL_PATH__.$viewDir."?article={$article}&category=".urlencode($category)."&page={$page}&where={$where}&keyword={$keyword}");
	}
	
	/*
	하단 파일&소스코드 출력
	*/
	echo $c_array['bottom_source'];
	if($c_array['bottom_file']){
		include $c_array['bottom_file'];
	}
	
?>