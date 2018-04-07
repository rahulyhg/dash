<?php
namespace content_enter\email\change;


class model extends \addons\content_enter\main\model
{
	/**
	 * Removes an email.
	 */
	public function remove_email()
	{
		if(\dash\user::login('email') && \dash\user::id())
		{
			\dash\db\users::update(['email' => null], \dash\user::id());
			// set the alert message
			self::set_alert(T_("Your email was removed"));
			// open lock of alert page
			self::next_step('alert');
			// go to alert page
			self::go_to('alert');
		}
	}


	/**
	 * Posts an enter.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_change($_args)
	{
		if(\dash\request::post('type') === 'remove')
		{
			$this->remove_email();
			return;
		}

		if(!\dash\request::post('emailNew'))
		{
			\dash\notif::error(T_("Plese fill the new email"));
			return false;
		}

		if(\dash\user::login('email') == \dash\request::post('emailNew'))
		{
			\dash\notif::error(T_("Please select a different email"));
			return false;
		}

		if(\dash\request::post('emailNew'))
		{
			self::set_enter_session('temp_email', \dash\request::post('emailNew'));
		}

		// set session verify_from set
		self::set_enter_session('verify_from', 'email_set');

		// send code whit email
		self::send_code_email();
	}
}
?>