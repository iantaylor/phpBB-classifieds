<?php
/**
*
* @package acp
* @version $Id: mod_version_check_version.php 51 2007-10-30 04:40:42Z Handyman $
* @copyright (c) 2007 StarTrekGuide
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package mod_version_check
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

class classifieds_version
{
	function version()
	{
		return array(
			'author'	=> 'Platinum_2007',
			'title'		=> 'phpbb Classifieds',
			'tag'		=> 'mod_version_check',
			'version'	=> '0.7.0',
			'file'		=> array('itmods.com', 'updatecheck', 'mods.xml'),
		);
	}
}

?>