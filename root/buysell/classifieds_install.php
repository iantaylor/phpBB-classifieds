<?php
/**
*
* @author platinum_2007 (Ian Taylor) iantaylor603@gmail.com
* @package umil
* @version $Id classifieds_install.php 0.2.1 2009-03-22 16:56:28GMT platinum_2007 $
* @copyright (c) 2009 ian taylor
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
define('UMIL_AUTO', true);
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
$user->session_begin();
$auth->acl($user->data);
$user->setup();

if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

// The name of the mod to be displayed during installation.
$mod_name = 'CLASSIFIEDS';

/*
* The name of the config variable which will hold the currently installed version
* You do not need to set this yourself, UMIL will handle setting and updating the version itself.
*/
$version_config_name = 'classifieds_version';

/*
* The language file which will be included when installing
* Language entries that should exist in the language file for UMIL (replace $mod_name with the mod's name you set to $mod_name above)
* $mod_name
* 'INSTALL_' . $mod_name
* 'INSTALL_' . $mod_name . '_CONFIRM'
* 'UPDATE_' . $mod_name
* 'UPDATE_' . $mod_name . '_CONFIRM'
* 'UNINSTALL_' . $mod_name
* 'UNINSTALL_' . $mod_name . '_CONFIRM'
*/
$language_file = 'mods/classified';

/*
* The array of versions and actions within each.
* You do not need to order it a specific way (it will be sorted automatically), however, you must enter every version, even if no actions are done for it.
*
* You must use correct version numbering.  Unless you know exactly what you can use, only use X.X.X (replacing X with an integer).
* The version numbering must otherwise be compatible with the version_compare function - http://php.net/manual/en/function.version-compare.php
*/
$versions = array(
	// Version 0.1.0
	'0.1.0' => array(
		'table_add' => array(
			array(CLASSIFIEDS_TABLE, array(
				'COLUMNS'			=> array(
				'ad_id'				=> array('UINT', NULL, 'auto_increment'),
				'ad_title'			=> array('VCHAR', ''),
				'ad_description'	=> array('TEXT', ''),
				'ad_poster_id'		=> array('UINT', 0),
				'ad_price'			=> array('VCHAR', ''),
				'ad_date'			=> array('VCHAR', ''),
				'bbcode_uid'		=> array('STEXT_UNI', ''),
				'bbcode_bitfield'	=> array('VCHAR', '0'),
				'bbcode_options'	=> array('VCHAR', '0'),
				'enable_bbcode'		=> array('USINT', 1),
				'enable_magic_url'	=> array('USINT', 1),
				'enable_smilies'	=> array('USINT', 1),
				'ad_status'			=> array('BOOL', 1),
				'cat_id'			=> array('UINT',0),
				'ad_views'			=> array('VCHAR', '0'),

				),
				'PRIMARY_KEY' => array('ad_id'),
			)),
		array(CLASSIFIEDS_CATEGORY_TABLE, array(
			'COLUMNS'	=> array(
				'id'		=> array('UINT', NULL, 'auto_increment'),
				'name'		=> array('VCHAR', ''),

				),
				'PRIMARY_KEY' => array('id'),
			)),
	
		),
		

		 'module_add' => array(
		 		 array('acp', 'ACP_CAT_DOT_MODS', 'ACP_CLASSIFIEDS'),
		 		 
		           array('acp', 'ACP_CLASSIFIEDS', array(
                                        'module_basename'                => 'classifieds',
                                        'modes'                          => array('index'),
                                ),
                        ),
                  
),

		'config_add' => array(
		array('classifieds_enable', true),
		array('enable_classifieds', '1', '0'),
		array('disable_message', 'classifieds disabled', '0'),
		array('number_ads', '10', '0'),

		),
		
		'permission_add' => array(
			array('u_view_classifieds', 1),
			array('u_post_classifieds', 1),
			array('u_edit_own_classifieds', 1),
			array('u_can_delete_classifieds', 1),

		)),
		
'0.2.1' => array(

	'config_add' => array(
		array('number_expire', '7', '0'),
		array('email_ad', '0' , '0'),
		array('email_expire', '0', '0'),
		array('pm_ad', '0' , '0'),
		array('pm_expire', '0' , '0'),
	
	),


		
	 'module_add' => array(
		 		 
		         array('acp', 'ACP_CLASSIFIEDS', array(
                          'module_basename'                => 'classifieds',
                           'modes'                          => array('manage'),
                                ),
                        ),

),

		'table_column_add' => array(
				array(CLASSIFIEDS_TABLE, 'ad_expire', array('VCHAR:255', '')),
				array(CLASSIFIEDS_TABLE, 'expire_email', array('VCHAR:255', '')),
		),
				
							
		'cache_purge' => '',
),
'0.2.2' => array(

	'config_add' => array(
		array('show_rules', '0', '0'),

	'cache_purge' => '',
	)),

'0.2.3' => array(

	'config_add' => array(
		array('pm_id', '2', '0'),
		array('sold_color', '#ECD5D8' , ''),
		array('closed_color', '#FFE4B5', ''),
		array('allow_tinypic', '1' , '0'),
		
	)),
	
	'0.4.0' => array(
	
	// new config settings 
	'config_add' => array(
		array('allow_comments', '1', '0'),
		array('allow_upload', '1' , ''),
		array('upload_size', '400', ''),
		
		),
	// new table columns
	'table_column_add' => array(
				array(CLASSIFIEDS_TABLE, 'allow_comments', array('USINT', 0)),
				array(CLASSIFIEDS_TABLE, 'notify_comments', array('USINT', 0)),
				array(USERS_TABLE, 'classified_email', array('USINT', 0)),
				
		),
		// we need some new tables for this version
	'table_add' => array(
			array(CLASSIFIEDS_COMMENTS_TABLE, array(
				'COLUMNS'	=> array(
					'comment_id'			=> array('UINT', NULL, 'auto_increment'),
					'comment_date'			=> array('VCHAR', ''),
					'comment_poster_id'		=> array('UINT', '0'),
					'comment_text'			=> array('TEXT', ''),
					'ad_id'					=> array('VCHAR', ''),
					'bbcode_bitfield'		=> array('VCHAR', ''),
					'bbcode_uid'			=> array('VCHAR', ''),
					'bbcode_options'		=> array('UINT', '0'),
					'enable_smilies'		=> array('TINT:', 1),
					'enable_bbcode'			=> array('TINT:', 1),
					'enable_magic_url'		=> array('TINT:', 1),
				),
				'PRIMARY_KEY' => array('comment_id'),
			)),

		),
		
	),
	
	'0.5.0'		=> array(
	'table_column_add' => array(
				array(CLASSIFIEDS_TABLE, 'thumb', array('VCHAR', '')),
				array(CLASSIFIEDS_TABLE, 'phone', array('VCHAR', '')),

	
		)),
		
	'0.6.0'	=> array(
	
		'table_column_add' => array(
				array(CLASSIFIEDS_CATEGORY_TABLE, 'left_id', array('UINT', 0)),
				array(CLASSIFIEDS_CATEGORY_TABLE, 'right_id', array('UINT', 0)),
				array(CLASSIFIEDS_CATEGORY_TABLE, 'parent', array('VCHAR', '')),

	
		),

			'config_add' => array(
				array('sort_active_first', '0', '0'),
			),
	
	
	),

	
'0.7.0'	=> array(

	'table_column_add' => array(
	
				array(USERS_TABLE, 'classifieds_credits', array('USINT', 0)),
				
				array(CLASSIFIEDS_CATEGORY_TABLE, 'parent_id', array('VCHAR', 0)),
				
				array(CLASSIFIEDS_TABLE, 'last_edit_by', array('VCHAR', 0)),
				array(CLASSIFIEDS_TABLE, 'edit_time', array('VCHAR', 0)),
				array(CLASSIFIEDS_TABLE, 'paypal', array('VCHAR', '')),
				array(CLASSIFIEDS_TABLE, 'paypal_currency', array('VCHAR', '')),
	
	),
	'config_add' => array(
			
		array('enable_int', '0', '0'),
		array('master_paypal', '', '0'),
		array('ad_cost', '1.99', '0'),
		array('paypal_currency', 'USD', '0'),
		array('enable_sandbox', '0', '0'),

	
	)),
	
	'0.8.0'	=> array(
	
		'table_remove' => CLASSIFIEDS_IMAGES_TABLE,
	),
	'table_column_add' => array(
			array(USERS_TABLE, 'last_classifieds_visit', array('VCHAR', '0')),
				
	),
	'config_add' => array(
		array('show_full', '0', '0'),
	),


);

// Include the UMIF Auto file and everything else will be handled automatically.
include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);

?>