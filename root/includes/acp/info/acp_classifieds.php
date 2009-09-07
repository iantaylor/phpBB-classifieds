<?php
/**
*
* @package phpBB Classifieds MOD
* @version $Id: 0.7.0
* @copyright Ian Taylor
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