<?php
namespace content_account;

class view
{
	public static function config()
	{
		\dash\data::include_adminPanel(true);
		\dash\data::include_css(false);
		\dash\data::include_js(false);

		\dash\data::include_editor(true);

		\dash\data::display_admin('content_account/layout.html');
	}
}
?>