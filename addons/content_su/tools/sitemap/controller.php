<?php
namespace addons\content_su\tools\sitemap;

class controller extends \addons\content_su\main\controller
{
	public function ready()
	{
		parent::ready();

		$this->get()->ALL();
	}
}
?>