<?php
namespace content_su\sendnotify;
class controller extends \addons\content_su\main\controller
{
	public function ready()
	{
		parent::ready();

		$this->post('nofity')->ALL();
	}
}
?>