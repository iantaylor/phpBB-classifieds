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
class acp_classifieds
{
   var $u_action;
   var $new_config;
   function main($id, $mode)
   {
      global $db, $user, $auth, $template, $cache;
      global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;
      include($phpbb_root_path . 'buysell/includes/functions_buysell.' . $phpEx);
      include($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);
	  include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);

      
      switch($mode)
      {
         case 'index':
            $this->page_title = 'ACP_CLASSIFIEDS';
            $this->tpl_name = 'acp_classifieds';
            break;
            
         case 'manage':
            $this->page_title = 'ACP_CLASSIFIEDS';
            $this->tpl_name = 'acp_classifieds_manage';
            break;
            
       }
       
$action			= request_var('action', '');
$id				= request_var('id', 0);
$settings 		= request_var('settings', '');
$ad_id = request_var('ad_id', 0);
$name 	= request_var('name', '', true);

$sql_ary = (array(
	'name'       		=> $name,
	'parent'			=> request_var('parent', 0),
	'parent_id'			=> request_var('parent_id', 0)

));

switch ($action)
{
	case 'move_up':
	case 'move_down':

	
		if (!$id)
		{
			trigger_error($user->lang['NO_CATEGORY'] . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$sql = 'SELECT *
			FROM ' . CLASSIFIEDS_CATEGORY_TABLE . "
			WHERE id = $id";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if (!$row)
		{
			trigger_error($user->lang['NO_CATEGORY'] . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$move_category_name = move_category_by($row, $action);

		if ($move_category_name !== false)
		{
			add_log('admin', 'LOG_CLASSIFIEDS_' . strtoupper($action), $row['name'], $move_category_name);
			$cache->destroy('sql', CLASSIFIEDS_CATEGORY_TABLE);
		}

	break;
	
	case "newcat":
	
		$sql = 'SELECT MAX(right_id) AS right_id
				FROM ' . CLASSIFIEDS_CATEGORY_TABLE;
					
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$sql_ary['left_id'] = $row['right_id'] + 1;
		$sql_ary['right_id'] = $row['right_id'] + 2;

		$sql = 'INSERT INTO '.CLASSIFIEDS_CATEGORY_TABLE . $db->sql_build_array('INSERT', $sql_ary);
		$db->sql_query($sql);
			
		// log the action
		add_log('admin', 'LOG_CLASSIFIEDS_NEW_CATEGORY', $name);

	
	break;
	
	case "editcat":
	
		$sql = 'UPDATE '.CLASSIFIEDS_CATEGORY_TABLE.' SET ' . $db->sql_build_array('UPDATE', $sql_ary). ' WHERE id = '.$id;
		$db->sql_query($sql);
			
		// log the action
		add_log('admin', 'LOG_CLASSIFIEDS_EDIT_CATEGORY', $name);
	
	break;
	
	case "deletecat":
	
		// delete the category
		$sql = 'DELETE FROM '.CLASSIFIEDS_CATEGORY_TABLE.'
				WHERE id = '.$id;
					
		$db->sql_query($sql);
			
		// Now if there are any advertisements under the deleted category they need to be moved to cat 1 (cannot delete cat 1).
		$sql = 'UPDATE ' . CLASSIFIEDS_TABLE . ' 
				SET cat_id = 1 
				WHERE cat_id='.$id;
					
		$db->sql_query($sql);
			
		// log the action
		add_log('admin', 'LOG_CLASSIFIEDS_DELETE_CATEGORY');

	
	break;
	
	case "delete":
	
		$sql = 'DELETE FROM '.CLASSIFIEDS_TABLE.' WHERE ad_id='.$ad_id;
		$db->sql_query($sql);
		redirect($this->u_action);
		
	break;
	
	case "add_days":
	
			$number = request_var('add_days', 0);
		$id = request_var('ad_id', 0);
		$days = '+'.$number.'days';
		$expired = time();
		$expire = strtotime($days, $expired);
		
		
		$sql_ary = (array(

			'ad_expire'        			=> $expire,
			'expire_email'        		=> 0,

		));

		$sql = 'UPDATE '.CLASSIFIEDS_TABLE.' SET ' . $db->sql_build_array('UPDATE', $sql_ary). ' WHERE ad_id = '.$ad_id;
		$db->sql_query($sql);

		redirect($this->u_action);
		
		break;


}

if (!function_exists('mod_version_check'))
{
	include($phpbb_root_path . 'buysell/includes/functions_version_check.' . $phpEx);
}
mod_version_check();



// config settings
       		
switch($settings)
{

	case "settings":

		set_config('enable_classifieds', request_var('enable_classifieds', 0));
		set_config('disable_message', utf8_normalize_nfc(request_var('disable_message', '', true)));
		set_config('number_expire', request_var('number_expire', 0));
		set_config('allow_tinypic', request_var('allow_tinypic', 0));
		set_config('allow_comments', request_var('allow_comments', 0));
		set_config('allow_upload', request_var('allow_upload', 0));
		set_config('upload_size', request_var('upload_size', 0));
		set_config('minimum_title_length', request_var('minimum_title_length', 0));
	
		
	break;
	
	case "display":
	
		set_config('sold_color', request_var('sold_color', ''));
		set_config('closed_color', request_var('closed_color', ''));
		set_config('sort_active_first', request_var('sort_active_first', 0));
		set_config('number_ads', request_var('number_ads', 0));
		set_config('show_rules', request_var('show_rules', 0));
		set_config('show_full', request_var('show_full', 0));
	
		
	break;
	
	
	case "email":
	
	     // figure out if the pm_id user actually exists.
        $pm_id = request_var('pm_id', 0);
        $sql = 'SELECT user_id FROM ' . USERS_TABLE . ' WHERE user_id= '. $pm_id;
        $result = $db->sql_query($sql);
		$pm_user_id = ($db->sql_fetchfield('user_id')) ? true : false;
				
		if (!$pm_user_id)
		{
			trigger_error($user->lang['BAD_PM_ID']);
		} 

		set_config('email_ad', request_var('email_ad', 0));
		set_config('email_expire', request_var('email_expire', 0));
		set_config('pm_ad', request_var('pm_ad', 0));
		set_config('pm_expire', request_var('pm_expire', 0));
		set_config('pm_id', request_var('pm_id', 0));

	break;   
	
	case "paypal":
	
		set_config('enable_int', request_var('enable_int', 0));
		set_config('master_paypal', request_var('master_paypal', ''));
		set_config('ad_cost', request_var('ad_cost', ''));
		set_config('paypal_currency', request_var('paypal_currency', ''));
		set_config('enable_sandbox', request_var('enable_sandbox', 0));

		set_config('cost_10', request_var('cost_10', 0));
		set_config('cost_50', request_var('cost_50', 0));
		set_config('cost_100', request_var('cost_100', 0));
		set_config('cost_200', request_var('cost_200', 0));
		set_config('cost_300', request_var('cost_300', 0));
		set_config('cost_500', request_var('cost_500', 0));
	
	break; 
	
	case "award" :
	
	$awards = request_var('number_credits', 0);
	$user_to_award = request_var('award_user', 0);
	$send_email = request_var('send_user_email', '');
	$send_pm 	= request_var('send_user_pm', '');
	
	$sql = 'UPDATE ' . USERS_TABLE . ' SET classifieds_credits = classifieds_credits +' . $awards . ' WHERE user_id = ' . $user_to_award;
	$db->sql_query($sql);
	
	$sql =  'SELECT user_id, username, user_colour, classifieds_credits, user_lang, user_email, user_jabber, user_notify_type 
			FROM  '. USERS_TABLE .'
			WHERE user_id = ' . $user_to_award;
				
	$result	 = $db->sql_query($sql);
	$row = $db->sql_fetchrow( $result );
	$db->sql_freeresult($result);
	
	if ($send_email)
	{	

		$messenger = new messenger();

		$messenger->template('credits_awarded', $row['user_lang']);
    	$messenger->to($row['user_email'], $row['username']);
    	$messenger->im($row['user_jabber'], $row['username']);
    	$messenger->assign_vars(array(
        	'USERNAME'    	 => $row['username'],
        	'SITE_NAME'		 => $config['sitename'],
        	'TOTAL_CREDITS'	 => $row['classifieds_credits'],
        	'NUMBER_CREDITS' => $awards,
        	
    	));
    		
    	$messenger->send($row['user_notify_type']);
		$messenger->save_queue();
				
	}
	if($send_pm)
	{

		$send_from = $config['pm_id'];
		$my_subject	= $user->lang['AWARDED'];
		$message	= sprintf($user->lang['AWARD_MESSAGE'], $awards, $row['classifieds_credits']);

		$poll = $uid = $bitfield = $options = ''; 
		generate_text_for_storage($my_subject, $uid, $bitfield, $options, false, false, false);
		generate_text_for_storage($message, $uid, $bitfield, $options, true, true, true);

		$data = array( 
			'address_list'		=> array ('u' => array($user_to_award => 'to')),
			'from_user_id'		=> $send_from,
			'from_username'		=> $config['sitename'],
			'icon_id'			=> 0,
			'from_user_ip'		=> $user->data['user_ip'],
			'enable_bbcode'		=> true,
			'enable_smilies'	=> true,
			'enable_urls'		=> true,
			'enable_sig'		=> true,
			'message'			=> $message,
			'bbcode_bitfield'	=> $bitfield,
			'bbcode_uid'		=> $uid,

		
			);
				submit_pm('post', $my_subject, $data, false);
		}

	
	break;     		
          

}

				
$sql = 'SELECT * FROM ' . CLASSIFIEDS_CATEGORY_TABLE . ' ORDER BY left_id ASC ';
$result	 = $db->sql_query($sql);

while($row = $db->sql_fetchrow( $result )) 
{ 
		
	$url = $this->u_action . "&amp;id={$row['id']}";
			
	$template->assign_block_vars('cat',array(
				
		'NAME'			=> $row['name'],
		'ID'			=> $row['id'],
		'EDIT_CAT'  	=> $this->u_action . '&amp;action=editcat&amp;id=' . $row['id'],
		'DELETE_CAT' 	=> $this->u_action . '&amp;action=deletecat&amp;id=' . $row['id'],
		'U_MOVE_UP'		=> $url . '&amp;action=move_up',
		'U_MOVE_DOWN'	=> $url . '&amp;action=move_down',
		'PARENT'		=> $row['parent'],
		'PARENT_ID'		=> $row['parent_id'],
		'PARENT_CAT'	=> get_category_parent($row['parent_id'])

		));
}
	
$limit 	 =	10;
$start   = request_var('start', 0);
$pagination_url = $this->u_action;		
				
$sql_ary = array(
    'SELECT'    => 'a.* , u.user_id, u.username, u.user_colour',
    'FROM'      => array(
        USERS_TABLE         => 'u',
        CLASSIFIEDS_TABLE   => 'a',
    ),
    'WHERE'     => 'u.user_id = a.ad_poster_id AND a.ad_expire > ' . time() . ' ORDER BY a.ad_date DESC',
);

$sql = $db->sql_build_query('SELECT', $sql_ary);
			
$result	 = $db->sql_query_limit($sql, $limit, $start);
while($row = $db->sql_fetchrow( $result )) 
{ 
				
	$template->assign_block_vars('ad_active',array(
		'AD_ID'				=>		$row['ad_id'],
		'AD_TITLE'			=>		$row['ad_title'],
		'AD_DATE'			=>		$user->format_date($row['ad_date']),
		'AD_POSTER'			=>		$row['username'],
		'AD_POSTER_COLOR'	=> 		$row['user_colour'],
		'AD_STATUS'			=> 		$row['ad_status'],
		'DELETE_LINK'		=>		$this->u_action . '&amp;action=delete&amp;ad_id= ' . $row['ad_id'],
		'AD_EXPIRE'			=> 		$user->format_date($row['ad_expire']),
		'EXPIRE'			=> 		$row['ad_expire'],
		'EDIT_EXPIRE'		=> 		$this->u_action . '&amp;action=add_days&amp;ad_id=' . $row['ad_id'] . '&amp;ad_expire=' . $row['ad_expire'],

					
		));
}
	
	$sql_ary['SELECT'] = 'COUNT(a.ad_id) as total_ads_active';
	$sql = $db->sql_build_query('SELECT', $sql_ary);
	$result = $db->sql_query($sql);
	$total_ads_active = $db->sql_fetchfield('total_ads_active');

	$db->sql_freeresult($result);

	$template->assign_vars(array(
    	'PAGINATION_ACTIVE'        => generate_pagination($pagination_url, $total_ads_active, $limit, $start),
    	'PAGE_NUMBER_ACTIVE'       => on_page($total_ads_active, $limit, $start),
    	'TOTAL_ADS_ACTIVE'       => $total_ads_active,
	));



	$sql_ary2 = array(
    	'SELECT'    => 'a.* , u.user_id, u.username, u.user_colour',
    	'FROM'      => array(
        	USERS_TABLE         => 'u',
        	CLASSIFIEDS_TABLE   => 'a',
    	),
    	'WHERE'     => 'u.user_id = a.ad_poster_id and a.ad_expire <'.time().' ORDER BY a.ad_date DESC',
	);
	$sql = $db->sql_build_query('SELECT', $sql_ary2);
			
		
		$result	 = $db->sql_query_limit($sql, $limit, $start);
		while($row = $db->sql_fetchrow( $result )) 
		{ 
				
				$template->assign_block_vars('ad_expired',array(
					'AD_ID'			=>		$row['ad_id'],
					'AD_TITLE'		=>		$row['ad_title'],
					'AD_DATE'		=>		$user->format_date($row['ad_date']),
					'AD_POSTER'		=>		$row['username'],
					'AD_POSTER_COLOR'	=> $row['user_colour'],
					'AD_STATUS'			=> $row['ad_status'],
					'DELETE_LINK'		=>	$this->u_action . '&amp;action=delete&amp;ad_id='.$row['ad_id'],
					'AD_EXPIRE'			=> $user->format_date($row['ad_expire']),
					'EXPIRE'			=> $row['ad_expire'],
					'EDIT_EXPIRE'	=> $this->u_action . '&amp;action=add_days&amp;ad_id='.$row['ad_id'].'&amp;ad_expire='.$row['ad_expire'],

					
		));
		}
		
	$sql_ary2['SELECT'] = 'COUNT(a.ad_id) as total_ads_expire';
	$sql = $db->sql_build_query('SELECT', $sql_ary2);
	
	$result = $db->sql_query($sql);
	$total_ads_expire = $db->sql_fetchfield('total_ads_expire');
	$db->sql_freeresult($result);

	$template->assign_vars(array(
    	'PAGINATION_EXPIRE'        => generate_pagination($pagination_url, $total_ads_expire, $limit, $start),
    	'PAGE_NUMBER_EXPIRE'       => on_page($total_ads_expire, $limit, $start),
    	'TOTAL_ADS_EXPIRE'         => $total_ads_expire,
	));

		
	
	$sql = 'SELECT username, user_colour 
			FROM ' . USERS_TABLE . ' 
			WHERE user_id = ' . intval($config['pm_id']);
			
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
		

		
	$template->assign_vars(array(
		'U_NEW_CAT'					=> $this->u_action . '&amp;action=newcat',
		'U_ACTION_SETTINGS'			=> $this->u_action . '&amp;settings=settings',	
		'U_ACTION_PAYPAL'			=> $this->u_action . '&amp;settings=paypal',	
		'U_ACTION_EMAIL'			=> $this->u_action . '&amp;settings=email',	
		'U_ACTION_DISPLAY'			=> $this->u_action . '&amp;settings=display',	
		'U_ACTION_AWARD'			=> $this->u_action . '&amp;settings=award',	
		'ENABLE_CLASSIFIEDS'		=> $config['enable_classifieds'],
		'DISABLE_MESSAGE'			=> $config['disable_message'],
		'NUMBER_ADS'				=> $config['number_ads'],
		'NUMBER_EXPIRE'				=> $config['number_expire'],
		'EMAIL_AD'					=> $config['email_ad'],	
		'EMAIL_EXPIRE'				=> $config['email_expire'],
		'PM_AD'						=> $config['pm_ad'],			
		'PM_EXPIRE'					=> $config['pm_expire'],	
		'SHOW_RULES'				=> $config['show_rules'],
		'PM_ID'						=> $config['pm_id'],
		'ALLOW_TINYPIC'				=> $config['allow_tinypic'],
		'CLOSED_COLOR'				=> $config['closed_color'],		
		'SOLD_COLOR'				=> $config['sold_color'],
		'PM_USER'					=> $row['username'],
		'USER_COLOR'				=> $row['user_colour'],	
		'ALLOW_COMMENTS'			=> $config['allow_comments'],		
		'ALLOW_UPLOAD'				=> $config['allow_upload'],		
		'UPLOAD_SIZE'				=> $config['upload_size'],	
		'SORT_ACTIVE_FIRST'			=> $config['sort_active_first'],
		'MINIUM_TITLE_LENGTH'		=> $config['minimum_title_length'],
		'MASTER_PAYPAL'				=> $config['master_paypal'],
		'AD_COST'					=> $config['ad_cost'],
		'PAYPAL_CURRENCY'			=> $config['paypal_currency'],
		'ENABLE_INT'				=> $config['enable_int'],
		'ENABLE_SANDBOX'			=> $config['enable_sandbox'],
		'COST_10'					=> $config['cost_10'],
		'COST_50'					=> $config['cost_50'],
		'COST_100'					=> $config['cost_100'],
		'COST_200'					=> $config['cost_200'],
		'COST_300'					=> $config['cost_300'],
		
		'COST_500'					=> $config['cost_500'],
		'SHOW_FULL'					=> $config['show_full'],
	
		'PAYPAL_FILE'				=> file_exists($phpbb_root_path . 'buysell/paypal.' . $phpEx),
					
	));
	

	
	

	}
}

?>