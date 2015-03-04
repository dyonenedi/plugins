<?php
	
	namespace App\Plugin\Dyonenedi;
	

	class Mail
	{
		public static function sendMail($from, $to, $subject, $message, $fromName='', $toName='') {
			$msg = wordwrap($message, 70 );
			
			$headers = 
				'Return-Path: ' . $from . "\r\n" . 
				'From: ' . $fromName . ' <' . $from . '>' . "\r\n" . 
				'X-Priority: 3' . "\r\n" . 
				'X-Mailer: PHP ' . phpversion() .  "\r\n" . 
				'Reply-To: ' . $fromName . ' <' . $from . '>' . "\r\n" .
				'MIME-Version: 1.0' . "\r\n" . 
				'Content-Transfer-Encoding: 8bit' . "\r\n" . 
				'Content-Type: text/plain; charset=UTF-8' . "\r\n
			";
			
			$params = '-f ' . $from;

			if (mail($to, utf8_decode($subject), $msg, $headers, $params)) {
				return true;
			} else {
				return false;
			}
		}
	}