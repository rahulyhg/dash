<?php
namespace addons\content_enter\home;

class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		// if the user login redirect to base
		if(\dash\permission::access('enter:another:session'))
		{
			// the admin can login by another session
			// never redirect to main
		}
		else
		{
			parent::if_login_not_route();
		}

		// check remeber me is set
		// if remeber me is set: login!
		parent::check_remember_me();

		// save all param-* | param_* in $_GET | $_POST
		$this->save_param();

		if(\dash\request::get('referer') && \dash\request::get('referer') != '')
		{
			$_SESSION['enter_referer'] = \dash\request::get('referer');
		}

		if(self::get_request_method() === 'get')
		{
			$this->get(false, 'enter')->ALL();
		}
		elseif(self::get_request_method() === 'post')
		{
			$this->post('enter')->ALL();
		}
		else
		{
			self::error_method('home');
		}
	}


	/**
	 * Saves a parameter.
	 * save all param-* in url into the session
	 *
	 */
	public function save_param()
	{
		$post = \dash\request::post();
		$get = \dash\request::get();

		$param = [];

		if(is_array($post) && is_array($get))
		{
			$param = array_merge($get, $post);
		}

		if(!is_array($param))
		{
			$param = [];
		}

		$save_param = [];

		foreach ($param as $key => $value)
		{
			if(preg_match("/^param(\-|\_)(.*)$/", $key, $split))
			{
				if(isset($split[2]))
				{
					$save_param[$split[2]] = $value;
				}
			}
		}

		if(!empty($save_param))
		{
			$_SESSION['param'] = $save_param;
		}
	}
}
?>