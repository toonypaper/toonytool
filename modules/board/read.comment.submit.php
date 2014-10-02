<?php
	include "../../include/engine.inc.php";
	include __DIR_PATH__."include/global.php";
	
	$lib = new libraryClass();
	$mysql = new mysqlConnection();
	$method = new methodController();
	
	$lib->security_filter("referer");
	$lib->security_filter("request_get");
	$method->method_param("POST","writer,comment,comment_modify,cidno,type,mode,board_id,read,page,where,keyword,tr_1,tr_2,tr_3,tr_4,tr_5");
	
	/*
	게시물 설정 정보 로드
	*/
	$mysql->select("
		SELECT *
		FROM toony_module_board_config
		WHERE board_id='$board_id'
	");
	$mysql->fetchArray("viewType,title,use_comment,use_list,use_reply,use_file1,use_file2,void_html,file_limit,list_limit,length_limit,array_level,write_level,secret_level,comment_level,delete_level,read_level,reply_level,controll_level,tc_1,tc_2,tc_3,tc_4,tc_5");
	$c_array = $mysql->array;
	
	/*
	검사
	*/
	mb_internal_encoding('UTF-8');
	if($c_array['use_comment']=="N"){ echo '댓글 기능이 비활성 중입니다.'; exit; }
	if($member['me_level']>$c_array['comment_level']){ echo '권한이 없습니다.'; exit; }
	if($mode==1||$mode==11||$mode==2){
		if(trim($comment)==""&&trim($comment_modify)==""){ echo '댓글을 입력하세요.'; exit; }
		if(mb_strlen($comment)<5&&mb_strlen($comment_modify)<5){ echo '댓글은 5자 이상 입력해야 합니다.'; exit; }	
	}
	
	/*
	댓글 등록
	*/
	if($mode==1){
		//검사
		if($type==1){
			$me_idno = "";
			if(trim($writer)==""){ echo "작성자 이름을 입력하세요."; exit; }
		}else if($type==2){
			$me_idno = $member['me_idno'];
			$writer = $member['me_nick'];
		}else{
			echo '오류. 댓글 등록 불가'; exit;
		}
		//ln값 처리
		$mysql->select("
			SELECT MAX(ln)+1000 AS ln_max
			FROM toony_module_board_comment_$board_id
			WHERE bo_idno='$read'
		");
		$ln_array['ln_max'] = $mysql->fetch("ln_max");
		if(!$ln_array['ln_max']) $ln_array['ln_max']=1000;
		$ln_array['ln_max'] = ceil($ln_array['ln_max']/1000)*1000;
		//DB 기록
		$mysql->query("
			INSERT INTO toony_module_board_comment_$board_id
			(ln,bo_idno,me_idno,writer,comment,ip,regdate,tr_1,tr_2,tr_3,tr_4,tr_5) 
			VALUES
			('{$ln_array['ln_max']}','$read','$me_idno','$writer','$comment','{$_SERVER['REMOTE_ADDR']}',now(),'$tr_1','$tr_2','$tr_3','$tr_4','$tr_5')
		");
		echo '<!--success::1-->';
	}
	/*
	대댓글 등록
	*/
	if($mode==11){
		//검사
		if($type==1){
			$me_idno = "";
			if(trim($writer)==""){ echo '작성자 이름을 입력하세요.'; exit; }
		}else if($type==2){
			$me_idno = $member['me_idno'];
			$writer = $member['me_nick'];
		}else{
			echo '오류. 댓글 등록 불가'; exit;
		}
		//원본 글에서 ln,rn,bo_idno값을 가져옴 가져옴
		$mysql->select("
			SELECT ln,rn,bo_idno
			FROM toony_module_board_comment_$board_id
			WHERE idno=$cidno
		");
		$bo_idno = (int)$mysql->fetch("bo_idno");
		//rn 값 처리
		$rn = (int)$mysql->fetch("rn");
		$rn_next = (int)$mysql->fetch("rn")+1;
		//ln값 처리
		$ln = (int)$mysql->fetch("ln");
		$ln_min = (int)(ceil($ln/1000)*1000);
		$ln_next = (int)$ln_min+1000;
		//같은 레벨중 바로 아래 답글의 ln값을 불러옴
		$mysql->select("
			SELECT
			IF(MIN(ln)>'$ln',MIN(ln),'$ln_next') ln
			FROM toony_module_board_comment_$board_id
			WHERE ln<$ln_next AND ln>$ln AND rn=$rn AND bo_idno='$bo_idno'
		");
		$ln_target = $mysql->fetch("ln");
		//댓글의 ln값 부여, 다른 게시물의 ln 정렬
		$mysql->select("
			SELECT IF(MAX(ln)>'$ln',MAX(ln),'$ln') ln
			FROM toony_module_board_comment_$board_id
			WHERE bo_idno=$bo_idno AND ln>$ln AND ln<$ln_target AND rn>$rn
		");
		$ln_insert = $mysql->fetch("ln")+1;
		$mysql->query("
			UPDATE toony_module_board_comment_$board_id
			SET ln=ln+1
			WHERE ln<$ln_next AND ln>=$ln_insert AND rn>0
		");
		//DB 기록
		$mysql->query("
			INSERT INTO toony_module_board_comment_$board_id
			(ln,rn,bo_idno,me_idno,writer,comment,ip,regdate,tr_1,tr_2,tr_3,tr_4,tr_5) 
			VALUES
			('$ln_insert','$rn_next','$read','$me_idno','$writer','$comment','$_SERVER[REMOTE_ADDR]',now(),'$tr_1','$tr_2','$tr_3','$tr_4','$tr_5')
		");
		echo '<!--success::1-->';
	}
	
	/*
	댓글 수정
	*/
	if($mode==2){
		//검사
		$mysql->select("
			SELECT *
			FROM toony_module_board_comment_$board_id
			WHERE idno=$cidno
		");
		$carray['me_idno'] = $mysql->fetch("me_idno");
		if($mysql->numRows()<1){
			echo '존재하지 않는 댓글입니다.'; exit;
		}
		if($carray['me_idno']!=$member['me_idno']&&$member['me_level']>$c_array['controll_level']&&$member['me_admin']!="Y"){
			echo '자신의 댓글이 아닙니다.'; exit;
		}
		//DB 수정
		$mysql->select("
			SELECT *
			FROM toony_module_board_comment_$board_id
			WHERE idno=$cidno
		");
		$wquery['writer'] = $mysql->fetch("writer");
		$wquery['me_idno'] = $mysql->fetch("me_idno");
		if($type==2){
			if($wquery['me_idno']==$__toony_member_idno){
				$writer = $member['me_nick'];
			}else{
				$writer = $wquery['writer'];
			}
		}else if($type==1){
			$writer = $wquery['writer'];
		}
		$mysql->query("
			UPDATE toony_module_board_comment_$board_id
			SET writer='$writer',comment='$comment_modify',ip='$_SERVER[REMOTE_ADDR]',tr_1='$tr_1',tr_2='$tr_2',tr_3='$tr_3',tr_4='$tr_4',tr_5='$tr_5'
			WHERE idno=$cidno
		");
		echo '<!--success::1-->';
	}
	
	/*
	댓글 삭제
	*/
	if($mode==3){
		//검사
		$mysql->select("
			SELECT *
			FROM toony_module_board_comment_$board_id
			WHERE idno=$cidno
		");
		$array['me_idno'] = $mysql->fetch("me_idno");
		$array['ln'] = $mysql->fetch("ln");
		$array['rn'] = $mysql->fetch("rn");
		if($mysql->numRows()<1){
			echo '존재하지 않는 댓글입니다.'; exit;
		}
		if($array['me_idno']!=$member['me_idno']&&$member['me_level']>$c_array['controll_level']&&$member['me_admin']!="Y"){
			echo '자신의 댓글이 아닙니다.'; exit;
		}
		//하위 자식 댓글이 있는경우 삭제 금지
		$ln_min = (int)(ceil($array['ln']/1000)*1000);
		$ln_max = (int)(ceil($array['ln']/1000)*1000)+1000;
			//부모글인 경우 색인 조건문 만듬
			if($array['rn']==0){
				$search_where = "ln<$ln_max AND ln>=$ln_min AND  bo_idno='$read'";
			//자식글(답글)인 경우 색인 조건문 만듬
			}else if($array['rn']>=1){
				//같은 레벨중 바로 아래 답글의 ln값을 불러옴
				$mysql->select("
					SELECT ln 
					FROM toony_module_board_comment_$board_id
					WHERE ln<$ln_max AND ln>{$array['ln']} AND rn={$array['rn']} AND bo_idno='$read'
					ORDER BY ln ASC
					LIMIT 1
				");
				$earray['ln'] = $mysql->fetch("ln");
				if($earray['ln']==""){
					$search_where = "ln<$ln_max AND ln>={$array['ln']} AND rn>={$array['rn']} AND bo_idno='$read'";
				}else{
					$search_where = "ln<{$earray['ln']} AND ln>={$array['ln']} AND rn>={$array['rn']} AND bo_idno='$read'";
				}
			}
			//자식글이 있는지 검사
			$mysql->select("
				SELECT *
				FROM toony_module_board_comment_$board_id
				WHERE $search_where
			");
			if($mysql->numRows()>1){
				echo '자식 글이 있는 경우 삭제가 불가능 합니다.'; exit;
			}
			
		//DB 삭제
		$mysql->query("
			DELETE
			FROM toony_module_board_comment_$board_id
			WHERE idno=$cidno
		");
		echo '<!--success::1-->';
	}
	
?>