<?php
namespace content_su\logitems;

class controller extends \addons\content_su\main\controller
{
	public $fields =
	[
		'id',
		'type',
		'caller',
		'title',
		'desc',
		'meta',
		'count',
		'priority',
		'datemodified',
		'datecreated',
	];

	public function ready()
	{
		parent::ready();

		$property                 = [];
		foreach ($this->fields as $key => $value)
		{
			$property[$value] = ["/.*/", true, $value];
		}

		$this->get(false, "list")->ALL(['property' => $property]);

	}
}
?>