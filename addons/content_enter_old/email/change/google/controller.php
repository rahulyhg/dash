<?php
namespace addons\content_enter\email\change\google;

class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		$url = \lib\url::directory();
		// if(self::lock('email/change/google'))
		// {
		// 	self::error_page('email/change/google');
		// 	return;
		// }

		$this->get()->ALL('email/change/google');
		$this->post('change_google')->ALL('email/change/google');
	}
}
?>