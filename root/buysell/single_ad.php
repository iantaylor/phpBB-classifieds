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
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'buysell/includes/functions_buysell.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/classified');
$user->add_lang('posting');

page_header($user->lang('PAGE_CLASSIFIED'));
$id = request_var('ad_id', 0);
$mode = request_var('mode', '');

$template->set_filenames(array(
    	'body' => 'classified_single_body.html',
));
	
if (!$auth->acl_get('u_view_classifieds'))
{
	trigger_error('NOT_AUTHORISED');
}

$template->assign_vars(array(	
												   
	'U_ACTION'			=> append_sid("{$phpbb_root_path}buysell/new_ad.$phpEx", "mode=newad"),
	'ALL_LINK'			=> append_sid($phpbb_root_path . 'buysell'),
	'CAN_EDIT'			=> $auth->acl_get('u_edit_own_classifieds'),
	'CAN_DELETE'		=> $auth->acl_get('u_can_delete_classifieds'),
	'CLOSED_COLOR'		=> $config['closed_color'],
	'SOLD_COLOR'		=> $config['sold_color'],
	'NEW_AD'			=> append_sid($phpbb_root_path . "buysell/new_ad.$phpEx"),
	'COMMENT'			=> append_sid($phpbb_root_path . "buysell/class_comment.$phpEx","mode=new_comment&amp;ad_id=".$id),
	'S_BBCODE_ALLOWED' 	=> true,
	'ACTIVE_LINK'		=>	append_sid($phpbb_root_path . 'buysell/?mode=active'),
	'CATEGORIES'			=> build_categories(),


));

// Update the views for the ad.
$sql = 'UPDATE ' . CLASSIFIEDS_TABLE . ' SET ad_views = ad_views +1 WHERE ad_id = '.$id;
$db->sql_query($sql);

$sql_ary = array(
	'SELECT'    => 'c.*, a.*, u.user_id, u.username, u.user_colour, u.user_from, u.user_aim, u.user_msnm, u.user_yim, u.user_jabber, u.user_email',
    'FROM'      => array(
        USERS_TABLE        			=> 'u',
        CLASSIFIEDS_TABLE   		=> 'a',
        CLASSIFIEDS_CATEGORY_TABLE 	=> 'c',
    ),
    'WHERE'     => 'u.user_id = a.ad_poster_id and ad_id = '.$id.' and a.cat_id = c.id ORDER BY a.ad_date DESC',
);
$sql = $db->sql_build_query('SELECT', $sql_ary);		
	
							
$result	 = $db->sql_query($sql);
$row = $db->sql_fetchrow( $result );
$db->sql_freeresult($result);
			
$row['bbcode_options'] = (($row['enable_bbcode']) ? OPTION_FLAG_BBCODE : 0) +
    (($row['enable_smilies']) ? OPTION_FLAG_SMILIES : 0) + 
    (($row['enable_magic_url']) ? OPTION_FLAG_LINKS : 0);
$description = generate_text_for_display($row['ad_description'], $row['bbcode_uid'], $row['bbcode_bitfield'], $row['bbcode_options']);

if(time() > $row['ad_expire'] && $mode != 'delete'){
		
	trigger_error('AD_EXPIRED');
}
		
				
$user_id = $row['user_id'];
$template->assign_vars(array(
				
	'LOCATION'			=>	$row['user_from'],
	'AD_TITLE'			=>	censor_text($row['ad_title']),
	'AD_PRICE'			=>	$row['ad_price'],
	'CAT_NAME'			=>	$row['name'],
	'AD_DATE'			=>	$user->format_date($row['ad_date']),
	'AD_POSTER'			=>	get_username_string('full', $row['user_id'],$row['username'], $row['user_colour']),
	'AD_POSTER_COLOR'	=> 	$row['user_colour'],
	'USER_LINK' 		=>	append_sid($phpbb_root_path . 'memberlist.' . $phpEx ,'mode=viewprofile&amp;u='.$row['user_id']),
	'AD_DESCRIPTION'	=> 	censor_text($description),
	'AD_STATUS'			=> 	$row['ad_status'],
	'U_PM'				=> 	append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=pm&amp;mode=compose&amp;u='.$row['user_id']),
	'DELETE_LINK'		=>	append_sid("{$phpbb_root_path}buysell/single_ad.$phpEx", 'mode=delete&amp;ad_id='.$row['ad_id']),
	'EDIT_LINK'			=>	append_sid("{$phpbb_root_path}buysell/new_ad.$phpEx", 'mode=edit&amp;ad_id='.$row['ad_id']),
	'POSTER'			=>  $row['ad_poster_id'],
	'USER_AIM'			=>	$row['user_aim'],
	'USER_MSN'			=>	$row['user_msnm'],
	'USER_YIM'			=>	$row['user_yim'],
	'USER_JABBER'		=>	$row['user_jabber'],
	'U_AIM'				=> ($row['user_aim'] && $auth->acl_get('u_sendim')) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=contact&amp;action=aim&amp;u=$user_id") : '',
	'U_EMAIL'			=> ($config['board_email_form'] && $config['email_enable']) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=email&amp;u=' . $user_id) : (($config['board_hide_emails'] && !$auth->acl_get('a_user')) ? '' : 'mailto:' . $row['user_email']),
	'U_MSN'				=> ($row['user_msnm'] && $auth->acl_get('u_sendim')) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=contact&amp;action=msnm&amp;u=$user_id") : '',
	'U_YIM'				=> ($row['user_yim']) ? 'http://edit.yahoo.com/config/send_webmesg?.target=' . urlencode($row['user_yim']) . '&amp;.src=pg' : '',
	'U_JABBER'			=> ($row['user_jabber'] && $auth->acl_get('u_sendim')) ? append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=contact&amp;action=jabber&amp;u=$user_id") : '',
	'ALLOW_COMMENTS'	=> $row['allow_comments'],
	'COMMENTS_ENABLED'	=> $config['allow_comments'],
	'PHONE'				=> $row['phone'],
	'PAYPAL'			=> $row['paypal'],
	'PAYPAL_CURRENCY'	=> $row['paypal_currency'],
	'L_LAST_EDIT'		=> sprintf($user->lang['LAST_EDIT'], $user->format_date($row['edit_time']), $row['last_edit_by']),
	'LAST_EDIT'			=> ($row['edit_time']) ? true : false,

));
				
$poster = $row['ad_poster_id'];
$mode = request_var('mode', '');
$id = request_var('ad_id', 0);
		
if($mode == 'delete' && $user->data['user_id'] == $row['ad_poster_id'] || $mode == 'delete' && $auth->acl_get('a_'))
{
		
	if ($auth->acl_get('u_can_delete_classifieds'))
	{

		if (confirm_box(true))
		{
			$sql = 'DELETE FROM '.CLASSIFIEDS_TABLE.' WHERE ad_id='.$id;
			$db->sql_query($sql);
			redirect(append_sid("{$phpbb_root_path}/buysell"));
		}
		else
		{

			confirm_box(false, $user->lang['DELETE_CONFIRM']);
					
		}
	}
	else
	{
	 	trigger_error('NOT_AUTHORISED');
	}
	
	
}	

	
	// Do some commenting 
	
	$userid = $user->data['user_id'];
	$id = request_var('ad_id', 0);
	
	
	$sql_ary = array(
	'SELECT'    => 'u.username, u.user_avatar,u.user_avatar_type, u.user_avatar_width, u.user_avatar_height,u.user_colour,c.*',
    'FROM'      => array(
        USERS_TABLE        			=> 'u',
        CLASSIFIEDS_COMMENTS_TABLE 	=> 'c',
    ),
    'WHERE'     => 'u.user_id = c.comment_poster_id AND c.ad_id = '.$id.' ORDER BY c.comment_id DESC',
);
$sql = $db->sql_build_query('SELECT', $sql_ary);		


$result = $db->sql_query($sql);			
while($row = $db->sql_fetchrow( $result )) 
{

	$comment_date 	= $user->format_date($row['comment_date']);
	$comment_text 	= $row['comment_text'];
	$comment_id 	= $row['comment_id'];
	$comment_author_id = $row['comment_poster_id'];
	$row['bbcode_options'] = (($row['enable_bbcode']) ? OPTION_FLAG_BBCODE : 0) +
    					(($row['enable_smilies']) ? OPTION_FLAG_SMILIES : 0) + 
    					(($row['enable_magic_url']) ? OPTION_FLAG_LINKS : 0);
	$comment_text_format = generate_text_for_display($row['comment_text'], $row['bbcode_uid'], $row['bbcode_bitfield'], $row['bbcode_options']);
	
				
	$template->assign_block_vars('comment',array(	
												   
			'COMMENT_DATE'		=> $comment_date,
			'COMMENT_TEXT'		=> $comment_text_format,
			'COMMENT_AVATAR'	=>get_user_avatar($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height']),
			'USERNAME'			=> get_username_string('full', $comment_author_id, $row['username'], $row['user_colour']),
			'U_DELETE_COMMENT'	=> append_sid($phpbb_root_path . "buysell/class_comment.$phpEx","mode=delete&amp;comment=".$comment_id."&amp;p=".$comment_author_id."&amp;ad_id=".$id),
		
			
		));
}
page_footer();
?>