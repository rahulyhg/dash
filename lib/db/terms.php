<?php
namespace dash\db;

/** terms managing **/
class terms
{
	/**
	 * this library work with terms
	 * v1.0
	 */


	/**
	 * insert new tag in terms table
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert()
	{
		\dash\db\config::public_insert('terms', ...func_get_args());
		return \dash\db::insert_id();
	}


	/**
	 * insert multi value to terms
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function multi_insert()
	{
		return \dash\db\config::public_multi_insert('terms', ...func_get_args());
	}


	/**
	 * update field from terms table
	 * get fields and value to update
	 * @example update table set field = 'value' , field = 'value' , .....
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function update()
	{
		return \dash\db\config::public_update('terms', ...func_get_args());
	}


	/**
	 * get the terms by id
	 *
	 * @param      <type>  $_term_id  The term identifier
	 * @param      string  $_field    The field
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get()
	{
		return \dash\db\config::public_get('terms', ...func_get_args());
	}


	/**
	 * Searches for the first match.
	 *
	 * @param      <type>  $_title  The title
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function search()
	{
		return \dash\db\config::public_search('terms', ...func_get_args());
	}


	/**
	 * get the terms by caller field
	 *
	 * @param      <type>   $_caller  The caller
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function caller($_caller)
	{
		$args =
		[
			'caller' => $_caller,
			'limit'  => 1,
		];

		return self::get($args);

	}


	public static function check_multi_id($_ids, $_type)
	{
		if(!is_array($_ids) || !$_type || !$_ids || !$_ids)
		{
			return false;
		}

		$_ids = implode(',', $_ids);

		$query =
		"
			SELECT *
			FROM terms
			WHERE
				terms.id IN ($_ids) AND
				terms.type = '$_type'
		";
		$result = \dash\db::get($query);

		return $result;

	}


	public static function get_mulit_term_title($_titles, $_type)
	{
		if(!is_array($_titles) || !$_type || !$_titles)
		{
			return false;
		}

		$_titles = implode("','", $_titles);

		$query =
		"
			SELECT *
			FROM terms
			WHERE
				terms.title IN ('$_titles') AND
				terms.type = '$_type'
		";
		$result = \dash\db::get($query);

		return $result;
	}
}
?>
