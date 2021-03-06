<?php
namespace content_cp\posts\edit;

class view
{
	public static function config()
	{
		$id = \dash\request::get('id');

		$detail = \dash\app\posts::get($id);
		if(!$detail)
		{
			\dash\header::status(403, T_("Invalid id"));
		}

		\dash\data::dataRow($detail);

		\content_cp\posts\main\view::myDataType();

		$moduleTypeTxt = \dash\data::myDataType();
		$moduleType    = '';

		if(\dash\data::myDataType())
		{
			$moduleType = '?type='. \dash\data::myDataType();
		}

		\dash\data::page_pictogram('file-text-o');

		\dash\data::moduleTypeTxt($moduleTypeTxt);
		\dash\data::moduleType($moduleType);


		$myTitle = T_("Edit post");
		$myDesc  = T_("You can change everything, change url and add gallery or some other change");

		$myBadgeLink = \dash\url::this(). $moduleType;
		$myBadgeText = T_('Back to list of posts');

		$myType = \dash\data::myDataType();

		if($myType)
		{
			switch ($myType)
			{
				case 'page':
					\dash\permission::access('cpPageEdit');

					$myTitle = T_('Edit page');
					$myBadgeText = T_('Back to list of pages');

					$pageList = \dash\db\posts::get(['type' => 'page', 'language' => \dash\language::current(), 'status' => ["NOT IN", "('deleted')"]]);
					$pageList = array_map(['\dash\app\posts', 'ready'], $pageList);

					\dash\data::pageList($pageList);
					break;

				case 'help':
					\dash\permission::access('cpHelpCenterEdit');
					$myTitle     = T_('Edit help');
					$myBadgeText = T_('Back to list of helps');
					$myDesc      = T_("Helps can contain keyword and category with title and descriptions.");
					\dash\data::listCats(\dash\app\term::cat_list('help'));
					$pageList = \dash\db\posts::get(['type' => 'help', 'language' => \dash\language::current(), 'status' => ["NOT IN", "('deleted')"]]);
					$pageList = array_map(['\dash\app\posts', 'ready'], $pageList);
					\dash\data::pageList($pageList);

					break;

				case 'post':
				default:
					\dash\permission::access('cpPostsEdit');
					\dash\data::listCats(\dash\app\term::cat_list());
					$myTitle = T_('Edit post');
					$myBadgeText = T_('Back to list of posts');
					break;
			}
		}
		else
		{
			\dash\permission::access('cpPostsEdit');
		}

		\dash\data::page_title($myTitle);
		\dash\data::page_desc($myDesc);

		\dash\data::badge_text($myBadgeText);
		\dash\data::badge_link($myBadgeLink);

	}
}
?>
