<?php
namespace addons\content_enter\main;

class controller extends \mvc\controller
{
	use _use;
	public function repository()
	{
		$url = \lib\url::directory();
		// /main can not route
		if($url === 'main')
		{
			\lib\header::status(404, T_("Unavalible"));
		}

		// // redirect subdomain to main domain on enter
		// if(\lib\url::subdomain())
		// {
		// 	// ---------------------------------------------- temporary, fix this
		// 	$mainEnter = \lib\url::protocol().'://'. \lib\url::domain().'/enter';
		// 	\lib\redirect::to($mainEnter);
		// }
	}


	/**
	* if the user is login redirect to base
	*/
	public function if_login_not_route()
	{
		if(\lib\user::login())
		{
			self::go_to(\lib\url::base());
		}
	}


	/**
	* if login route
	*/
	public function if_login_route()
	{
		if(!\lib\user::login())
		{
			self::go_to(\lib\url::base());
		}
	}


	/**
	* check is set remeber me of this user
	*/
	public function check_remember_me()
	{
		if(\lib\db\sessions::get_cookie() && !\lib\user::login())
		{
			$user_id = \lib\db\sessions::get_user_id();

			if($user_id && is_numeric($user_id))
			{
				// set user id in static var
				self::$user_id = $user_id;
				// load user data by user id
				self::load_user_data('user_id');
				// set login session
				self::enter_set_login(null, true);
			}
		}
	}
}
?>