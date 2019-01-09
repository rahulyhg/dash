<?php
namespace content_enter;


class view
{

	public static function config()
	{
		\dash\data::include_css(false);
		\dash\data::include_js(false);
		\dash\data::bodyclass('enter');
		\dash\data::bodyclass(\dash\data::bodyclass(). ' bg'. date('g'));

		// get mobile number to show in mobile input
		$session_mobile = \dash\utility\enter::get_session('usernameormobile');
		$temp_mobile    = \dash\utility\enter::get_session('temp_mobile');
		$myMobile       = null;
		$get_mobile     = \dash\request::get('mobile');

		if(\dash\user::login('mobile'))
		{
			$myMobile = \dash\user::login('mobile');
		}
		elseif($session_mobile)
		{
			$myMobile = $session_mobile;
		}
		elseif($temp_mobile)
		{
			$myMobile = $temp_mobile;
		}
		elseif($get_mobile && \dash\utility\filter::mobile($get_mobile))
		{
			$myMobile = \dash\utility\filter::mobile($get_mobile);
		}

		if($get_mobile && \dash\utility\filter::mobile($get_mobile) && \dash\permission::supervisor())
		{
			$myMobile = \dash\utility\filter::mobile($get_mobile);
		}

		// if mobile not set but the user was login
		// for example in pass/change page
		// get the user mobile from login.mobile
		// set mobile in display
		\dash\data::getMobile($myMobile);
		\dash\data::getUsernamemobile($myMobile);

		if(!\dash\url::module())
		{
			if(\dash\permission::supervisor())
			{
				if(!$get_mobile)
				{
					\dash\data::getMobile(null);
					\dash\data::getUsernamemobile(null);
				}
			}
		}


		// in all page the mobiel input is readonly
		\dash\data::mobileReadonly(true);

		\dash\data::googleLogin(\dash\option::social('google', 'status'));

		if(\dash\url::subdomain())
		{
			\dash\data::googleLogin(false);
		}

	}
}
?>