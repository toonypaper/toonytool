<?php
	$mysql = new mysqlConnection();
	$lib = new libraryClass();
	
	/*
	모듈이 설치되어 있는지 검사
	*/
	if(!$mysql->is_table("toony_module_board_config")&&$viewType){
		$lib->error_alert_location("게시판 모듈 추가 설치가 필요합니다.",__URL_PATH__."admin/?m=board&p=install","A");
	}
	
	/*
	최근게시물 출력 관련 함수
	*/
	//댓글 갯수
	function latest_comment_func($comment){
		if($comment>0){
			return number_format($comment);
		}else{
			return "";
		}
	}
	
	/*
	최근게시물 출력
	*/
	function call_board_latest($viewType,$article,$board_id,$line,$length,$ment_length,$skin,$width,$height,$margin,$quard){
		
		if($viewType=="p"){
			$viewDir = "";
			$viewSkinType = "";
		}else{
			$viewDir = "m/";
			$viewSkinType = "_mobile";
		}
		
		$mysql = new mysqlConnection();
		$tpl = new skinController();
		$lib = new libraryClass();
		$tpl = new skinController();
		$header = new skinController();
		$loop = new skinController();
		$footer = new skinController();
		
		$mysql->select("
			SELECT name
			FROM toony_module_board_config
			WHERE board_id='$board_id'
		");
		$mysql->fetchArray("name");
		$array = $mysql->array;
		
		//게시판이 존재하지 않는다면 오류 출력
		if(!$array['name']){
			return "최근게시물에서 설정된 게시판이 존재하지 않습니다.";
		}
		//게시판이 존재한다면 게시물 출력
		$mysql->select("
			SELECT
			(
				SELECT COUNT(*)
				FROM toony_module_board_comment_$board_id
				WHERE bo_idno=A.idno
			) comment,
			A.*
			FROM toony_module_board_data_$board_id A
			WHERE A.use_notice='N' AND rn=0
			ORDER BY A.ln DESC, A.regdate DESC
			LIMIT $line
		");
		
		//최근게시물 템플릿 로드
		$tpl->skin_file_path("modules/board/latestskin/{$skin}/index{$viewSkinType}.html");
		$header->skin_html_load($tpl->skin);
		$header->skin_loop_header("[{loop_start}]");
		$loop->skin_html_load($tpl->skin);
		$loop->skin_loop_array("[{loop_start}]","[{loop_end}]");
		$footer->skin_html_load($tpl->skin);
		$footer->skin_loop_footer("[{loop_end}]");
		
		//header 템플릿 치환
		$header->skin_modeling('[/latestskinDir/]',__URL_PATH__."modules/board/latestskin/".$skin."/");
		$header->skin_modeling('[title]',htmlspecialchars($array['name']));
		$header->skin_modeling('[board_link]',__URL_PATH__.$viewDir.'?article='.$article);
		$tpl = $header->skin_echo();
		
		//loop 템플릿 치환
		if($mysql->numRows()>0){
			do{
				$array['memo'] = strip_tags($mysql->fetch("memo"));
				$mysql->htmlspecialchars = 0;
				$mysql->fetchArray("board_id,idno,subject,ment,regdate,idno,file1,file2,comment,writer");
				$array = $mysql->array;
				$loop->skin_modeling('[/latestskinDir/]',__URL_PATH__."modules/board/latestskin/".$skin."/");
				$loop->skin_modeling('[thumbnail]',call_board_latest_thumbnail_func($viewType,$article,$board_id,$array['idno'],$array['file1'],$array['file2'],$array['ment'],$width,$height,$quard,$margin));
				$loop->skin_modeling('[subject]',$lib->func_length_limit($array['subject'],0,$length));
				$loop->skin_modeling('[ment]',$lib->func_length_limit(strip_tags($array['ment']),0,$ment_length));
				$loop->skin_modeling('[date]',date("Y.m.d",strtotime($array['regdate'])));
				$loop->skin_modeling('[nick]',$array['writer']);
				$loop->skin_modeling('[comment]',latest_comment_func($array['comment']));
				$loop->skin_modeling('[link]',__URL_PATH__.$viewDir.'?article='.$article.'&p=read&read='.$array['idno']);
				$tpl .= $loop->skin_echo();
			}while($mysql->nextRec());
		}
		
		//footer 템플릿 치환
		if($mysql->numRows()<1){
			$footer->skin_modeling_hideArea("[{not_loop_start}]","[{not_loop_end}]","show");
		}else{
			$footer->skin_modeling_hideArea("[{not_loop_start}]","[{not_loop_end}]","hide");
		}
		$footer->skin_modeling('[/latestskinDir/]',__URL_PATH__."modules/board/latestskin/".$skin."/");
		$footer->skin_modeling('[title]',htmlspecialchars($array['name']));
		$footer->skin_modeling('[board_link]',__URL_PATH__.$viewDir.'?article='.$article);
		$tpl .= $footer->skin_echo();
		return $tpl;
	}
	//최근게시물 썸네일 추출
	function call_board_latest_thumbnail_func($viewType,$article,$board_id,$idno,$file1,$file2,$ment,$width,$height,$quard,$margin){
		
		if($viewType=="p"){
			$viewDir = "";
		}else{
			$viewDir = "m/";
		}
		$lib = new libraryClass();
		
		//본문내 첫번째 이미지 태그를 추출
		preg_match("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $ment, $match);
		if(strtolower(array_pop(explode(".",$file1)))=='gif'||strtolower(array_pop(explode(".",$file1)))=='jpg'||strtolower(array_pop(explode(".",$file1)))=='bmp'||strtolower(array_pop(explode(".",$file1)))=='png'){
			$thumb = $lib->func_img_resize("modules/board/upload/".$board_id."/",$file1,$width,$height,$margin,$quard);
		}else if(strtolower(array_pop(explode(".",$file2)))=='gif'||strtolower(array_pop(explode(".",$file2)))=='jpg'||strtolower(array_pop(explode(".",$file2)))=='bmp'||strtolower(array_pop(explode(".",$file2)))=='png'){
			$thumb = $lib->func_img_resize("modules/board/upload/".$board_id."/",$file2,$width,$height,$margin,$quard);
		}else if(isset($match[0])){
			$thumb = "<img src=\"{$match[1]}\" width=\"".$width."\" height=\"".$height."\" />";
		}else{
			$thumb = $lib->func_img_resize("images/","blank_thumbnail.jpg",$width,$height,$margin,$quard);
		}
		return $thumb;
	}
?>