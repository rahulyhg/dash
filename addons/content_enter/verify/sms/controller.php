<?php
namespace content_enter\verify\sms;


class controller
{
	public static function routing()
	{
		// if this step is locked go to error page and return
		if(\dash\utility\enter::lock('verify/sms'))
		{
			\dash\header::status(404, 'verify/sms');
			return;
		}
	}
}
?>