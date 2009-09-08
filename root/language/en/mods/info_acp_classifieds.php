<?php
/**
*
* @package phpBB Classifieds MOD
* @version $Id: 0.7.0
* @copyright Ian Taylor
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/

if (empty($lang) || !is_array($lang))
{
    $lang = array();
}
// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'ENABLE_CLASSIFIEDS'				=>	'Enable classifieds',
	'ENABLE_CLASSIFIEDS_EXPLAIN'		=>	'Turn classifieds on or off',
	
	'DISABLE_MESSAGE'					=>	'Disabled message',
	'DISABLE_MESSAGE_EXPLAIN'			=>	'Set the message to display when the classifieds are set to off.',
	
    'ACP_CLASSIFIEDS_INDEX_TITLE'       => 'classifieds control',
    'ACP_CLASSIFIEDS_MANAGE_TITLE'       => 'classifieds management',

    'ACP_CLASSIFIEDS'                   => 'classifieds Mod',
   	'CATEGORIES'						=> 'Categories', 
   	'CATEGORIES_EXPLAIN'				=> 'Enter a category title',
   	'NOTE_REC'							=>'NOTE: You if you delete a category and there is ad\'s in the delete category they will be moved to the "Not Categorized" category. The "Not Categorized" category name can be changed but not deleted.',
   	
   	'ALLOW_GUEST'						=> 'Allow Guests',
   	'ALLOW_GUEST_EXPLAIN'				=> 'If set to yes guests will be able to view the classifieds.',   
   	
   	'PREVIEW_DISABLE'					=>	'Preview Disable Message',
   	
   	'NUMBER_ADS'						=>	'Number of ads to display',
   	'NUMBER_ADS_EXPLAIN'				=>	'Number of ad\'s to display on each page.',
   	
   	'TITLE'								=>	'Title',
   	'DATE'								=>	'Date',
   	
   	'NUMBER_EXPIRE'						=> 	'Number of days the ad should show',
   	'NUMBER_EXPIRE_EXPLAIN'				=>	'Set the number of days ads should show up for, after they expired they will not be deleted but rather removed from the classifieds and sorted into the expired section under "Classifieds Managment" where you can delete or recover the ad.',						


	'EMAIL_SETTINGS'					=> 'Email & Private message settings',
	'EMAIL_NEW_AD'						=>	'Email on new Ad',
	'EMAIL_NEW_AD_EXPLAIN'				=>	'Send an email to the ad poster upon new ad, will tell them when there ad will expire and explain how to use it more. ',
	'EMAIL_EXPIRED'						=> 'Email on expired ad',
	'EMAIL_EXPIRED_EXPLAIN'				=>	'Send an email to the ad poster when there ad has expired, will explain how to get the ad revived if needed and will only send if the ad is marked Active',
	
	'PM_NEW_AD'						=>	'Private message on new Ad',
	'PM_NEW_AD_EXPLAIN'				=>	'Send a private message to the ad poster upon new ad, will tell them when there ad will expire and explain how to use it more. ',
	'PM_EXPIRED'						=> 'Private message on expired ad',
	'PM_EXPIRED_EXPLAIN'				=>	'Send a private message to the ad poster when there ad has expired, will explain how to get the ad revived if needed and will only send if the ad is marked Active',
	'PM_ID'								=> 'Send PM from id',
	'PM_ID_EXPLAIN'						=>	'User id to send the private messages from',
	
	'SHOW_RULES'						=> 'Show rules',
	'SHOW_RULES_EXPLAIN'				=> 'Show rules on the index page of the classifieds.<br /> Rules can be changed in language/en/mods/classifieds.php',

		
	'SOLD_COLOR'						=>	'Sold color',	
	'SOLD_COLOR_EXPLAIN'				=>	'When a ad is marked sold it will show up in this color <br /> Example : <font color="#FFE4B5">#FFE4B5</font> or a color name like <font color="red">red</font> ',	   	
	'CLOSED_COLOR'						=>	'Closed color',	
	'CLOSED_COLOR_EXPLAIN'				=>	'When a ad is marked closed it will show up in this color<br /> Example : <font color="#FFE4B5">#FFE4B5</font> or a color name like <font color="red">red</font>',
	
	'ALLOW_TINYPIC'						=>	'Allow tinypic',
	'ALLOW_TINYPIC_EXPLAIN'				=>	'If enabled users will be able to upload images to tinypic.com',
	'ALLOW_COMMENTS'					=>	'Allow commenting',
	'ALLOW_COMMENTS_EXPLAIN'			=>	'Allow users to comment on Advertisements if the seller/buyer has them enabled',
	'ALLOW_UPLOAD'						=>	'Allow Image Uploading',
	'ALLOW_UPLOAD_EXPLAIN'				=>	'If enabled users will be able to upload an image to your server.',
	'UPLOAD_SIZE'						=>	'Max image size',
	'UPLOAD_SIZE_EXPLAIN'				=> 	'If you allow image uploading set the size images should be re-sized to so your layout will not break.',
	
	'SORT_ACTIVE_FIRST'					=> 'Sort active ads first',
	'SORT_ACTIVE_FIRST_EXPLAIN'			=> 'If enabled closed and sold ads will be sorted to the bottom of the list.',
	'NO_CATEGORY'						=> 'No category under the selected id!',
	'PARENT'							=> 'Parent :',
	'PARENT_ID'							=> 'Parent ID :',
	'PARENT_EXPLAIN'					=> '(1 = yes or 0 = no)',
	'CHARACTERS'						=> 'Characters',
	'MINIMUM_LENGTH'				=> 'Minimum title length',
	'MINIMUM_LENGTH_EXPLAIN'		=> 'Enter 0 to disable a minimum length for titles.',
	'BAD_PM_ID'						=> 'The entered user_id for PM sender does not exist!',
	
		'ACP_MOD_VERSION_CHECK'	=> 'Check for MOD updates',
	'ANNOUNCEMENT_TOPIC'	=> 'Release Announcement',

	'CURRENT_VERSION'		=> 'Current Version',

	'DOWNLOAD_LATEST'		=> 'Download Latest Version',

	'LATEST_VERSION'		=> 'Latest Version',

	'NO_ACCESS_MODS_DIRECTORY'	=> 'Unable to open adm/mods, check to make sure that directory exists and you have read permission on that directory',
	'NO_INFO'					=> 'Version server could not be contacted',
	'NOT_UP_TO_DATE'			=> '%s is not up to date',

	'RELEASE_ANNOUNCEMENT'	=> 'Annoucement Topic',
	'UP_TO_DATE'			=> '%s is up to date',

	'VERSION_CHECK'			=> 'MOD Version Check',
	'VERSION_CHECK_EXPLAIN'	=> 'Checks to see if your mods are up to date',
	'DISPLAY_SETTINGS'		=> 'Display settings',
	'ACTIVE_ADS'			=> 'Active ads :',
	'ACTIVE_ADS_EXPLAIN'	=> 'Below is a list of Active classifieds ads, if the ad is highlighted in yellow it is either Closed or Sold.',
	'EXPIRED_ADS'			=> 'Expired ads :',
	'EXPIRED_ADS_EXPLAIN'	=> 'Below is a list of expired classifieds ads, if the ad is highlighted in yellow it is either Closed or Sold and should be deleted.',
	'SET_DAYS'				=> 'Set the number of days to extend the ad for.',
	'PARENTS_EXPLAIN'		=> 'After setting an ad to a parent you must then sort it into the correct parent in the list below using the up and down arrows',
	
		// PAYPAL language variables, requres special script.
	
	'PAYPAL_SETTINGS'		=> 'Paypal Settings',
	'PAYPAL_EXPLAIN'		=> 'The settings below are to be used with a special script Visit <a href="http://itmods.com/pay.php">www.itmods.com for more information </a>',
	'MASTER_PAYPAL'			=> 'Master Paypal Email',
	'MASTER_PAYPAL_EXPLAIN' => 'This will be the email address to accept payment from.',
	'AD_COST'				=> 'Cost per credit',
	'AD_COST_EXPLAIN'		=> 'How much do you want to charge per credit? 1 credit = 1 advertisement.',
	'CURRENCY'				=> 'Currency',
	'ENABLE_INT'			=> 'Enable Paypal integration',
	'ENABLE_INT_EXPLAIN'	=> 'If enabled users will have to pay a certain fee to post and ad',
	'ENABLE_SANDBOX'		=> 'Enable sandbox testing',
	'ENABLE_SANDBOX_EXPLAIN' => 'This will turn on paypal sandbox testing and should only be used for testing not while live!',
	'COST_10'						=> '# of credits for a ad worth 0-10',
	'COST_50'						=> '# of credits for a ad worth 11-50',
	'COST_100'						=> '# of credits for a ad worth 51-100',
	'COST_200'						=> '# of credits for a ad worth 101-200',
	'COST_300'						=> '# of credits for a ad worth 201-500',
	'COST_500'						=> '# of credits for a ad worth 501+',
	'CREDIT_PER_AD'					=> 'Credit cost based on Ad price',
	'SHOW_FULL'						=> 'Hide empty categories',
	'SHOW_FULL_EXPLAIN'				=> 'If enabled only categories with 1 or more ads will display, parents will still display',


));
?>