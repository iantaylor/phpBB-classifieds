<?php
/**
*
* @author platinum_2007 (Ian Taylor) iantaylor603@gmail.com
* @package Classifieds Mod
* @version 0.2.0
* @copyright (c) 2009 ian taylor
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}
class acp_classifieds_info
{
    function module()
    {
        return array(
            'filename'    => 'acp_classifieds',
            'title'        => 'ACP_CLASSIFIEDS',
            'version'    => '1.0.0',
            'modes'        => array(
            'index'        => array('title' => 'ACP_CLASSIFIEDS_INDEX_TITLE', 'auth' => 'acl_a_board', 'cat' => array('ACP_CAT_DOT_MODS')),
            'manage'        => array('title' => 'ACP_CLASSIFIEDS_MANAGE_TITLE', 'auth' => 'acl_a_board', 'cat' => array('ACP_CAT_DOT_MODS')),

            ),
        );
    }

    function install()
    {
    }

    function uninstall()
    {
    }
}
?>