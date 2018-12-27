<?php
namespace content_crm\member\security;


class model
{


	public static function getPost()
	{
		$post =
		[
			'sidebar'       => \dash\request::post('sidebar'),
			'twostep'       => \dash\request::post('twostep'),
			'forceremember' => \dash\request::post('forceremember'),
			'status'        => \dash\request::post('status'),
			'language'      => \dash\request::post('language'),
			'username'      => \dash\request::post('username'),

		];

		return $post;
	}


	/**
	 * Posts a user add.
	 */
	public static function post()
	{

		$user_id = \dash\coding::decode(\dash\request::get('id'));


		if(\dash\request::post('type') === 'terminate' && \dash\request::post('id') && is_numeric(\dash\request::post('id')))
		{
			if(\dash\db\sessions::is_my_session(\dash\request::post('id'), $user_id))
			{
				\dash\log::set('sessionTerminate');
				\dash\db\sessions::terminate_id(\dash\request::post('id'));
				\dash\notif::ok(T_("Session terminated"));
				\dash\redirect::pwd();
				return true;
			}
		}

		$request    = self::getPost();
		$password   = \dash\request::post('password');
		$repassword = \dash\request::post('repassword');

		if($password)
		{
			if(!$repassword)
			{
				\dash\notif::error(T_("Please set repassword"), 'repassword');
				return false;
			}

			if($password !== $repassword)
			{
				\dash\notif::error(T_("Password not match whit repassword"), ['element' => ['password', 'repassword']]);
				return false;
			}

			$request['password'] = $password;

		}

		$result = \dash\app\user::edit($request, \dash\request::get('id'));

		if(\dash\engine\process::status())
		{
			\dash\log::set('editProfileSecurity', ['code' => $user_id]);
			\dash\user::refresh();
			\dash\redirect::pwd();
		}
	}
}
?>