<?php 
	/*
	스킨 컨트롤러
	*/
	class skinController{
		var $skin_file_path = "";
		var $skin = "";
		var $skin_re = "";
		var $loop_area = "";
		//스킨 파일을 로드하여 HTML소스코드를 변수에 담음
		function skin_file_path($obj){
			if($obj!=""){
				$this->skin_file_path = $obj;
				$this->skin_file_load();
			}
		}
		function skin_file_load(){
			global $member,$site_config,$member_type_var,$viewDir;
			ob_start();
			include __DIR_PATH__.$this->skin_file_path;
			$this->skin = ob_get_contents();
			$this->skin_org = ob_get_contents();
			ob_end_clean();
		}
		//스킨 파일을 로드하지 않고 직접 스킨 HTML소스코드를 변수에 담음
		function skin_html_load($html){
			$this->skin = $html;
			$this->skin_org = $html;
		}
		//스킨 파일 치환
		function skin_modeling($s_str,$r_str){
			if($this->loop_area!=""){
				if($this->skin_re==""){
					$this->skin = str_replace($s_str,$r_str,$this->loop_area);
					$this->skin_re = 1;
				}else{
					$this->skin = str_replace($s_str,$r_str,$this->skin);
				}
			}else{
				$this->skin = str_replace($s_str,$r_str,$this->skin);
			}
		}
		//헤더영역 지정
		function skin_loop_header($start_str){
			$this->start_str_pos = strpos($this->skin,$start_str);
			$this->area = mb_substr($this->skin,0,$this->start_str_pos);
			$this->skin = $this->area;
		}
		//중간영역 지정
		function skin_loop_middle($start_str,$end_str){
			$this->str_length = strlen($end_str);
			$this->start_str_length = strlen($start_str);
			$this->start_str_length = strlen($start_str);
			$this->start_str_pos = strpos($this->skin,$start_str);
			$this->end_str_pos = strpos($this->skin,$end_str);
			$this->area = mb_substr($this->skin,$this->start_str_pos+$this->start_str_length,$this->end_str_pos-$this->start_str_pos-$this->start_str_length);
			$this->skin = $this->area;
		}
		//하단영역 지정
		function skin_loop_footer($end_str){
			$this->str_length = strlen($end_str);
			$this->end_str_pos = strpos($this->skin,$end_str);
			$this->area = mb_substr($this->skin,$this->end_str_pos+$this->str_length);
			$this->skin = $this->area;
		}
		//반복영역 지정
		function skin_loop_array($start_str,$end_str){
			$this->str_length = strlen($end_str);
			$this->start_str_pos = strpos($this->skin,$start_str);
			$this->end_str_pos = strpos($this->skin,$end_str);
			$this->loop_area = mb_substr($this->skin,$this->start_str_pos,$this->end_str_pos-$this->start_str_pos+$this->str_length);
			$this->loop_area = str_replace($start_str,"",$this->loop_area);
			$this->loop_area = str_replace($end_str,"",$this->loop_area);
			$this->skin = $this->loop_area;
		}
		//특정 영역 보이기&감추기
		function skin_modeling_hideArea($start_str,$end_str,$type){
			if($this->loop_area!=""){
				if($this->skin_re==""){
					$this->loopSkin = $this->loop_area;
					$this->skin = $this->loop_area;
					$this->skin_re = 1;
				}else{
					$this->loopSkin = $this->skin;
				}
			}else{
				$this->loopSkin = $this->skin;
			}
			$this->str_length = strlen($start_str);
			$this->end_str_length = strlen($end_str);
			$this->start_str_pos = strpos($this->skin,$start_str);
			$this->end_str_pos = strpos($this->skin,$end_str);
			//시작/종료 문자열이 없는 경우 작동 중지
			if(!$this->start_str_pos||!$this->end_str_pos){
				return;
			}
			//치환
			$hide_area = mb_substr($this->loopSkin,$this->start_str_pos,($this->end_str_pos-$this->start_str_pos)+$this->end_str_length);
			$show_area = mb_substr($this->loopSkin,$this->start_str_pos+$this->str_length,$this->end_str_pos-$this->start_str_pos-$this->str_length);
			switch($type){
				case "hide" :
					$this->skin = str_replace($hide_area,"",$this->loopSkin);
					break;
				case "show" :
					$this->skin = str_replace($hide_area,$show_area,$this->loopSkin);
					break;
			}
		}
		//스킨 출력
		function skin_echo(){
			if($this->skin_re==1){
				$this->skin_re = "";
				return $this->skin;
			}
			return $this->skin;
		}
	}
	/*
	메소드 컨트롤러
	*/
	class methodController{
		function method_param($type,$name){
			if($type=="GET"){
				global $_GET;
				$expl = explode(",",$name);
				if(sizeof($expl)>0){
					for($i=0;$i<sizeof($expl);$i++){
						global $$expl[$i];
						if(isset($_GET[$expl[$i]])&&$$expl[$i]!=""){
							if(!is_array($_GET[$expl[$i]])){
								$$expl[$i] = addslashes($_GET[$expl[$i]]);
							}else{
								$$expl[$i] = $_GET[$expl[$i]];
							}
						}else{
							$$expl[$i] = NULL;
						}
					}
				}
			}else if($type=="POST"){
				global $_POST;
				$expl = explode(",",$name);
				if(sizeof($expl)>0){
					for($i=0;$i<sizeof($expl);$i++){
						global $$expl[$i];
						if(isset($_POST[$expl[$i]])){
							if(!is_array($_POST[$expl[$i]])){
								$$expl[$i] = addslashes($_POST[$expl[$i]]);
							}else{
								$$expl[$i] = $_POST[$expl[$i]];
							}
						}else{
							$$expl[$i] = NULL;
						}
					}
				}
			}else if($type=="FILE"){
				global $_FILES;
				$expl = explode(",",$name);
				if(sizeof($expl)>0){
					for($i=0;$i<sizeof($expl);$i++){
						if(isset($_FILES[$expl[$i]])){
							global $$expl[$i];
							$$expl[$i] = $_FILES[$expl[$i]];
						}else{
							global $$expl[$i];
							$$expl[$i] = NULL;
						}
					}
				}
			}
		}
	}
	/*
	세션 컨트롤러
	*/
	class sessionController{
		function session_register($name,$var){
			global $_SESSION;
			$_SESSION[$name] = $var;
		}
		function session_deleter($name){
			global $_SESSION;
			$_SESSION[$name] = "";
			@session_unregister($name);
		}
		function session_destroy(){
			global $_SESSION;
			session_destroy();
		}
		function session_selector($name){
			global $_SESSION;
			if(isset($_SESSION[$name])){
				return $_SESSION[$name];
			}else{
				return NULL;
			}
		}
	}
?>