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
include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);
include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/classified');
$user->add_lang('posting');
page_header($user->lang('PAGE_CLASSIFIED'));

$template->set_filenames(array(
    	'body' => 'classified_ad_body.html',
	));
	
if (!$auth->acl_get('u_post_classifieds'))
{	
     trigger_error('NOT_AUTHORISED');
}



		
$edit = request_var('mode', '');
$id = request_var('ad_id', 0);
$error = array();

/*
* The below code is to be used with the paypal integration script that is not provided with the mod.
* If phpBB classifieds-paypal integration is install this will check if the user has credits, if not he/she will be redirected to the credit purchase form.
*/
;
		


if(!$user->data['classifieds_credits'] && $edit != 'edit' && file_exists($phpbb_root_path . 'buysell/paypal.' . $phpEx) && $config['enable_int'])
{

	redirect(append_sid("{$phpbb_root_path}buysell/paypal.$phpEx"));

}
/*
* END special code.
*/

$template->assign_vars(array(	
												   
	'U_ACTION'			=>	append_sid("{$phpbb_root_path}buysell/new_ad.$phpEx", "mode=newad"),
	'S_BBCODE_ALLOWED' 	=> true,
	'ALLOW_TINYPIC'		=> $config['allow_tinypic'],
	'NEW_AD'			=> $edit,
	'ALLOW_UPLOAD'		=> $config['allow_upload'],
	'UPLOAD_SIZE'		=> $config['upload_size'],
	'MODE'				=> $edit,
	'COMMENTS_ENABLED'	=> $config['allow_comments'],

));

$sql = 'SELECT * 
		FROM ' . CLASSIFIEDS_CATEGORY_TABLE . ' 
		ORDER BY left_id ASC';
		
$result	 = $db->sql_query($sql);

while($row = $db->sql_fetchrow( $result ))
{ 
		
	$template->assign_block_vars('cat',array(
		
		'NAME'		=>	$row['name'],
		'ID'		=>	$row['id'],
		'PARENT'	=> 	$row['parent'],

	));
}

switch($edit)
{
	case 'newad' :

		$now 		= time();
		$days 		= '+' . $config['number_expire'] . 'days';
		$expire 	= strtotime($days, $now);
		$status 	= request_var('ad_status', 0);
		$ad_title	= utf8_normalize_nfc(request_var('ad_title', '', true));
		$ad_description 	= utf8_normalize_nfc(request_var('message', '', true));
		$price 				= request_var('ad_price','', true);
		$uid = $bitfield = $options = ''; // will be modified by generate_text_for_storage
		$allow_bbcode = $allow_urls = $allow_smilies = true;
		$cat 				= request_var('cat', '', true);
		$allow_comments 	= request_var('allow_comments', 0);
		$notify_comments 	= request_var('notify_comments', 0);
		$thumb 		= request_var('thumb', '');
		$phone 		= request_var('phone', '');
		$paypal 	= request_var('paypal', '');
		$paypal_currency = request_var('paypal_currency', '');


		$sql = 'SELECT * 
				FROM ' . CLASSIFIEDS_CATEGORY_TABLE . ' 
				WHERE id =' . $cat;
				
		$result	 = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		
		// check for errors	
		if (!$ad_title)
		{
   	 		$error[] = $user->lang['NO_TITLE'];
		}
		// 1=yes
		if ($row['parent'])
		{
			$error[]	= $user->lang['BAD_CATEGORY'];
		}
		if (strlen($ad_title) < $config['minimum_title_length'])
		{
			$error[]	= sprintf($user->lang['NO_TITLE_TOO_SHORT'], $config['minimum_title_length']);
		}
		if (!@fopen($thumb, "r") && $thumb)
		{
			$error[]	= $user->lang['BAD_THUMB_URL'];
		}
		if (!preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $thumb) && $thumb)
		{
			$error[]	= $user->lang['BAD_THUMB_URL'];
		}
		if (!$ad_description)
		{
			$error[] = $user->lang['NO_DESCRIPTION'];
		}
		if (!$price)
		{
    		$error[] = $user->lang['NO_PRICE'];
		}
		if (!preg_match("/^[0-9\.,]+$/", $price))
		{
		
			$error[] = $user->lang['ONLY_NUMBERS'];
		
		}
		
					
			/*
			* The below code is to be used with the paypal integration script that is not provided with the mod.
			* This will remove 1 credit from the users credits after posting his/her advertisement.			
			*/
			if(file_exists($phpbb_root_path . 'buysell/paypal.' . $phpEx) && $config['enable_int'])
			{
			
				$remove = 1;
			
				if($price >= 11 && $price <= 50)
				{
				
					if ($user->data['classifieds_credits'] < $config['cost_50'])
					{
				
						$error[]	= $user->lang['NEED_MORE_CREDITS'];
						
					}
					else
					{
					
						$remove = $config['cost_50'];
					
					}
				
				}
				if($price >= 51 && $price <= 100)
				{
				
					if ($user->data['classifieds_credits'] < $config['cost_100'])
					{
				
						$error[]	= $user->lang['NEED_MORE_CREDITS'];
						
					}
					else
					{
					
						$remove = $config['cost_100'];
					
					}
				}
				if($price >= 101 && $price <= 200)
				{
				
					if ($user->data['classifieds_credits'] < $config['cost_200'])
					{
				
						$error[]	= $user->lang['NEED_MORE_CREDITS'];
						
					}
					else
					{
					
						$remove = $config['cost_200'];
					
					}
				}
				if($price >= 201 && $price <= 500)
				{
				
					if ($user->data['classifieds_credits'] < $config['cost_300'])
					{
				
						$error[]	= $user->lang['NEED_MORE_CREDITS'];
						
					}
					else
					{
					
						$remove = $config['cost_300'];
					
					}
				}
				
				if($price > 500)
				{
				
					if ($user->data['classifieds_credits'] < $config['cost_500'])
					{
				
						$error[]	= $user->lang['NEED_MORE_CREDITS'];
						
					}
					else
					{
					
						$remove = $config['cost_500'];
					
					}
				
				}
				
				if (!sizeof($error))
				{
					$sql = 'UPDATE ' . USERS_TABLE . ' 
							SET classifieds_credits = classifieds_credits -' . intval($remove) . ' 
							WHERE user_id= ' . intval($user->data['user_id']);
					$db->sql_query($sql);
				
				}
				
			
					
			}
			/*
			* END special code
			*/

		
		if($auth->acl_get('u_post_classifieds') && !sizeof($error)) 
		{
		
				
	

			generate_text_for_storage($ad_description, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);

			$sql_ary = (array(
    			'ad_date'       			=> $now,
				'ad_title'        			=> $ad_title,
				'ad_description'        	=> $ad_description,
				'ad_price'         			=> $price,
				'ad_poster_id'              => $user->data['user_id'],
				'bbcode_uid'        		=> $uid,
    			'bbcode_bitfield'   		=> $bitfield,
   				'bbcode_options'    		=> $options,
   				'cat_id'   			 		=> $cat,
   				'ad_status'					=> $status,
   				'ad_expire'					=> $expire,	
   				'allow_comments'			=> $allow_comments,
   				'notify_comments'			=> $notify_comments,
   				'thumb'						=> $thumb,
   				'phone'						=> $phone,
   				'paypal'					=> $paypal,
    			'paypal_currency'			=> $paypal_currency,
   				'edit_time'					=> '',
   				'last_edit_by'				=> '',


			));

			$sql = 'INSERT INTO  ' . CLASSIFIEDS_TABLE . $db->sql_build_array('INSERT', $sql_ary);
			$db->sql_query($sql);

			
			$sql = $db->sql_build_query('SELECT', array(
			'SELECT'	=> ' a.*, u.user_id, u.username, u.user_colour',
			'FROM'		=> array(
				CLASSIFIEDS_TABLE				=> 'a',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'u.user_id = a.ad_poster_id',		
				)
			),
			'WHERE'		=> 'u.user_id = ' . $user->data['user_id'],
			'ORDER_BY'	=> 'a.ad_id DESC'
			));
	
			$advertisement_id = $db->sql_nextid();
		
			$sql =  'SELECT user_id, username, user_colour, user_lang, user_email, user_jabber, user_notify_type 
					FROM  '. USERS_TABLE .'
					WHERE user_id = ' . $user->data['user_id'];
				
			$result	 = $db->sql_query($sql);
			$row = $db->sql_fetchrow( $result );
			$db->sql_freeresult($result);
			
			if($config['email_ad'])
			{
				$messenger = new messenger();

				$messenger->template('new_ad', $row['user_lang']);
    			$messenger->to($row['user_email'], $row['username']);
    			$messenger->im($row['user_jabber'], $row['username']);
    			$messenger->assign_vars(array(
        			'USERNAME'    	 => $row['username'],
        			'TITLE'			 => $ad_title,
        			'EXPIRE_DATE'    => $user->format_date($expire),
        			'SITE_NAME'		 => $config['sitename'],
    			));
    			$messenger->send($row['user_notify_type']);
				$messenger->save_queue();
				
			}
			// find out who wants to be notified and send a email to them
			$sql =  'SELECT user_id, username, user_colour, classified_email, user_lang, user_email, user_jabber, user_notify_type 
						FROM  '. USERS_TABLE . ' 
						WHERE classified_email = 1';
			$result	 = $db->sql_query($sql);
			$row = $db->sql_fetchrow( $result );
		
		
			while($row = $db->sql_fetchrow($result))
			{ 
	
				$messenger = new messenger();
				$messenger->template('notify_classified', $row['user_lang']);
    			$messenger->to($row['user_email'], $row['username']);
    			$messenger->im($row['user_jabber'], $row['username']);
    			$messenger->assign_vars(array(
        			'USERNAME'    	 => $row['username'],
        			'TITLE'			 => $ad_title,
        			'POSTER'		 => $user->data['username'],
        			'SITE_NAME'		 => $config['sitename'],
        			'PRICE'			 => $price,
        			'AD_LINK'		 => generate_board_url() . "/buysell/single_ad.$phpEx"."?ad_id=".$advertisement_id,
        			'DESCRIPTION'	 => $ad_description,

    			));
    			$messenger->send($row['user_notify_type']);
				$messenger->save_queue();	
			}
		
			$db->sql_freeresult($result);

			if($config['pm_ad'])
			{

				$send_from = $config['pm_id'];
				$my_subject	= $user->lang('NEW');
				$message	= $row['username'] . " " . $user->lang('POSTED'). "<strong> " . $ad_title . "</strong> \n " . $user->lang('EXPIRE') . "<strong> " . $user->format_date($expire) . "</strong> \n " . $user->lang('INFO');

				$poll = $uid = $bitfield = $options = ''; 
				generate_text_for_storage($my_subject, $uid, $bitfield, $options, false, false, false);
				generate_text_for_storage($message, $uid, $bitfield, $options, true, true, true);

				$data = array( 
					'address_list'		=> array ('u' => array($user->data['user_id'] => 'to')),
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
   					'allow_comments'	=> $allow_comments,
   					'notify_comments'	=> $notify_comments,

		
			);
				submit_pm('post', $my_subject, $data, false);
			}
				redirect(append_sid("{$phpbb_root_path}/buysell"));
	
		}
	
		$price = request_var('ad_price','', true);
		$uid = $bitfield = $options = ''; // will be modified by generate_text_for_storage
		$allow_bbcode = $allow_urls = $allow_smilies = true;
		$cat 			= request_var('cat', '', true);
		$allow_comments 		= request_var('allow_comments', 0);
		$notify_comments 		= request_var('notify_comments', 0);

	
	$template->assign_vars(array(

		'ERROR'					=> (sizeof($error)) ? implode('<br />', $error) : '',
		'AD_TITLE'				=> $ad_title,
		'AD_DESCRIPTION'		=> $ad_description,
		'AD_PRICE'				=> $price,
		'THUMB'					=> $thumb,
		'PHONE'					=> $phone,
		'ALLOW_COMMENT'			=> $allow_comments,
		'NOTIFY_COMMENT'		=> $notify_comments,
		'PAYPAL'				=> $paypal,
		'PAYPAL_CURRENCY'		=> $paypal_currency,
		

	));
		
	break;	
			
	case 'edit':


		$sql_ary = array(
			'SELECT'    => 'c.*, a.*, u.user_id, u.username, u.user_colour, u.user_from, u.user_aim, u.user_msnm, user_yim, user_jabber',
    		'FROM'      => array(
        		USERS_TABLE        			=> 'u',
       			CLASSIFIEDS_TABLE   		=> 'a',
        		CLASSIFIEDS_CATEGORY_TABLE 	=> 'c',
    		),
   			'WHERE'     => 'u.user_id = a.ad_poster_id and ad_id = ' . $id . ' and a.cat_id = c.id',
		);

		$sql = $db->sql_build_query('SELECT', $sql_ary);		
		$result	 = $db->sql_query($sql);
		$row = $db->sql_fetchrow( $result );
		$db->sql_freeresult($result);

						
		if (!$auth->acl_get('u_edit_own_classifieds'))
		{	
	     	trigger_error('NOT_AUTHORISED');
		}
						
		$desc = decode_message($row['ad_description'], $row['bbcode_uid']);

		$template->assign_vars(array(
		
			'AD_TITLE'			=>		$row['ad_title'],
			'AD_PRICE'			=>		$row['ad_price'],
			'AD_DATE'			=>		$user->format_date($row['ad_date']),
			'AD_LINK' 			=>	append_sid($phpbb_root_path . 'buysell/single_ad.' . $phpEx ,'ad_id=' . $row['ad_id']),
			'AD_DESCRIPTION'	=>  	$row['ad_description'],
			'AD_STATUS'			=> 		$row['ad_status'],
			'U_ACTION'			=>		append_sid("{$phpbb_root_path}buysell/new_ad.$phpEx", "mode=edit&amp;ad_id=$id"),
			'MODE_EDIT'			=> 		request_var('mode', 'edit'),
			'POSTER'			=> 		$row['ad_poster_id'],
			'CURRENT_ID'		=> 		$user->data['user_id'],
			'THUMB'				=> 		$row['thumb'],
			'CAT_ID'			=> 		$row['cat_id'],
			'CATEGORY'			=>		$row['name'],
			'ALLOW_COMMENT'		=> 		$row['allow_comments'],
			'NOTIFY_COMMENT'	=>		$row['notify_comments'],
			'PHONE'				=> 		$row['phone'],
			'PAYPAL'			=> 		$row['paypal'],
			'PAYPAL_CURRENCY'	=> 		$row['paypal_currency'],

		));
		


		if($user->data['user_id'] == $row['ad_poster_id'] || $auth->acl_get('a_')) 
		{
			
		
			if(!empty($_POST) && $auth->acl_get('u_edit_own_classifieds'))
			{
			
					
				$cat 			= request_var('cat', '', true);
				$phone 			= request_var('phone', '');
				$paypal 		= request_var('paypal', '');
				$paypal_currency 	= request_var('paypal_currency', '');				
								
				$status 		= request_var('ad_status', 0);
				$allow_comments = request_var('allow_comments', 0);
    			$notify_comments 	= request_var('notify_comments', 0);
				$thumb 			= request_var('thumb', '');
				$now 			= time();
				$ad_title 		= utf8_normalize_nfc(request_var('ad_title', '', true));
				$ad_description = utf8_normalize_nfc(request_var('message', '', true));
				$price 			= request_var('ad_price','', true);
				$uid 			= $bitfield = $options = ''; // will be modified by generate_text_for_storage
				$allow_bbcode 	= $allow_urls = $allow_smilies = true;

				if (!$ad_title)
				{
   	 				$error[] = $user->lang['NO_TITLE'];
				}
				
				// 1=yes
				if ($row['parent_id'] == 1)
				{
					$error[]	= 'YOU CANNOT SELECT A PARENT';
				}
				
				if (strlen($ad_title) < $config['minimum_title_length'])
				{
					$error[]	= sprintf($user->lang['NO_TITLE_TOO_SHORT'], $config['minimum_title_length']);
				}
				
				if (!@fopen($thumb, "r") && $thumb)
				{
					$error[]	= $user->lang['BAD_THUMB_URL'];
				}
				
				if (!preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $thumb) && $thumb)
				{
					$error[]	= $user->lang['BAD_THUMB_URL'];
				}
				
				if (!$ad_description)
				{
					$error[] = $user->lang['NO_DESCRIPTION'];
				}
				
				if (!$price)
				{
    				$error[] = $user->lang['NO_PRICE'];
				}

			$template->assign_vars(array(

					'ERROR'					=> (sizeof($error)) ? implode('<br />', $error) : '',
					'AD_TITLE'				=> $ad_title,
					'AD_DESCRIPTION'		=> $ad_description,
					'AD_PRICE'				=> $price,
					'THUMB'					=> $thumb,
					'PHONE'					=> $phone,
					'PAYPAL'				=> $paypal,
					'PAYPAL_CURRENCY'		=> $paypal_currency,
					'ALLOW_COMMENT'			=> $allow_comments,
					'NOTIFY_COMMENT'		=> $notify_comments,

				));

				if(!sizeof($error))
				{
				
				
				if (confirm_box(true))
				{
		
					generate_text_for_storage($ad_description, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);

					$sql_ary = (array(

						'ad_title'        			=> $ad_title,
						'ad_description'        	=> $ad_description,
						'ad_price'         			=> $price,
						'bbcode_uid'        		=> $uid,
    					'bbcode_bitfield'   		=> $bitfield,
   						'bbcode_options'    		=> $options,
   						'cat_id'   			 		=> $cat,
   						'ad_status'					=> $status,
   						'allow_comments'			=> $allow_comments,
   						'notify_comments'			=> $notify_comments,
   						'thumb'						=> $thumb,
   						'phone'						=> $phone,
   						'paypal'					=> $paypal,
   						'paypal_currency'			=> $paypal_currency,
   						'edit_time'					=> time(),
   						'last_edit_by'				=> $user->data['username'],
	
					));

					$id = request_var('ad_id', 0);
					$sql = 'UPDATE ' . CLASSIFIEDS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary). ' WHERE ad_id = ' . $id;
					$db->sql_query($sql);
	
		 			redirect(append_sid("{$phpbb_root_path}/buysell"));

				}
				else
				{

	
					$s_hidden_fields = build_hidden_fields(array(		
						'submit'					=> true,
						'ad_title'        			=> $ad_title,
						'message'        			=> $ad_description,
						'ad_price'         			=> $price,
						'bbcode_uid'        		=> $uid,
    					'bbcode_bitfield'   		=> $bitfield,
   						'bbcode_options'    		=> $options,
     					'cat'   			 		=> $cat,
   						'ad_status'					=> $status,
 						'allow_comments'			=> $allow_comments,
 		   				'notify_comments'			=> $notify_comments,
 		   				'thumb'						=> $thumb,
 		   				'phone'						=> $phone,
 		   				'paypal'					=> $paypal,
 		   				'paypal_currency'			=> $paypal_currency,
 		   				
   						'edit_time'					=> time(),
   						'last_edit_by'				=> $user->data['username'],

					));

	
					confirm_box(false, $user->lang['EDIT_CONFIRM'], $s_hidden_fields);
				}
				}
			}
			
		}
		
	break;

	case 'extend_ad':

		$add_days = '+' . $config['number_expire'] . 'days';
		$new_date = strtotime($add_days, time());
	
		$sql_ary = (array(

			'ad_expire'        			=> $new_date,

	
		));

		$id = request_var('ad_id', 0);
		$sql = 'UPDATE ' . CLASSIFIEDS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_ary). ' WHERE ad_id = ' . $id;
		$db->sql_query($sql);

		redirect(append_sid("{$phpbb_root_path}/buysell"));

	break;

}


page_footer();
?>