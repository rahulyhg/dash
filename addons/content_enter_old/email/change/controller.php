<?php
namespace content_enter\email\change;

class controller extends \addons\content_enter\main\controller
{
	public function ready()
	{
		if(\dash\url::directory() === 'email/change/google')
		{
			// @check
			// \dash\engine\mvc::controller_set("\\addons\content_enter\\email\\change\\google\\controller");
			return;
		}

		// if the user is login redirect to base
		parent::if_login_route();

		// if the user have not an email can not change her email
		// he must set email
		if(!\dash\user::login('email'))
		{
			\dash\redirect::to(\dash\url::base(). '/enter/email/set');
			return;
		}

		$this->get()->ALL('email/change');
		$this->post('change')->ALL('email/change');
	}
}
?>