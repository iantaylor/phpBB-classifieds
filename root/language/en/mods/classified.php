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
	'PAGE_CLASSIFIED'		=> 'Classifieds',
	'NEW_AD'				=> 'New AD',
	'BY'					=> 'by',
	'DESCRIPTION'			=> 'Description',	
	'TITLE'					=> 'Title',	
	'PRICE'					=> 'Price',
	'USERNAME'				=>	'Username',
	'DATE'					=>	'Date',
	'CAT_NAME'				=>  'Category',
	'CATEGORIES'			=>	'Categories',
	'STATUS'				=>  'Ad Status',
	'NOT_SOLD'				=>	'Not sold',
	'SOLD'					=>	'Sold',
	'CLOSED'				=>	'Closed',
	'UPLOAD_PIC'			=>	'Click to upload and image, take the code that is given to you and put it in the description!',
	'EDIT_OWN'				=>	'<p style="text-align:center"><font color="red"> <b>You can only edit your own ad!</b> </font></[>',
	'TITLE'					=>	'Ad title',
	'VIEW_ALL'				=>	'View all',
	'VIEW_ACTIVE'			=>  'View active ads',
	'USER_CONTROLS'			=>	'User Controls',
	'VIEW_OWN'				=>	'View your ads',
	'ACTIVE_ADS'			=>	'Active ads',
	'SOLD_ADS'				=>	'Sold ads',
	'CLOSED_ADS'			=>	'Closed ads',
	'ALLOW_COMMENTS'		=>	'Allow comments:',
	'EMAIL_COMMENTS'		=>	'Email on new comments:',
	'COMMENTS'				=>  'Comments',
	
	
	'LIST_AD'    			=> '1 ad',
	'LIST_ADS'    			=> '%s ads',
	'ADDED_PERMISSIONS'     => 'You have successfully added classifieds permission options to your database.',
	'DELETE_CONFIRM'		=>	'Are you sure you want to delete this ad?',
	'EDIT_CONFIRM'			=>	'Are you sure you want to edit this ad?',
	
	//errors
	
	'NO_TITLE'				=>	'Please enter a title!',
	'ONLY_NUMBERS'			=>  'Please only enter numbers in the price field!',
	'NO_TITLE_TOO_SHORT'	=>  'Title is to shot, please enter a minimum of %1s characters!',
	'NO_DESCRIPTION'		=>	'Please enter a description!',
	'NO_PRICE'				=>	'Please enter a price!',
	'AD_EXPIRED'			=>	'Sorry this ad has expired, if you are the owner of this ad please contact the Board Administrator for more information.',
	'BAD_FILE_TYPE'			=>  'The file you are trying to upload is now allowed! Please make sure the file is either one of the following types: .gif, .jpg, .png, .jpeg, .bmp',
	'BAD_THUMB_URL'			=> 'Please enter a valid URL in the thumbnail input!',
	'BAD_CATEGORY'			=> 'Please do not select a parent category, select a category that does not have a blue background!',
	
	// pm on new ad
	
	'NEW'					=> 'You posted an ad in the classifieds',
	'POSTED'				=> 'You just posted a ad in the classifieds with the title :',
	'EXPIRE'				=> 'Your Ad will expire on :',
	'INFO'					=> 'If your ad has expired without being sold please contact the board Administrator to have it revived.
If you ad has sold before being expired please edit the ad and mark it "closed" or "sold".',

	// PM on expired ad

	'NEW_EXPIRED'					=> 'Your AD has expired!',
	'YOUR_AD'						=> 'You posted an AD in the classifieds and has expired on :',
	'ASSUME'						=>	'Since your ad is still marked Active we must assume it has not sold yet! So if you wish to have the ad revived please contact the Board Administrator with the info on your ad.',
		
	'WILD_CARD'						=>	'Use * as a wildcard for partial matches.',
	
	// image upload languages 
	
	'EMPTY_FILE'					=> 'File size is empty.',
	'TO_BIG'						=> 'File is to big!',
	'NO_PERM_GIF'					=> 'PERMISSION DENIED [GIF]',
	'NO_PERM_JPG'					=> 'PERMISSION DENIED [JPG]',
	'NO_PERM_JPEG'					=> 'PERMISSION DENIED [JPEG]',
	'NO_PERM_PNG'					=> 'PERMISSION DENIED [PNG]',
	'DUP_IMAGE'						=> 'CANNOT MAKE IMAGE IT ALREADY EXISTS',
	'NO_FILE'						=> 'NO FILE SELECTED',
	'BAD_TYPE'						=> 'File type is not allowed!',
	'SUCC'							=>  'Success!',
	'INSTRUCTION'					=> 'Now take the code below and paste it in the description box!',
	'UPLOADING'						=> 'File Uploading Please Wait...',
	'IMAGE_UPLOAD'					=> 'Image uploader',
	'SUPPORTED'						=> 'Supported File Types: gif, jpg, png',
	'ERRORS_FOUND'					=> 'Error(s) Found:',
	'THUMBNAIL'						=> 'Create thumbnail',
	'THUMB_INSTRUCTIONS'			=> 'Now take the code below and place it in the "thumbnail" input!',
	'THUMB'							=> 'Thumbnail',
	'THUMB_EXPLAIN'					=> 'Filling out this input will place a thumbnail image on the index page. For the best results user the thumbnail uploader provided [optional].',
	
	'RULES'							=> 'Classifieds rules',
	'RULES_DESC'					=> 'Please feel free to create an ad for testing but <strong>delete</strong> the ad when you are finished testing the mod! <br />Development is done on this site so the classifieds is always the newest version and may not be the exact version downloadable!',
	'PHONE'							=> 'Phone number',
	'PAYPAL_EMAIL'					=> 'Paypal email',
	'PAYPAL_EXPLAIN'				=> 'If you wish to do the transaction with paypal you can enter your email here to display a buynow button on your ad.',
	'CURRENCY'						=> 'Currency',
	'VIEW_EXPIRED'					=> 'View expired ads',
	'PRICE_EXPLAIN'					=> 'Enter your preferred price, must only be numbers [mandatory].',
	'PHONE_EXPLAIN'					=> 'Enter your phone number for members to contact you [optional].',
	'ALLOW_COMMENTS_EXPLAIN'		=> 'Would you like people to be able to comment on your ad?',
	'EMAIL_COMMENTS_EXPLAIN'		=> 'Would you like to get a email when someone comments on your ad?',
	'CATEGORY_EXPLAIN'				=> 'Please select the category your ad falls under.',
	'LOG_CLASSIFIEDS_MOVE_UP'		=> 'Moved classifieds category up',
	'LAST_EDIT'						=> '<i>Last edited on %1$s by %2$s</i>',
	
	// Special Script stuffs
	
	'CONFIRM_ORDER'					=> 'Confirm your order',
	'TOTAL_COST'					=> 'The total cost of your order is :',
	'PLEASE_COMPLETE'				=> 'Please click the button below to complete your order.',
	'ORDER_CONFIRMED'				=> 'Order confirmed',
	'THANKS_PURCHASE'				=> 'Thank you for your purchase you now have',
	'THANKS_PURCHASE_CREDITS'		=> 'in your account and can post',
	'BOUGHT_ADVERTISEMENTS'			=> 'Advertisements.',
	'KEEP_TRACK'					=> 'To keep track of your credits you may visit this page at any time.',
	'PURCHASE_AD'					=> 'Purchase Advertisement',
	'BUY_CREDIT'					=> 'In order to post an advertisement you need to purchase credits Please view the chart below to see how many credits you will need!',
	'HOW_CREDITS'					=> 'How many credits do you want to purchase?',
	'ORDER_CAN'						=> 'ORDER HAS BEEN CANCELED',
	'CALC_TOTAL'					=> 'Calculate total',
	'ITEM_NAME'						=> 'Classifieds credit',
	'COMPLETE_ORDER'				=> 'Complete order',
	'PLACE_AD'						=> 'Post your advertisement now!',
	'AD_PRICE_CREDIT'				=> 'Ad price',
	'CREDITS_NEEDED'				=> 'Credits needed',
	'COST_PER_10'					=> '0-10',
	'COST_PER_50'					=> '11-50',
	'COST_PER_100'					=> '51-100',
	'COST_PER_200'					=> '101-200',
	'COST_PER_300'					=> '201-500',
	'COST_PER_500'					=> '501+',
	'EACH_CREDIT'					=> 'Each credit will cost',
	'NEED_MORE_CREDITS'				=> 'You do not have enough credits to place this ad! please refer to this chart <a href="paypal.php">Credit explanation</a>',

));



?>