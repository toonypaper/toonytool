<?php
	/*
	메일 발송 클래스
	*/
	interface mail_interface{
		
		function mail_send();
	
	}
	class mailSender extends mysqlConnection implements mail_interface{
		
		private $headers;
		public $template = "mailling";
		public $t_name = "";
		public $t_email = "";
		public $f_email = "";
		public $f_name = "";
		public $account_check_url = "";
		public $account_password  = "";
		public $subject = "";
		public $memo = "";
		public $site_title = "";
		public $site_name = "";
		public $bodySource = "";
		
		public function mail_send(){
			global $site_config;
			$this->f_name = '=?UTF-8?B?'.base64_encode($site_config['ad_site_name']).'?=';
			$this->f_email = $site_config['ad_email'];
			$this->site_title = $site_config['ad_site_title'];
			$this->site_name = $site_config['ad_site_name'];
			$this->memo = str_replace("{{name}}",$this->t_name,$this->memo);
			$this->select("
				SELECT source
				FROM toony_admin_mailling_template
				WHERE type='{$this->template}'
			");
			$this->htmlspecialchars = 0;
			$this->nl2br = 0;
			$this->bodySource = str_replace("{{site_title}}",$this->site_title,$this->fetch("source"));
			$this->bodySource = str_replace("{{site_name}}",$this->site_name,$this->bodySource);
			$this->bodySource = str_replace("{{check_url}}",$this->account_check_url,$this->bodySource);
			$this->bodySource = str_replace("{{password}}",$this->account_password,$this->bodySource);
			$this->bodySource = str_replace("{{name}}",$this->t_name,$this->bodySource);
			$this->bodySource = str_replace("{{memo}}",$this->memo,$this->bodySource);
			//헤더 정보 설정
			$this->headers  = "MIME=Version: 1.0\r\n";
			$this->headers .= "Content-type:text/html; charset=UTF-8\r\n";
			$this->headers .= "From: ".$this->f_name."<".$this->f_email.">\r\n";
			$this->headers .= "Return-Path: ".$this->f_email."\r\n";
			$this->subject = '=?UTF-8?B?'.base64_encode($this->subject).'?=';
			//메일 발송
			mail($this->t_email,$this->subject,$this->bodySource,$this->headers);
		}
	}
?>