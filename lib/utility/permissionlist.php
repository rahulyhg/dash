<?php
namespace dash\utility;


class permissionlist
{
	private static $count_use = 0;
	private static $count = 0;

	private static function find($_path)
	{
		$permission_caller = [];
		$directory         = new \RecursiveDirectoryIterator($_path);
		$flattened         = new \RecursiveIteratorIterator($directory);
		$files             = new \RegexIterator($flattened, "/\\.(php|html)\$/i");

		foreach($files as $file)
		{

			$fileExt     = \dash\file::getExtension($file);
			$lines       = file($file);
			$find_access = "\\permission::access(";
			$find_check  = "\\permission::check(";
			$find_html   = "perm(";

			foreach($lines as $num => $line)
			{
				if(strpos($line, $find_access) !== false)
				{
					preg_match("/permission\::access\((\'|\")([\w\d\:\_\-]+)(\'|\")\)/", $line, $split);
					if(isset($split[2]))
					{
						self::$count_use++;
						$permission_caller[] = $split[2];
					}
				}

				if(strpos($line, $find_check) !== false)
				{
					preg_match("/permission\::check\((\'|\")([\w\d\:\_\-]+)(\'|\")\)/", $line, $split);
					if(isset($split[2]))
					{
						self::$count_use++;
						$permission_caller[] = $split[2];
					}
				}

				if($fileExt === 'html')
				{
					if(strpos($line, $find_html) !== false)
					{
						preg_match("/perm\((\'|\")([\w\d\:\_\-]+)(\'|\")\)/", $line, $split);
						if(isset($split[2]))
						{
							self::$count_use++;
							$permission_caller[] = $split[2];
						}
					}
				}
			}
		}

		$permission_caller = array_filter($permission_caller);
		$permission_caller = array_unique($permission_caller);
		$permission_caller = array_values($permission_caller);
		self::$count += count($permission_caller);
		return $permission_caller;
	}


	// Create a files in language folder has contain twig trans value
	public static function extract()
	{
		ob_start();

		$mypath            = realpath(core).DIRECTORY_SEPARATOR;
		$permission_caller = self::find($mypath);
		// \dash\permission::write_file($permission_caller, 'dash');
		$dash_caller = $permission_caller;

		$mypath            = realpath(root).DIRECTORY_SEPARATOR;
		$permission_caller = self::find($mypath);
		// \dash\permission::write_file($permission_caller, 'project');

		echo '<h1>EXTRACT PERMISSION CALLERS ('.self::$count.' callers founded | used in: '.self::$count_use.' place)</h1><hr><h3>DASH</h3>';
		\dash\code::pretty($dash_caller, true);

		echo '<hr><h3>PROJECT</h3>';
		\dash\code::pretty($permission_caller, true);
	}
}
