<?php
	/*
	메일 발송 클래스
	*/
	class mailSender extends mysqlConnection{
		//메일을 설정함
		function func_mail_sender(){
			global $site_config;
			//메일 변수 초기화
			$this->func_mail_sender->temp = "mailling";
			$this->func_mail_sender->t_name = "";
			$this->func_mail_sender->t_email = $site_config['ad_email'];
			$this->func_mail_sender->account_check_url = "";
			$this->func_mail_sender->account_password = "";
			$this->func_mail_sender->subject = $site_config['ad_site_name'];
			$this->func_mail_sender->memo = "";
			$this->func_mail_sender->f_email = $site_config['ad_email'];
			$this->func_mail_sender->f_name = $site_config['ad_site_name'];
			$this->func_mail_sender->f_name_re = '=?UTF-8?B?'.base64_encode($this->func_mail_sender->f_name).'?=';
		}
		//설정된 메일을 발송함
		function func_mail_sender_get(){
			global $site_config;
			//본문 내용 치환자 치환
			$this->func_mail_sender->memo = str_replace("{{name}}",$this->func_mail_sender->t_name,$this->func_mail_sender->memo);
			//템플릿 치환자 치환
			$this->select("
				SELECT source
				FROM toony_admin_mailling_template
				WHERE type='{$this->func_mail_sender->temp}'
			");
			$this->htmlspecialchars = 0;
			$this->nl2br = 0;
			$this->source = $this->fetch("source");
			$this->bodySource = str_replace("{{site_title}}",$site_config['ad_site_title'],$this->source);
			$this->bodySource = str_replace("{{site_name}}",$site_config['ad_site_name'],$this->bodySource);
			$this->bodySource = str_replace("{{check_url}}",$this->func_mail_sender->account_check_url,$this->bodySource);
			$this->bodySource = str_replace("{{password}}",$this->func_mail_sender->account_password,$this->bodySource);
			$this->bodySource = str_replace("{{name}}",$this->func_mail_sender->t_name,$this->bodySource);
			$this->bodySource = str_replace("{{memo}}",$this->func_mail_sender->memo,$this->bodySource);
			//헤더 정보 설정
			$this->func_mail_sender->headers  = "MIME=Version: 1.0\r\n";
			$this->func_mail_sender->headers .= "Content-type:text/html; charset=UTF-8\r\n";
			$this->func_mail_sender->headers .= "From: ".$this->func_mail_sender->f_name_re."<".$this->func_mail_sender->f_email.">\r\n";
			$this->func_mail_sender->headers .= "Return-Path: ".$this->func_mail_sender->f_email."\r\n";
			$this->t_email = $this->func_mail_sender->t_email;
			$this->subject = '=?UTF-8?B?'.base64_encode($this->func_mail_sender->subject).'?=';
			$this->body = $this->bodySource;
			$this->headers = $this->func_mail_sender->headers;
			mail($this->t_email,$this->subject,$this->body,$this->headers);
		}
	}
?>