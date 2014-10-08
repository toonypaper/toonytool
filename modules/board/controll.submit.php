<?php
	include "../../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$tar_mysql = new mysqlConnection();
	$c_tar_mysql = new mysqlConnection();
	$method = new methodController();
	$fileUploader = new fileUploader();
	$skin_delete_form = new skinController();
	$fileUploader = new fileUploader();
	
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	$method->method_param("POST","board_id,article,where,keyword,page,category,cnum,type,tar_board_id");
	
	/*
	검사
	*/
	$mysql->select("
		SELECT *
		FROM toony_module_board_config 
		WHERE board_id='$board_id'
	");
	$c_array['controll_level'] = $mysql->fetch("controll_level");
	if($member['me_level']>$c_array['controll_level']){
		echo "글을 관리할 권한이 없습니다."; exit;
	}
	
	/*
	선택한 게시물을 쪼갠 후 배열 순서를 재배치
	*/
	$cnum_ex = explode(",",$cnum);
	$cnum_ex = array_reverse($cnum_ex);
	
	/*
	파일 중복 저장 방지를 위한 현재 시간 변수 생성
	*/
	$nowDate = date("ymdhis",mktime());
	
	/*
	삭제인 경우
	*/
	if($type=="delete"){
		for($i=0;$i<sizeof($cnum_ex);$i++){
			if($cnum_ex[$i]!=""){
				$mysql->select("
					SELECT *
					FROM toony_module_board_data_$board_id
					WHERE idno=$cnum_ex[$i]
				");
				$mysql->fetchArray("ln,rn,ment");
				$barray = $mysql->array;
				//최소/최대 ln값 구함
				$ln_min = (int)(ceil($barray['ln']/1000)*1000)-1000;
				$ln_max = (int)(ceil($barray['ln']/1000)*1000);
				//부모글인 경우 삭제 조건문 만듬
				if($barray['rn']==0){
					$delete_where = "ln>$ln_min AND ln<=$ln_max";
				//자식글(답글)인 경우 삭제 조건문 만듬
				}else if($barray['rn']>=1){
					//같은 레벨중 바로 아래 답글의 ln값을 불러옴
					$mysql->select("
						SELECT ln 
						FROM toony_module_board_data_$board_id
						WHERE ln>=$ln_min AND ln<{$array['ln']} AND rn={$array['rn']}
						ORDER BY ln DESC
						LIMIT 1
					");
					$earray['ln'] = $mysql->fetch("ln");
					if($earray['ln']==""){
						$delete_where = "ln<={$barray['ln']} AND ln>$ln_min AND rn>={$barray['rn']}";
					}else{
						$delete_where = "ln<={$barray['ln']} AND ln>{$earray['ln']} AND rn>={$barray['rn']}";
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
				//내용에 삽입된 스마트에디터 사진 삭제
				$fileUploader->sEditor_fileDelete($barray['ment']);
			}
		}
		echo '<!--success::1-->';
	}
	
	/*
	복사인 경우
	*/
	if($type=="copy"){
		for($i=0;$i<sizeof($cnum_ex);$i++){
			//원본글의 정보를 불러옴
			$mysql->select("
				SELECT *
				FROM toony_module_board_data_$board_id
				WHERE idno=$cnum_ex[$i]
			");
			$mysql->htmlspecialchars = 0;
			$mysql->nl2br = 0;
			$mysql->fetchArray("idno,category,ln,rn,me_idno,writer,password,email,ment,subject,file1,file2,link1,link2,use_secret,use_notice,use_html,use_email,ip,regdate,td_1,td_2,td_3,td_4,td_5");
			$array = $mysql->array;
			//가져온 원본들의 내용을 addslashes 시킴
			foreach($array as $key=>$value){
				$array[$key] = addslashes($array[$key]);
			}
			//rn이 0인 부모글인 경우만 복사 실행
			if($array['rn']==0){
				//대상 게시판의 최대 ln값 불러옴
				$mysql->select("
					SELECT MAX(ln)+1000 AS ln_max
					FROM toony_module_board_data_$tar_board_id
					WHERE 1
					ORDER BY ln DESC
					LIMIT 1
				");
				$tar_ln = $mysql->fetch("ln_max");
				if(!$tar_ln) $tar_ln=1000;
				$tar_ln = ceil($tar_ln/1000)*1000;
				//대상 게시판으로 첨부파일을 복사
				$oldPath = __DIR_PATH__."modules/board/upload/".$board_id."/";
				$tarPath = __DIR_PATH__."modules/board/upload/".$tar_board_id."/";
				$fileUploader->savePath = $tarPath;
					$fileUploader->filePathCheck();
					$fileUploader->savePath = "";
				if($array['file1']!=""){
					$fileUploader->fileCopy($oldPath.$array['file1'],$tarPath.$nowDate."_".$array['file1']);
					$file1Name = $nowDate."_".$array['file1'];
				}else{
					$file1Name = "";
				}
				if($array['file2']!=""){
					$fileUploader->fileCopy($oldPath.$array['file2'],$tarPath.$nowDate."_".$array['file2']);
					$file2Name = $nowDate."_".$array['file2'];
				}else{
					$file2Name = "";
				}
				//대상 게시판으로 글을 복사
				$mysql->query("
					INSERT INTO
					toony_module_board_data_$tar_board_id
					(category,ln,rn,me_idno,writer,password,email,ment,subject,file1,file2,link1,link2,use_secret,use_html,use_email,ip,regdate,td_1,td_2,td_3,td_4,td_5)
					VALUES
					('{$array['category']}','$tar_ln','0','{$array['me_idno']}','{$array['writer']}','{$array['password']}','{$array['email']}','{$array['ment']}','{$array['subject']}','$file1Name','$file2Name','{$array['link1']}','{$array['link2']}','{$array['use_secret']}','{$array['use_html']}','{$array['use_email']}','{$array['ip']}',now(),'{$array['td_1']}','{$array['td_2']}','{$array['td_3']}','{$array['td_4']}','{$array['td_5']}')
				");
				
			}
		}
		echo '<!--success::2-->';
	}
	
	/*
	이동인 경우
	*/
	if($type=="move"){
		
		/*
		글 이동
		*/
		for($i=0;$i<sizeof($cnum_ex);$i++){
			//원본 게시물을 로드
			$mysql->select("
				SELECT *
				FROM toony_module_board_data_$board_id
				WHERE idno='$cnum_ex[$i]'
			");
			$ln[$i] = $mysql->fetch("ln");
			$rn[$i] = $mysql->fetch("rn");
		}
		for($i=0;$i<sizeof($cnum_ex);$i++){
			//rn이 0인 부모글인 경우만 이동 실행
			if($rn[$i]==0){
				//글의 최소/최대 ln값 구함
				$ln_min = (int)(ceil($ln[$i]/1000)*1000)-1000;
				$ln_max = (int)(ceil($ln[$i]/1000)*1000);
				//글의 자식들의 범위를 구함
				$where = "ln>$ln_min AND ln<=$ln_max";
				$mysql->select("
					SELECT *
					FROM toony_module_board_data_$board_id 
					WHERE $where
				");
				//대상 게시판의 최대 ln값 불러옴
				$tar_mysql->select("
					SELECT MAX(ln)+1000 AS ln_max
					FROM toony_module_board_data_$tar_board_id
					WHERE 1
					ORDER BY ln DESC
					LIMIT 1
				");
				$tar_ln = $tar_mysql->fetch("ln_max");
				if(!$tar_ln) $tar_ln=1000;
				$tar_ln = ceil($tar_ln/1000)*1000;
				
				do{
					$mysql->htmlspecialchars = 0;
					$mysql->nl2br = 0;
					$mysql->fetchArray("idno,category,ln,rn,me_idno,writer,password,email,ment,subject,file1,file1_cnt,file2,file2_cnt,link1,link2,use_secret,use_notice,use_html,use_email,view,ip,regdate,td_1,td_2,td_3,td_4,td_5");
					$array = $mysql->array;
					//가져온 원본들의 내용을 addslashes 시킴
					foreach($array as $key=>$value){
						$array[$key] = addslashes($array[$key]);
					}
					//대상 게시판으로 첨부파일을 복사
					$oldPath = __DIR_PATH__."modules/board/upload/".$board_id."/";
					$tarPath = __DIR_PATH__."modules/board/upload/".$tar_board_id."/";
					$fileUploader->savePath = $tarPath;
					$fileUploader->filePathCheck();
					$fileUploader->savePath = "";
					if($array['file1']!=""){
						$fileUploader->fileCopy($oldPath.$array['file1'],$tarPath.$nowDate."_".$array['file1']);
						$fileUploader->fileDelete($oldPath.$array['file1']);
						$file1Name = $nowDate."_".$array['file1'];
					}else{
						$file1Name = "";
					}
					if($array['file2']!=""){
						$fileUploader->fileCopy($oldPath.$array['file2'],$tarPath.$nowDate."_".$array['file2']);
						$fileUploader->fileDelete($oldPath.$array['file2']);
						$file2Name = $nowDate."_".$array['file2'];
					}else{
						$file2Name = "";
					}
					//대상 게시판으로 글을 복사
					$tar_mysql->query("
						INSERT INTO
						toony_module_board_data_$tar_board_id
						(category,ln,rn,me_idno,writer,password,email,ment,subject,file1,file1_cnt,file2,file2_cnt,link1,link2,use_secret,use_html,use_email,view,ip,regdate,td_1,td_2,td_3,td_4,td_5)
						VALUES
						('{$array['category']}','$tar_ln','{$array['rn']}','{$array['me_idno']}','{$array['writer']}','{$array['password']}','{$array['email']}','{$array['ment']}','{$array['subject']}','$file1Name','{$array['file1_cnt']}','$file2Name','{$array['file2_cnt']}','{$array['link1']}','{$array['link2']}','{$array['use_secret']}','{$array['use_html']}','{$array['use_email']}','{$array['view']}','{$array['ip']}',now(),'{$array['td_1']}','{$array['td_2']}','{$array['td_3']}','{$array['td_4']}','{$array['td_5']}')
					");
					//이동된 글의 idno값을 다시 불러옴
					$tar_mysql->select("
						SELECT idno
						FROM toony_module_board_data_$tar_board_id
						WHERE ln='$tar_ln'
					");
					$tar_read_idno = $tar_mysql->fetch("idno");
					//좋아요 이동
					$tar_mysql->query("
						UPDATE
						toony_module_board_like
						SET
						board_id='$tar_board_id',read_idno='$tar_read_idno'
						WHERE board_id='$board_id' AND read_idno='{$array['idno']}'
					");
					//댓글 복사를 위한 대상 댓글 테이블의 ln값 구함
					$tar_mysql->select("
						SELECT MAX(ln)+1000 AS ln_max
						FROM toony_module_board_comment_$tar_board_id
						WHERE 1
						ORDER BY ln DESC
						LIMIT 1
					");
					$c_tar_ln = $tar_mysql->fetch("ln_max");
					if(!$c_tar_ln) $c_tar_ln=1000;
					$c_tar_ln = ceil($c_tar_ln/1000)*1000;
					//댓글 복사를 위한 원본 댓글 테이블의 댓글 추출
					$tar_mysql->select("
						SELECT *
						FROM toony_module_board_comment_$board_id
						WHERE bo_idno='{$array['idno']}'
					");
					if($tar_mysql->numRows()>0){
						do{
							$tar_mysql->htmlspecialchars = 0;
							$tar_mysql->nl2br = 0;
							$tar_mysql->fetchArray("ln,rn,bo_idno,me_idno,writer,comment,ip,regdate,tr_1,tr_2,tr_3,tr_4,tr_5");
							$c_array = $tar_mysql->array;
							//가져온 원본들의 내용을 addslashes 시킴
							foreach($c_array as $key=>$value){
								$c_array[$key] = addslashes($c_array[$key]);
							}
							$c_tar_mysql->query("
								INSERT INTO
								toony_module_board_comment_$tar_board_id
								(ln,rn,bo_idno,me_idno,writer,comment,ip,regdate,tr_1,tr_2,tr_3,tr_4,tr_5)
								VALUES
								('{$c_array['ln']}','{$c_array['rn']}','$tar_read_idno','{$c_array['me_idno']}','{$c_array['writer']}','{$c_array['comment']}','{$c_array['ip']}','{$c_array['regdate']}','{$c_array['tr_1']}','{$c_array['tr_2']}','{$c_array['tr_3']}','{$c_array['tr_4']}','{$c_array['tr_5']}')
							");
						}while($tar_mysql->nextRec());
					}
					
					//기존 댓글 삭제
					$tar_mysql->query("
						DELETE
						FROM toony_module_board_comment_$board_id
						WHERE bo_idno='{$array['idno']}'
					");
					
					//원본글 삭제
					$tar_mysql->query("
						DELETE
						FROM toony_module_board_data_$board_id
						WHERE idno='{$array['idno']}'
					");
					$tar_ln--;
				}while($mysql->nextRec());
				
			}
		}
		echo '<!--success::3-->';
	}
	
?>