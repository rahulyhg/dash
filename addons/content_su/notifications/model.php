<?php
namespace addons\content_su\notifications;


class model extends \addons\content_su\main\model
{
	public function notifications_list($_args, $_fields = [])
	{
		$meta   = [];
		$meta['admin'] = true;

		$search = null;
		if(\dash\request::get('search'))
		{
			$search = \dash\request::get('search');
		}

		foreach ($_fields as $key => $value)
		{
			if(isset($_args->get($value)[0]))
			{
				$meta[$value] = $_args->get($value)[0];
			}
		}

		$result = \dash\db\notifications::search($search, $meta);

		return $result;
	}
}
?>
