<?php 
	/*
	스킨 컨트롤러
	*/
	abstract class skinController_abstract{
		
		abstract function skin_file_load();
		abstract function skin_echo();
		//시작,끝 문자열의 길이 및 위치 확인
		protected function string_length($s_str,$e_str){
			if($s_str){
				$this->s_str_length = strlen($s_str);
				$this->s_str_pos = strpos($this->skin,$s_str);
			}
			if($e_str){
				$this->e_str_length = strlen($e_str);
				$this->e_str_pos = strpos($this->skin,$e_str);
			}
		}
		//시작,끝 문자열로 범위 지정
		protected function string_area($type,$s_str,$e_str){
			switch($type){
				case "header" :
					return mb_substr($this->skin,0,$this->s_str_pos);
					break;
				case "middle" :
					return mb_substr($this->skin,$this->s_str_pos+$this->s_str_length,$this->e_str_pos-$this->s_str_pos-$this->s_str_length);
					break;
				case "footer" :
					return mb_substr($this->skin,$this->e_str_pos+$this->e_str_length);
					break;
				case "array" :
					$this->loop_area = mb_substr($this->skin,$this->s_str_pos,$this->e_str_pos-$this->s_str_pos+$this->e_str_length);
					$this->loop_area = str_replace($s_str,"",$this->loop_area);
					$this->loop_area = str_replace($e_str,"",$this->loop_area);
					return $this->loop_area;
					break;
			}
		}
	}
	class skinController extends skinController_abstract{
	
		public $skin_file_path = "";
		public $skin = "";
		public $skin_org = "";
		public $skin_re = "";
		public $loop_area = "";
		public $s_str_length = "";
		public $e_str_length = "";
		public $s_str_pos = "";
		public $e_str_pos = "";
		
		//스킨 파일을 or HTML소스코드를 불러옴
		public function skin_file_path($obj){
			if($obj!=""){
				$this->skin_file_path = $obj;
				$this->skin_file_load();
			}
		}
		public function skin_file_load(){
			global $member,$site_config,$member_type_var,$viewDir;
			ob_start();
			include __DIR_PATH__.$this->skin_file_path;
			$this->skin = ob_get_contents();
			$this->skin_org = ob_get_contents();
			ob_end_clean();
		}
		public function skin_html_load($html){
			$this->skin = $html;
			$this->skin_org = $html;
		}
		//스킨파일에서 영역별로 소스코드 분리
		public function skin_loop_header($s_str){
			$this->string_length($s_str,"");
			$this->skin = $this->string_area("header","","");
		}
		public function skin_loop_middle($s_str,$e_str){
			$this->string_length($s_str,$e_str);
			$this->skin = $this->string_area("middle","","");
		}
		public function skin_loop_footer($e_str){
			$this->string_length("",$e_str);
			$this->skin = $this->string_area("footer","","");
		}
		public function skin_loop_array($s_str,$e_str){
			$this->string_length($s_str,$e_str);
			$this->skin = $this->string_area("array",$s_str,$e_str);
		}
		//특정 영역 보이기&감추기
		public function skin_modeling_hideArea($s_str,$e_str,$type){
			if($this->loop_area!=""){
				if($this->skin_re==""){
					$loopSkin = $this->loop_area;
					$this->skin = $this->loop_area;
					$this->skin_re = 1;
				}else{
					$loopSkin = $this->skin;
				}
			}else{
				$loopSkin = $this->skin;
			}
			$this->string_length($s_str,$e_str);
			if(!$this->s_str_pos||!$this->e_str_pos){
				return;
			}
			//치환
			$hide_area = mb_substr($loopSkin,$this->s_str_pos,($this->e_str_pos-$this->s_str_pos)+$this->e_str_length);
			$show_area = mb_substr($loopSkin,$this->s_str_pos+$this->s_str_length,$this->e_str_pos-$this->s_str_pos-$this->s_str_length);
			switch($type){
				case "hide" :
					$this->skin = $this->string_replace($hide_area,"",$loopSkin);
					break;
				case "show" :
					$this->skin = $this->string_replace($hide_area,$show_area,$loopSkin);
					break;
			}
		}
		//스킨 치환
		private function string_replace($org,$chg,$area){
			return str_replace($org,$chg,$area);
		}
		public function skin_modeling($s_str,$r_str){
			if($this->loop_area!=""){
				if($this->skin_re==""){
					$this->skin = $this->string_replace($s_str,$r_str,$this->loop_area);
					$this->skin_re = 1;
				}else{
					$this->skin = $this->string_replace($s_str,$r_str,$this->skin);
				}
			}else{
				$this->skin = $this->string_replace($s_str,$r_str,$this->skin);
			}
		}
		//스킨 출력
		public function skin_echo(){
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
						if(isset($_GET[$expl[$i]])){
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
				//article,m,p 기본 파라미터 처리
				global $article,$m,$p;
				if(isset($_POST['article'])){
					$article = $_POST['article'];
				}
				if(isset($_POST['m'])){
					$m = $_POST['m'];
				}
				if(isset($_POST['p'])){
					$p = $_POST['p'];
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
			unset($_SESSION[$name]);
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
		function is_session($name){
			if(isset($_SESSION[$name])){
				return TRUE;
			}else{
				return FALSE;
			}
		}
	}
?>