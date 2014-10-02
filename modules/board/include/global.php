<?php
	/*
	모듈이 설치되어 있는지 검사
	*/
	if(!$mysql->is_table("toony_module_board_config")){
		$lib->error_alert_location("게시판 모듈이 설치되지 않았습니다.",__URL_PATH__."admin/?m=board&p=install","A");
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
	function call_board_latest($viewType,$article,$board_id,$line,$length,$ment_length,$view,$width,$height,$margin,$quard){
		//뷰타입 변수 처리
		if($viewType=="p"){
			$viewDir = "";
		}else{
			$viewDir = "m/";
		}
		
		$mysql = new mysqlConnection();
		$tpl = new skinController();
		$lib = new libraryClass();
		$mysql->select("
			SELECT name
			FROM toony_module_board_config
			WHERE board_id='$board_id'
		");
		$mysql->fetchArray("name");
		$array = $mysql->array;
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
			ORDER BY A.idno DESC
			LIMIT $line
		");
		switch($view){
			case "default" :
				$viewFile = "latest.html";
				break;
			case "webzine" :
				$viewFile = "latest_webzine.html";
				break;
			case "gallery" :
				$viewFile = "latest_gallery.html";
				break;
			default :
				$viewFile = "latest.html";
		}
		//header
		$header = new skinController();
		$header->skin_file_path("modules/board/_tpl/outModules/{$viewFile}");
		$header->skin_loop_header("[{loop_start}]");
		$header->skin_modeling('[title]','<a href="'.__URL_PATH__.$viewDir.'?article='.$article.'">'.htmlspecialchars($array['name']).'</a>');
		$header->skin_modeling('[more]','<a href="'.__URL_PATH__.$viewDir.'?article='.$article.'" class="more">더보기</a>');
		$tpl = $header->skin_echo();
		//array
		$loop = new skinController();
		$loop->skin_file_path("modules/board/_tpl/outModules/{$viewFile}");
		$loop->skin_loop_array("[{loop_start}]","[{loop_end}]");
		if($mysql->numRows()>0){
			do{
				$array['memo'] = strip_tags($mysql->fetch("memo"));
				$mysql->htmlspecialchars = 0;
				$mysql->fetchArray("board_id,idno,subject,ment,regdate,idno,file1,file2,comment,writer");
				$array = $mysql->array;
				$loop->skin_modeling('[thumbnail]',call_board_latest_thumbnail_func($viewType,$article,$board_id,$array['idno'],$array['file1'],$array['file2'],$array['ment'],$width,$height,$quard,$margin));
				$loop->skin_modeling('[subject]','<a href="'.__URL_PATH__.$viewDir.'?article='.$article.'&p=read&read='.$array['idno'].'" class="sbj">'.$lib->func_length_limit($array['subject'],0,$length).'</a>');
				$loop->skin_modeling('[ment]','<a href="'.__URL_PATH__.$viewDir.'?article='.$article.'&p=read&read='.$array['idno'].'" class="ment">'.$lib->func_length_limit(strip_tags($array['ment']),0,$ment_length).'</a>');
				$loop->skin_modeling('[regdate]',date("Y.m.d",strtotime($array['regdate'])));
				$loop->skin_modeling('[name]',$array['writer']);
				$loop->skin_modeling('[comment]',latest_comment_func($array['comment']));
				$tpl .= $loop->skin_echo();
			}while($mysql->nextRec());
		}
		//footer
		$footer = new skinController();
		$footer->skin_file_path("modules/board/_tpl/outModules/{$viewFile}");
		$footer->skin_loop_footer("[{loop_end}]");
		if($mysql->numRows()<1){
			$footer->skin_modeling_hideArea("[{not_loop_start}]","[{not_loop_end}]","show");
		}else{
			$footer->skin_modeling_hideArea("[{not_loop_start}]","[{not_loop_end}]","hide");
		}
		$tpl .= $footer->skin_echo();
		return $tpl;
	}
	//최근게시물 썸네일 추출
	function call_board_latest_thumbnail_func($viewType,$article,$board_id,$idno,$file1,$file2,$ment,$width,$height,$quard,$margin){
		//뷰타입 변수 처리
		if($viewType=="p"){
			$viewDir = "";
		}else{
			$viewDir = "m/";
		}
		$lib = new libraryClass();
		//본문내 첫번째 이미지 태그를 추출
		preg_match("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $ment, $match);
		
		if(strtolower(array_pop(explode(".",$file1)))=='gif'||strtolower(array_pop(explode(".",$file1)))=='jpg'||strtolower(array_pop(explode(".",$file1)))=='bmp'||strtolower(array_pop(explode(".",$file1)))=='png'){
			$thumb = "<a href=\"".__URL_PATH__.$viewDir."?article={$article}&p=read&read=".$idno."\" class=\"thumb\">".$lib->func_img_resize("modules/board/upload/".$board_id."/",$file1,$width,$height,$margin,$quard)."</a>";
		}else if(strtolower(array_pop(explode(".",$file2)))=='gif'||strtolower(array_pop(explode(".",$file2)))=='jpg'||strtolower(array_pop(explode(".",$file2)))=='bmp'||strtolower(array_pop(explode(".",$file2)))=='png'){
			echo "asdf";$thumb = "<a href=\"".__URL_PATH__.$viewDir."?article={$article}&p=read&read=".$idno."\" class=\"thumb\">".$lib->func_img_resize("modules/board/upload/".$board_id."/",$file2,$width,$height,$margin,$quard)."</a>";
		}else if($match['0']){
			$thumb = "<a href=\"".__URL_PATH__.$viewDir."?article={$article}&p=read&read=".$idno."\" class=\"thumb\"><img src=\"{$match['1']}\" width=\"".$width."\" height=\"".$height."\" /></a>";
		}else{
			$thumb = "
				<a href='".__URL_PATH__.$viewDir."?article={$article}&p=read&read=".$idno."' class='thumb'>".$lib->func_img_resize("images/","blank_thumbnail.jpg",$width,$height,$margin,$quard)."</a>
			";
		}
		return $thumb;
	}
?>