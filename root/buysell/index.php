<?php
/**
*
* @package Classified mod
* @version $Id: 0.1.0
* @copyright Ian Taylor
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/


define('IN_PHPBB', true);

$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);
include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);
include($phpbb_root_path . 'buysell/includes/functions_buysell.' . $phpEx);


// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/classified');

page_header($user->lang('PAGE_CLASSIFIED'));


$template->set_filenames(array(
    	'body' => 'classified_index_body.html',
));

if (!$auth->acl_get('u_view_classifieds'))
{
	trigger_error('NOT_AUTHORISED');
}


$limit = intval($config['number_ads']);
$start   = request_var('start', 0);
	
// Needed Variables
$userid = $user->data['user_id'];
$template->assign_vars(array(
	
	'ALL_LINK'				=>	append_sid($phpbb_root_path . 'buysell'),
	'ACTIVE_LINK'			=>	append_sid($phpbb_root_path . 'buysell/?mode=active'),
	'ENABLE_CLASSIFIEDS'	=> $config['enable_classifieds'],
	'DISABLE_MESSAGE'		=> $config['disable_message'],
	'NEW_AD'				=> append_sid($phpbb_root_path . 'buysell/new_ad.php'),
	'OWN_LINK'				=> append_sid($phpbb_root_path . 'buysell/?mode=viewown'),	
	'U_SEARCH_ADS'			=> append_sid($phpbb_root_path . 'buysell/?mode=search'),
	'U_VIEW_EXPIRED'		=> append_sid($phpbb_root_path . 'buysell/?mode=view_expired'),
	'CATEGORIES'			=> build_categories(),
	'SHOW_RULES'			=> $config['show_rules'],
	'CLOSED_COLOR'			=> $config['closed_color'],
	'SOLD_COLOR'			=> $config['sold_color'],
	'USER_AD_STATS'			=> user_total_ads($userid),
	'LAST_POST_IMG'			=> $user->img('icon_topic_latest', 'VIEW_LATEST_POST'),
	'ENABLE_COMMENT'		=> $config['allow_comments'],
	'MODE'					=> request_var('mode', ''),
	'CAN_DELETE'			=> $auth->acl_get('u_can_delete_classifieds'),

						
));


// Figure out if there is a category selected, if not display all the results
$cat = request_var('id', 0);
$mode = request_var('mode','');
$profile_user = request_var('user', 0);
$search = request_var('question', '');
$trimmed = $db->sql_escape(strtolower($search));
$order_by = ($config['sort_active_first']) ? 'a.ad_status, a.ad_date DESC' : 'a.ad_date DESC';
$pagination_url = append_sid($phpbb_root_path . 'buysell/index.' . $phpEx);	

		
$sql_ary =  array(
	'SELECT'	=> ' a.*, u.user_id, u.username, u.user_colour, u.user_from',
	'FROM'		=> array(
		CLASSIFIEDS_TABLE				=> 'a',
	),
	'LEFT_JOIN'	=> array(
		array(
			'FROM'	=> array(USERS_TABLE => 'u'),
			'ON'	=> 'u.user_id = a.ad_poster_id',		
		)
	),
	'WHERE'		=> 'a.ad_poster_id = u.user_id AND a.ad_expire > '.time(),
	'ORDER_BY'	=> $order_by
);

$sql = $db->sql_build_query('SELECT', $sql_ary);

switch ($mode)
{

	// Mode to view ad's under the clicked category
	case "cat":
	
		// if the clicked link is just a category search for c.id
		if ($cat)
		{
		
			$where = 'a.ad_poster_id = u.user_id AND c.id = a.cat_id and c.id = '. $cat .' AND a.ad_expire > '.time();
		
		}
		// If the clicked link is a parent search for parent_id
		elseif(request_var('parent_id', 0))
		{
		
			$where = 'a.ad_poster_id = u.user_id AND c.id = a.cat_id and c.parent_id = '. request_var('parent_id', 0) .' AND a.ad_expire > '. time();
		
		}

		$sql_ary['FROM']	= array(
				CLASSIFIEDS_TABLE				=> 'a',
				CLASSIFIEDS_CATEGORY_TABLE		=> 'c',);
		
		$sql_ary['WHERE']	= $where;			
		

		$pagination_url = append_sid($phpbb_root_path . 'buysell/index.' . $phpEx . '?mode=cat&amp;id='.request_var('id', 0));	

			
	break;

	// Mode to view a users own ad's probably going to remove this and just use "viewuser"
	case "viewown":
	
		$sql_ary['WHERE']	= 'a.ad_poster_id = u.user_id AND a.ad_poster_id = '. $db->sql_escape($user->data['user_id']) .' AND a.ad_expire > '. time();
		
	

		$pagination_url = append_sid($phpbb_root_path . 'buysell/index.' . $phpEx . '?mode=viewown');	

	break;
	
	// Mode to view Active ad's only, same Query different WHERE
	case "active":
	
		$sql_ary['WHERE']	= 'a.ad_poster_id = u.user_id AND a.ad_status = 0 AND a.ad_expire > '. time();
		
		
		$pagination_url = append_sid($phpbb_root_path . 'buysell/index.' . $phpEx . '?mode=active');	

			
	break;
	
	// Mode to view a specific users ad's, same Query different WHERE
	case "viewuser":
	
		$sql_ary['WHERE']	= 'a.ad_poster_id = u.user_id AND  a.ad_poster_id = '. $db->sql_escape($profile_user) .' AND a.ad_expire > '.time();
		
	
	$pagination_url = append_sid($phpbb_root_path . 'buysell/index.' . $phpEx . '?mode=viewuser&amp;user='. request_var('user', 0));	


	break;
	
	// Mode to view a expired ad's, same Query different WHERE
	case "view_expired":
		
		$sql_ary['WHERE']	= 'a.ad_poster_id = u.user_id AND a.ad_poster_id = '. $db->sql_escape($user->data['user_id']) .' AND a.ad_expire < '. time();
		

		$pagination_url = append_sid($phpbb_root_path . 'buysell/index.' . $phpEx . '?mode=view_expired');	

	break;
	
	// Simple mode for searching
	case "search":
		
		$sql_ary['WHERE']	= "LOWER(a.ad_description) ".$db->sql_like_expression(str_replace('*', $db->any_char, $trimmed))."
    		OR LOWER(a.ad_title) ".$db->sql_like_expression(str_replace('*', $db->any_char, $trimmed))."
    		OR u.username ".$db->sql_like_expression(str_replace('*', $db->any_char, $trimmed))."";
    		
    	$sql_ary['ON']	= "u.user_id = a.ad_poster_id AND a.ad_expire >".time();

		
 		$pagination_url = append_sid($phpbb_root_path . 'buysell/index.' . $phpEx . '?mode=search');	
   	
		break;


}		
	// Build the query
	$sql = $db->sql_build_query('SELECT', $sql_ary);
	
	// get the results
	$result	 = $db->sql_query_limit($sql, $limit, $start);
	

	//Loop though the results
	while($row = $db->sql_fetchrow( $result )) 
	{ 

			
		$row['bbcode_options'] = (($row['enable_bbcode']) ? OPTION_FLAG_BBCODE : 0) +
    (($row['enable_smilies']) ? OPTION_FLAG_SMILIES : 0) + 
    (($row['enable_magic_url']) ? OPTION_FLAG_LINKS : 0);
			$text_format = generate_text_for_display($row['ad_description'], $row['bbcode_uid'], $row['bbcode_bitfield'], $row['bbcode_options']);
				
				$strip_format = strip_bbcode($text_format);
				
				$template->assign_block_vars('ad',array(
					'AD_VIEWS'		=>		$row['ad_views'],
					'AD_TITLE'		=>		censor_text($row['ad_title']),
					'AD_PRICE'		=>		$row['ad_price'],
					'AD_DATE'		=>		$user->format_date($row['ad_date']),
					'AD_POSTER'		=>		$row['username'],
					'AD_POSTER_COLOR'	=> $row['user_colour'],
					'USER_LINK' 		=>	append_sid($phpbb_root_path . 'memberlist.' . $phpEx ,'mode=viewprofile&amp;u='.intval($row['user_id'])),
					'AD_LINK' 		=>	append_sid($phpbb_root_path . 'buysell/single_ad.' . $phpEx ,'ad_id='.intval($row['ad_id'])),
					'AD_DESCRIPTION'	=>  censor_text(truncate_string($text_format,100)),
					'AD_STATUS'			=> $row['ad_status'],
					'THUMB'				=> $row['thumb'],
					'LOCATION'			=> $row['user_from'],
					'ALLOW_COMMENTS'	=> $row['allow_comments'],
					'TOTAL_COMMENTS'	=> total_comments($row['ad_id']),
					'CATEGORY'			=> get_ad_category($row['cat_id']),
					'U_EXTEND'		=> append_sid($phpbb_root_path . 'buysell/new_ad.' . $phpEx ,'mode=extend_ad&amp;ad_id='.intval($row['ad_id'])),
					'U_DELETE'		=>	append_sid("{$phpbb_root_path}buysell/single_ad.$phpEx", 'mode=delete&amp;ad_id='.$row['ad_id']),

		));
	}
	//set them free
	$db->sql_freeresult($result);

			$sql_ary['SELECT']	 = 'COUNT(ad_id) as all_ads';
			$sql = $db->sql_build_query('SELECT', $sql_ary);
			
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);
			
			$total_ads = $row['all_ads'];
			
			$template->assign_vars(array(
    			'PAGINATION'        => generate_pagination($pagination_url, $total_ads, $limit, $start),
    			'PAGE_NUMBER'       => on_page($total_ads, $limit, $start),
    			'TOTAL_ADS'         => ($total_ads == 1) ? $user->lang['LIST_AD'] : sprintf($user->lang['LIST_ADS'], $total_ads),
));
		
		
			
				
			// pagination 
			

// send email when ad has expired
$sql =  'SELECT a.* , u.user_id, u.username, u.user_colour, u.user_lang, u.user_email, u.user_jabber, u.user_notify_type from  '. USERS_TABLE .' u , '. CLASSIFIEDS_TABLE .' a
				WHERE u.user_id = a.ad_poster_id and '.time().' > a.ad_expire and a.expire_email = 0 and a.ad_status = 0';
				
$result	 = $db->sql_query($sql);
$row 	 = $db->sql_fetchrow( $result );
$db->sql_freeresult($result);
			
if($row['ad_expire'])
{
			
	if($config['email_expire'])
	{
		$messenger = new messenger();
		$messenger->template('ad_expired', $row['user_lang']);
   	 	$messenger->to($row['user_email'], $row['username']);
    	$messenger->im($row['user_jabber'], $row['username']);
    	$messenger->assign_vars(array(
       		'USERNAME'   	=> $row['username'],
        	'TITLE'			=> $row['ad_title'],
        	'AD_DATE'		=> $user->format_date($row['ad_date']),
			'PRICE'			=> $row['ad_price'],
        	'EXPIRE_DATE'   => $user->format_date($row['ad_expire']),
        	'SITE_NAME'		=> $config['sitename'],
    	));
    	$messenger->send($row['user_notify_type']);
		$messenger->save_queue();
		
	}

	if($config['pm_expire'])
	{

	
		$my_subject	= $user->lang('NEW_EXPIRED');
		$message	= $row['username']." ".$user->lang('YOUR_AD'). "<b> ".$user->format_date($row['ad_expire']). "</b> \n ".$user->lang('ASSUME'). "<b> \n".$user->lang('TITLE').": <b>".$row['ad_title']."</b> \n".$user->lang('PRICE'). "<b>".$row['ad_price']. "</b>";

		$poll = $uid = $bitfield = $options = ''; 
generate_text_for_storage($my_subject, $uid, $bitfield, $options, false, false, false);
generate_text_for_storage($message, $uid, $bitfield, $options, true, true, true);

		$data = array( 
			'address_list'		=> array ('u' => array(2 => 'to')),
			'from_user_id'		=> 2,
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
	

	
	$sql = 'UPDATE '.CLASSIFIEDS_TABLE. ' SET expire_email = 1 WHERE ad_id ='.$db->sql_escape($row['ad_id']);
	$result = $db->sql_query($sql);
		
}
	

page_footer();


?>