<?php
$Info = array("name" => "Mailer", "class" => "Mailer");

class Mailer
{
	static function Install()
	{
		return 1;
	}

	static function Initialize()
	{
		Messenger::AddListener("send_email", "Mailer::SendMail");
		return 1;
	}

	static function SendMail($recipient, $title, $content)
	{
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'To: <'.$recipient.'>' . "\r\n";
		$headers .= 'From: CFS Mailer <cfs@fatihbakir.net>' . "\r\n";
		mail ($recipient, $title, $content, $headers);
	}
}

?>