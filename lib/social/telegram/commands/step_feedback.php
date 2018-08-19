<?php
namespace dash\social\telegram\commands;
// use telegram class as bot
use \dash\social\telegram\tg as bot;
use \dash\social\telegram\step;
use \dash\social\telegram\hook;

class step_feedback
{
	private static $menu = ["hide_keyboard" => true];

	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function start()
	{
		step::start('feedback');

		return self::step1();
	}


	/**
	 * show thanks message
	 * @return [type] [description]
	 */
	public static function step1()
	{
		// after this go to next step
		step::plus();
		// show give contact menu
		$menu     = self::$menu;
		$txt_text = "";
		if(\dash\user::id())
		{
			$txt_text = "با تشکر از شما بابت اعتمادتان.\n\n";
		}
		$txt_text .= "تمام تلاش ما بر افزایش کیفیت _name_ است. ";
		$txt_text .= "بدین منظور دانستن نظرات ارزشمند شما درباره مشکلات و نواقص و صد البته پیشنهادات گرانبهای شما سبب کمک به ما برای این مهم خواهد شد.\n";
		$txt_text .= "لطفا نظر خود را درباره‌ی _name_ برای ما بنویسید.\n";

		$result   =
		[
			'text'         => $txt_text,
			'reply_markup' => $menu,
		];

		bot::sendMessage($result);

		// return menu
		return $result;
	}



	public static function step2($_feedback)
	{
		$txt_text = "نظر ارزشمند شما در ثبت شد.\n";
		$txt_text .= "ممنون از همراهیتان.";
		if(strlen($_feedback) < 10)
		{
			$txt_text = "ممنون!\n";
			// not registerd!
		}

		self::saveComment($_feedback);
		$result   =
		[
			'text'         => $txt_text,
			'reply_markup' => step::get('menu'),
		];
		bot::sendMessage($result);

		step::stop();
		return $result;
	}


	/**
	 * save comment of this user into comments table
	 * @param  [type] $_feedback [description]
	 * @return [type]            [description]
	 */
	private static function saveComment($_feedback)
	{
		$meta =
		[
			'url' => 'telegram'
		];
		if(\dash\user::id())
		{
			$meta['user'] = \dash\user::id();
		}
		$result = \dash\db\comments::save($_feedback, $meta);

		// send feedback to javad account after saving in comments table
		$text   = "📨 بازخورد جدید از ";
		$text   .= hook::from('first_name');
		$text   .= ' '. hook::from('last_name');
		$text   .= "\n$_feedback\n";
		$text   .= "\nکد کاربر ". hook::from();
		$text   .= ' @'. hook::from('username');
		$msg    =
		[
			'method'       => 'sendMessage',
			'text'         => $text,
			'chat_id'      => '46898544',

		];

		$result = bot::sendMessage($msg);


		return $result;
	}
}
?>