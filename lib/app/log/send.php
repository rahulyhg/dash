<?php
namespace dash\app\log;


class send
{
	public static function notification()
	{
		$not_send = \dash\db\logs::get(['notif' => 1, 'send' => null]);
		if(!$not_send || !is_array($not_send))
		{
			// nothing to send
			return true;
		}

		\dash\temp::set('logLoadUserDetail', true);

		$not_send = array_map(['\dash\app\log', 'ready'], $not_send);

		$send_telegram = [];
		$send_sms      = [];
		$send_email    = [];


		foreach ($not_send as $key => $value)
		{
			if(isset($value['telegram']) && $value['telegram'])
			{
				$send_telegram[] = $value;
			}

			if(isset($value['sms']) && $value['sms'])
			{
				$send_sms[] = $value;
			}

			if(isset($value['email']) && $value['email'])
			{
				$send_email[] = $value;
			}
		}

		// 'awaiting','sended','expire','cancel','cannotsend', 'turnoff'

		if(!empty($send_telegram))
		{
			if(!\dash\option::social('telegram', 'status'))
			{
				$id_raw = array_column($send_telegram, 'id_raw');
				if(!empty($id_raw))
				{
					$id_raw = implode(',', $id_raw);
					\dash\db\logs::update_where(['send' => 0],['id' => ["IN", "($id_raw)"]]);
				}
			}
			else
			{
				$start_time = time();
				$count_send = 0;
				foreach ($send_telegram as $key => $value)
				{
					if(!isset($value['user_detail']) || (isset($value['user_detail']) && !is_array($value['user_detail'])))
					{
						\dash\db\logs::update(['send' => 0], $value['id_raw']);
						continue;
					}

					\dash\db\logs::update(['send' => 1], $value['id_raw']);

					foreach ($value['user_detail'] as $user_id => $user_detail)
					{
						if(isset($user_detail['chat_id']) && isset($value['send_sms']['telegram']))
						{
							$myData   = ['chat_id' => $user_detail['chat_id'], 'text' => $value['send_sms']['telegram']];
							$myResult = \dash\social\telegram\tg::json_sendMessage($myData);

							// @check need to check the telegram is send this message or not
							if($myResult)
							{
							}

							$count_send++;

							if((time() - $start_time) > 60 || $count_send > 20)
							{
								return false;
							}
						}
					}
				}
			}
		}

		if(!empty($send_sms))
		{
			if(!\dash\option::social('kavenegar', 'status'))
			{
				\dash\db\sendnotifications::set_status('turnoff', array_column($send_sms, 'id'));
			}
			else
			{
				if(\dash\url::isLocal())
				{
					return;
				}

				\dash\db\sendnotifications::set_status('sended', array_column($send_sms, 'id'));
				foreach ($send_sms as $key => $value)
				{
					// \dash\utility\sms::send($value['to'], $value['text']);
				}
			}
		}
	}
}
?>
