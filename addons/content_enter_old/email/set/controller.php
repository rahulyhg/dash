<?php
namespace addons\content_enter\email\set;

class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		// if the user is login redirect to base
		parent::if_login_route();

		// parent::ready();
		// if the user have email can not set email again
		// he must change her email
		if(\lib\user::login('email'))
		{
			\lib\redirect::to(\lib\url::base(). '/enter/email/change');
			return;
		}

		$this->get()->ALL('email/set');

		$this->post('email')->ALL('email/set');
	}
}
?>