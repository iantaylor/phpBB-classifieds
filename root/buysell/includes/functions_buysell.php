<?php
/**
*
* @package phpBB Classifieds MOD
* @version $Id: 0.8.0
* @copyright Ian Taylor
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}
function build_categories()
{
	global $db, $phpbb_root_path, $phpEx, $config;

	$sql = 'SELECT * FROM '.CLASSIFIEDS_CATEGORY_TABLE.' ORDER BY left_id ASC';
	$result	 = $db->sql_query($sql);
	$category = '';
	$show_only_full = $config['show_full'];
	
	
	while ($row = $db->sql_fetchrow($result)) 
	{ 
		
		$category_link = append_sid($phpbb_root_path . 'buysell/index.' . $phpEx, 'mode=cat&amp;id=' . intval($row['id']));
		$parent_link = append_sid($phpbb_root_path . 'buysell/index.' . $phpEx, 'mode=cat&amp;parent_id=' . intval($row['id']));
	
		if ($row['parent'])
		{
			$category .= '<strong><a href="'. $parent_link .'">'. $row['name'] .'</a></strong><br />';
		}
		else
		{
			if($show_only_full && total_ads_per_category($row['id']) != 0)
			{
				$category .= '» <a href="'. $category_link .'">'. $row['name'] .'</a> (' . total_ads_per_category($row['id']) . ')<br />';
			}
			elseif(!$show_only_full)
			{
				$category .= '» <a href="'. $category_link .'">'. $row['name'] .'</a> (' . total_ads_per_category($row['id']) . ')<br />';
			}
			
		}
	
	}
	return $category;
}

/*
	Below function will format the number of ad's the user has that are sold, active and closed.
	usage of ad_stats = 
	sold = 1
	active = 0
	closed = 2
		
*/

function user_total_ads($user_id)
{

	global $db, $user, $config, $phpEx, $phpbb_root_path;
	
	define('SOLD', 1);
	define('ACTIVE', 0);
	define('CLOSED', 2);
	$script_name = str_replace('.' . $phpEx, '', $user->page['page_name']);
	
	
	$sql = 'SELECT COUNT(ad_id) AS number_sold 
			FROM ' . CLASSIFIEDS_TABLE . ' 
			WHERE ad_status = ' . SOLD . ' 
				AND ad_expire > ' . time() . ' 
				AND ad_poster_id = ' . intval($user_id);
				
	$result = $db->sql_query($sql);
	$total_sold = $db->sql_fetchfield('number_sold');
	$db->sql_freeresult($result);
	
	$sql = 'SELECT COUNT(ad_id) AS number_active 
			FROM ' . CLASSIFIEDS_TABLE . ' 
			WHERE ad_status = ' . ACTIVE . ' 
				AND ad_expire > ' . time() . ' 
				AND ad_poster_id = ' . intval($user_id);
				
	$result = $db->sql_query($sql);
	$total_active = $db->sql_fetchfield('number_active');
	$db->sql_freeresult($result);

	$sql = 'SELECT COUNT(ad_id) AS number_closed 
			FROM ' . CLASSIFIEDS_TABLE . ' 
			WHERE ad_status = ' . CLOSED . ' 
				AND ad_expire > ' . time() . ' 
				AND ad_poster_id = ' . intval($user_id);
				
	$result = $db->sql_query($sql);
	$total_closed = $db->sql_fetchfield('number_closed');
	$db->sql_freeresult($result);
	
	if($script_name == 'memberlist')
	{
	
		$ads_link = append_sid("{$phpbb_root_path}buysell/index.$phpEx", 'mode=viewuser&amp;user='.$user_id);
		
		$list_totals = '( <strong>' . $total_active . '</strong> , <strong style="color:' .$config['sold_color'] . '">' . $total_sold . '</strong> , <strong style="color:' . $config['closed_color'] . '">' . $total_closed . '</strong> )<br /><a href="' . $ads_link . '">(' . $user->lang['VIEW_USERS_ADS'] . ')</a>';

	
	}
	
	else
	{
		$list_totals = '<strong>' . $user->lang['ACTIVE_ADS'] . ': ' . $total_active . '</strong><br /><strong style="color:' .$config['sold_color'] . '">' . $user->lang['SOLD_ADS'] . ': ' . $total_sold . '</strong><br /><strong style="color:' . $config['closed_color'] . '">' . $user->lang['CLOSED_ADS'] . ': ' . $total_closed . '</strong>';
		
	}
	
	
	return $list_totals;


}

function total_comments($id)
{

		global $db, $user;

			$sql = 'SELECT COUNT(ad_id) as total_comments FROM '.CLASSIFIEDS_COMMENTS_TABLE.' WHERE ad_id='.$id;
			$result = $db->sql_query($sql);
			$total_comments = $db->sql_fetchfield('total_comments');
			$db->sql_freeresult($result);
			
			return $total_comments;

}
function total_ads_per_category($id)
{

		global $db, $user;

			$sql = 'SELECT COUNT(ad_id) as total_advertisements FROM '.CLASSIFIEDS_TABLE.' WHERE cat_id='.$id.' AND ad_expire > '.time();
			$result = $db->sql_query($sql);
			$total_advertisements = $db->sql_fetchfield('total_advertisements');
			$db->sql_freeresult($result);
			
			return $total_advertisements;

}

function get_ad_category($id)
{

		global $db, $user;

			$sql = 'SELECT name, id FROM '.CLASSIFIEDS_CATEGORY_TABLE." WHERE id=$id";
			$result	 = $db->sql_query($sql);
			$result = $db->sql_query($sql);
			$category_name = $db->sql_fetchfield('name');
			$db->sql_freeresult($result);
			
			return '['.$category_name.']';

}
function get_category_parent($id)
{

		global $db, $user;

			$sql = 'SELECT * FROM '.CLASSIFIEDS_CATEGORY_TABLE." WHERE id=$id";
			$result	 = $db->sql_query($sql);
			$result = $db->sql_query($sql);
			$category_parent_name = $db->sql_fetchfield('name');
			$db->sql_freeresult($result);
			
			return '['.$category_parent_name.']';

}
function move_category_by($category_row, $action = 'move_up')
{
	global $db;

	/**
	* Fetch all the siblings between the module's current spot
	* and where we want to move it to. If there are less than $steps
	* siblings between the current spot and the target then the
	* module will move as far as possible
	*/
	$sql = 'SELECT * FROM ' . CLASSIFIEDS_CATEGORY_TABLE . '
		WHERE ' . (($action == 'move_up') ? "right_id < {$category_row['right_id']} ORDER BY right_id DESC" : "left_id > {$category_row['left_id']} ORDER BY left_id ASC");

	$result = $db->sql_query_limit($sql, 1);

	$target = array();
	while ($row = $db->sql_fetchrow($result))
	{
		$target = $row;
	}
	$db->sql_freeresult($result);

	if (!sizeof($target))
	{
		return false;
	}

	/**
	* $left_id and $right_id define the scope of the nodes that are affected by the move.
	* $diff_up and $diff_down are the values to substract or add to each node's left_id
	* and right_id in order to move them up or down.
	* $move_up_left and $move_up_right define the scope of the nodes that are moving
	* up. Other nodes in the scope of ($left_id, $right_id) are considered to move down.
	*/
	if ($action == 'move_up')
	{
		$left_id = $target['left_id'];
		$right_id = $category_row['right_id'];

		$diff_up = $category_row['left_id'] - $target['left_id'];
		$diff_down = $category_row['right_id'] + 1 - $category_row['left_id'];

		$move_up_left = $category_row['left_id'];
		$move_up_right = $category_row['right_id'];
	}
	else
	{
		$left_id = $category_row['left_id'];
		$right_id = $target['right_id'];

		$diff_up = $category_row['right_id'] + 1 - $category_row['left_id'];
		$diff_down = $target['right_id'] - $category_row['right_id'];

		$move_up_left = $category_row['right_id'] + 1;
		$move_up_right = $target['right_id'];
	}

	// Now do the dirty job
	$sql = 'UPDATE ' . CLASSIFIEDS_CATEGORY_TABLE . "
		SET left_id = left_id + CASE
			WHEN left_id BETWEEN {$move_up_left} AND {$move_up_right} THEN -{$diff_up}
			ELSE {$diff_down}
		END,
		right_id = right_id + CASE
			WHEN right_id BETWEEN {$move_up_left} AND {$move_up_right} THEN -{$diff_up}
			ELSE {$diff_down}
		END
		WHERE
			left_id BETWEEN {$left_id} AND {$right_id}
			AND right_id BETWEEN {$left_id} AND {$right_id}";

	$db->sql_query($sql);

	return $target['name'];
}	

?>