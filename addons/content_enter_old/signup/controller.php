<?php
namespace addons\content_enter\signup;

class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		parent::if_login_not_route();

		if(\lib\request::get('referer') && \lib\request::get('referer') != '')
		{
			$_SESSION['enter_referer'] = \lib\request::get('referer');
		}

		if(self::get_request_method() === 'get')
		{
			$this->get(false, 'signup')->ALL();
		}
		elseif(self::get_request_method() === 'post')
		{
			$this->post('signup')->ALL();
		}
		else
		{
			self::error_method('home');
		}
	}
}
?>