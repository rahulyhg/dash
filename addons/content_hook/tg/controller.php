<?php
namespace content_hook\tg;

class controller
{
	/**
	 * allow telegram to access to this location
	 * to send response to our server
	 * @return [type] [description]
	 */
	public static function routing()
	{
		$myhook = 'tg/'.\dash\option::social('telegram', 'hookFolder');

		if(\dash\url::child() === \dash\option::social('telegram', 'hookFolder'))
		{
			// disable log visitors
			\dash\temp::set('force_stop_visitor', true);

			// fire telegram api
			$result = \dash\social\telegram\tg::fire();
			if(isset($_SERVER['HTTP_POSTMAN_TOKEN']))
			{
				if($result)
				{
					echo("This result only show in postman!)\n");
					$result = \dash\social\telegram\log::json($result);
					echo($result);
					echo("\n--debug\n");
				}
				else
				{
					echo("Final result is empty!");
				}

			}

			// bobooom :)
			\dash\code::boom();
		}

		// log access to this url
		\dash\log::set('tgUnauthorizedAccess');
		\dash\header::status(404);
	}
}
?>