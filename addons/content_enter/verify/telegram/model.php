<?php
namespace content_enter\verify\telegram;


class model
{

	public function post_verify()
	{
		// runcall
		if(mb_strtolower(\dash\request::post('verify')) === 'true')
		{
			if(!\dash\utility\enter::get_session('run_telegram_to_user'))
			{
				\dash\notif::result("Telegram sended");
				\dash\utility\enter::set_session('run_telegram_to_user', true);
				self::send_telegram_code();
			}
			return;
		}
		\dash\data::check_code('telegram');
	}


	/**
	 * send verification code to the user telegram
	 *
	 * @param      <type>  $_chat_id  The chat identifier
	 * @param      <type>  $_text     The text
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function send_telegram_code()
	{
		// the telegram is off for this project
		if(!\dash\option::social('telegram', 'status'))
		{
			return false;
		}

		$my_chat_id = null;

		if(\dash\utility\enter::user_data('chatid'))
		{
			$my_chat_id = \dash\utility\enter::user_data('chatid');
		}
		elseif(\dash\utility\enter::get_session('temp_chat_id'))
		{
			$my_chat_id = \dash\utility\enter::get_session('temp_chat_id');
		}

		if(!$my_chat_id)
		{
			return false;
		}

		$code = \dash\utility\enter::get_session('verification_code');
		// make text
		$text = '';
		$text .= T_("Your login code is :code", ['code' => \dash\utility\human::number($code)]);
		$text .= "\n\n". T_("This code can be used to log in to your account. Do not give it to anyone!");
		$text .= "\n" . T_("If you didn't request this code, ignore this message.");

		\dash\utility\telegram::sendMessage($my_chat_id, $text);
		return true;
	}
}
?>
