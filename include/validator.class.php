<?php
	/*
	유효성 검사 클래스
	*/
	class validator{
		
		private function validt_input_value($val){
			global $$val;
			return trim($$val);
		}
		public function validt_success($msg,$location){
			echo "success::{$msg}";
			echo "|location::{$location}";
			exit;
		}
		public function validt_success_return($ele,$msg){
			echo "success_returnStr::{$ele}";
			echo "|msg::{$msg}";
			exit;
		}
		public function validt_success_function($func){
			echo "success_returnFunction::{$func}";
			exit;
		}
		public function validt_returnAjax($msg,$ajaxDoc){
			echo "returnAjax::{$msg}";
			echo "|ajaxDoc::{$ajaxDoc}";
			exit;
		}
		//무조건 에러 출력
		public function validt_diserror($val,$msg){
			echo "error::{$val}";
			echo "|msg::{$msg}";
			exit;
		}
		//정규식으로 검사 후 에러 출력
		private function validt_match($val,$match,$msg){
			global $$val;
			if($this->validt_input_value($val)!=""&&!preg_match($match,$$val)){
				$this->validt_diserror($val,$msg);
			}
		}
		//글자수만 검사 후 에러 출력
		public function validt_strLen($val,$minLen,$maxLen,$null,$msg){
			global $$val;
			ob_start();
			mb_internal_encoding("UTF-8");
			if($minLen==""){
				$minLen = 0;
			}
			if($this->validt_input_value($val)!=""){
				if(mb_strlen($$val)<$minLen){
					echo "error::{$val}";
					echo "|msg::{$msg}";
					exit;
				}
				if($maxLen!=""&&mb_strlen($$val)>$maxLen){
					echo "error::{$val}";
					echo "|msg::{$msg}";
					exit;
				}
			}
			if($null==1){
				$this->validt_null($val,$msg);
			}
		}
		//공백인지 검사
		public function validt_null($val,$msg){
			global $$val;
			if(trim($$val)==""){
				if(trim($msg)==""){
					$msg = "NULL_ERROR";
				}
				$this->validt_diserror($val,$msg);
				exit;
			}
		}
		//체크박스 체크 유무 검사
		public function validt_checked($val,$msg){
			global $$val;
			if($$val!="checked"){
				$this->validt_diserror($val,$msg);
				exit;
			}
		}
		//셀렉트박스 선택 유무 검사
		public function validt_selected($val,$msg){
			global $$val;
			if($$val=="none"||$$val==""){
				$this->validt_diserror($val,$msg);
				exit;
			}
		}
		//이메일인지 검사
		public function validt_email($val,$null,$msg){
			$match = "/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/";
			$this->validt_match($val,$match,$msg);
			if($null==1){
				$this->validt_null($val,$msg);
			}
		}
		//한글인지 검사
		public function validt_kor($val,$minLen,$maxLen,$null,$msg){
			$match = "/^[a-zA-Z0-9가-힣]+$/";
			$this->validt_match($val,$match,$msg);
			$this->validt_strLen($val,$minLen,$maxLen,$null,$msg);
			if($null==1){
				$this->validt_null($val,$msg);
			}
		}
		//닉네임인지 검사
		public function validt_nick($val,$null,$msg){
			$match = "/^[a-zA-Z0-9가-힣]+$/";
			$this->validt_match($val,$match,$msg);
			$this->validt_strLen($val,2,12,$null,$msg);
			if($null==1){
				$this->validt_null($val,$msg);
			}
		}
		//패스워드인지 검사
		public function validt_password($val,$null,$msg){
			$this->validt_strLen($val,5,50,$null,$msg);
			if($null==1){
				$this->validt_null($val,$msg);
			}
		}
		//이름인지 검사
		public function validt_name($val,$null,$msg){
			$match = "/^[가-힣]+$/";
			$this->validt_match($val,$match,$msg);
			$this->validt_strLen($val,2,10,$null,$msg);
			if($null==1){
				$this->validt_null($val,$msg);
			}
		}
		//연락처인지 검사
		public function validt_phone($val,$null,$msg){
			$match = "/^[0-9]+$/";
			$this->validt_match($val,$match,$msg);
			$this->validt_strLen($val,8,15,$null,$msg);
			if($null==1){
				$this->validt_null($val,$msg);
			}
		}
		//숫자인지 검사
		public function validt_number($val,$minLen,$maxLen,$null,$msg){
			$match = "/^[0-9]+$/";
			$this->validt_match($val,$match,$msg);
			$this->validt_strLen($val,$minLen,$maxLen,$null,$msg);
			if($null==1){
				$this->validt_null($val,$msg);
			}
		}
		//idx인지 검사
		public function validt_idx($val,$null,$msg){
			$match = "/^[0-9a-zA-Z_]+$/";
			$this->validt_match($val,$match,$msg);
			$this->validt_strLen($val,3,15,$null,$msg);
			if($null==1){
				$this->validt_null($val,$msg);
			}
		}
		//영어인지 검사
		public function validt_eng($val,$minLen,$maxLen,$null,$msg){
			$match = "/^[a-zA-Z_]+$/";
			$this->validt_match($val,$match,$msg);
			$this->validt_strLen($val,$minLen,$maxLen,$null,$msg);
			if($null==1){
				$this->validt_null($val,$msg);
			}
		}
		//사용금지 태그 사용했는지 검사
		public function validt_tags($val,$null,$msg){
			global $$val;
			$not_tags = "?,script,iframe,link,meta";
			$not_tags_ex = explode(",",$not_tags);
			for($i=0;$i<count($not_tags_ex);$i++){
				if(stristr($$val,"<".$not_tags_ex[$i])||stristr($$val,"</".$not_tags_ex[$i])){
					if($msg==""){
						$msg = "허용되지 않는 태그가 포함되어 있습니다.";
					}
					$this->validt_diserror($val,$msg);
					return;
				}
			}
			if($null==1){
				$this->validt_null($val,$msg);
			}
		}
	}
?>