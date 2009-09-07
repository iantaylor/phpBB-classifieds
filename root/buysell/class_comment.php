<?php
/**
*
* @author Ian Taylor, Platinum2007 iantaylor603@gmail.com - http://street-steeze.com
*
* @package Classifieds
* @version $id
* @copyright (c) Street Steeze, Ian-Taylor.ca street-steeze.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include( $phpbb_root_path . 'includes/functions_messenger.' .$phpEx );

$user->session_begin();
$auth->acl($user->data);
$user->setup();

$id = request_var('ad_id', 0);
$mode = request_var('mode', '');
$poster = request_var('p', 0);
$comment = request_var('comment', 0);

switch($mode)
{

	case "new_comment":
	
		$comment_time =  time();
		$comment_poster_id = $user->data['user_id'];
		$comment_text = request_var('comment_text','', true);
		$ad_id 			= request_var('ad_id', 0);
		$uid = $bitfield = $options = ''; // will be modified by generate_text_for_storage
		$allow_bbcode = $allow_urls = $allow_smilies = true;

		generate_text_for_storage($comment_text, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);

		$sql_ary = (array(
    		'comment_date'       		=> $comment_time,
			'comment_poster_id'         => $comment_poster_id,
			'comment_text'              => $comment_text,
    		'bbcode_uid'        		=> $uid,
    		'bbcode_bitfield'   		=> $bitfield,
   			'bbcode_options'    		=> $options,
   			'ad_id'						=> $ad_id,
	
		));


		$sql = 'INSERT INTO ' . CLASSIFIEDS_COMMENTS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_ary);
		$db->sql_query($sql);
	
		$sql_ary =  array(
			'SELECT'	=> ' a.*, u.user_id, u.username, u.user_colour, u.user_from, u.user_colour, u.user_lang, u.user_email, u.user_jabber, u.user_notify_type',
			'FROM'		=> array(
				CLASSIFIEDS_TABLE				=> 'a',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'u.user_id = a.ad_poster_id',		
				)
			),
			'WHERE'		=> 'a.ad_id ='.$ad_id.' AND u.user_id = a.ad_poster_id',
		);

		$sql = $db->sql_build_query('SELECT', $sql_ary);
		
		$result	 = $db->sql_query($sql);

		$row = $db->sql_fetchrow($result);
	
		// Send a email to the ad poster if he/she wants to be notified 
		if($row['notify_comments'])
		{
			$messenger = new messenger();

			$messenger->template('classifieds_comment', $row['user_lang']);
    		$messenger->to($row['user_email'], $row['username']);
    		$messenger->im($row['user_jabber'], $row['username']);
    		$messenger->assign_vars(array(
        		'USERNAME'    	 => $row['username'],
        		'SITE_NAME'		 => $config['sitename'],
        		'AD_LINK'		 => generate_board_url()."/buysell/single_ad.$phpEx"."?ad_id=".$ad_id,
        		'SIGNATURE'		 => $config['board_email_sig'],	
    		));
    		$messenger->send($row['user_notify_type']);
			$messenger->save_queue();	
		}

		redirect(append_sid("{$phpbb_root_path}buysell/single_ad.$phpEx","ad_id=".$id));

	break;

	case "delete":
	
		if($user->data['is_registered'] && ($auth->acl_getf_global('m_') || $auth->acl_get('a_') || $user->data['user_id'] == $poster))
		{
	
			$sql = 'DELETE 
					FROM '. CLASSIFIEDS_COMMENTS_TABLE .
					" WHERE comment_id=". intval($comment);
			$db->sql_query($sql);
			$id = request_var('ad_id', 0);
			redirect(append_sid("{$phpbb_root_path}buysell/single_ad.$phpEx","ad_id=".$id));
	
		}
		else
		{
	    	trigger_error('NOT_AUTHORISED');
		}
	
	break;	

}

?>