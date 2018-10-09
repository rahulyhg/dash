<?php
namespace content_account\address;


class view
{

	public static function config()
	{
		\dash\data::page_title(T_('Edit your profile'));
		\dash\data::page_desc(T_('You can edit your profile.'));

		\dash\data::badge_link(\dash\url::base(). '/a');
		\dash\data::badge_text(T_('Back to dashbaord'));

		$args               = [];
		$args['user_id']    = \dash\user::id();
		$args['pagenation'] = false;
		$args['status']     = 'enable';
		$dataTable          = \dash\app\address::list(null, $args);
		\dash\data::dataTable($dataTable);

		self::static_var();

	}

	private static function static_var()
	{

		$countryList = \dash\utility\location\countres::$data;
		\dash\data::countryList($countryList);

		$cityList    = \dash\utility\location\cites::$data;
		$proviceList = \dash\utility\location\provinces::key_list('localname');

		$new = [];
		foreach ($cityList as $key => $value)
		{
			$temp = '';

			if(isset($value['province']) && isset($proviceList[$value['province']]))
			{
				$temp .= $proviceList[$value['province']]. ' - ';
			}
			if(isset($value['localname']))
			{
				$temp .= $value['localname'];
			}
			$new[$key] = $temp;
		}
		asort($new);

		\dash\data::cityList($new);
	}
}
?>