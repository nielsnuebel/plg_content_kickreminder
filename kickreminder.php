<?php
/**
 * Joomla! Content plugin - KickReminder
 *
 * @author     Niels Nuebel <niels@niels-nuebel.de>
 * @copyright  Copyright 2015-2017 Niels Nuebel. All rights reserved
 * @license    GNU Public License
 * @link       http://www.niels-nuebel.de
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * KickCCK System Plugin
 */
class plgContentKickReminder extends JPlugin
{
	/**
	 * Database object
	 *
	 * @var    JDatabaseDriver
	 * @since  3.3
	 */
	protected $db;

	/**
	 * Constructor
	 *
	 * @param object $subject
	 * @param array  $config
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * The display event.
	 *
	 * @param   string    $context     The context
	 * @param   stdClass  $item        The item
	 * @param   Registry  $params      The params
	 * @param   integer   $limitstart  The start
	 *
	 * @return  string
	 *
	 * @since   3.7.0
	 */
	public function onContentAfterTitle($context, $item, $params, $limitstart = 0)
	{
		$js = array();

		$js[] = '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>';
		$js[] = '<script>';
		$js[] = 'jQuery(function () {';
		$js[] = "jQuery('#cleancachefolder').click(function(){";
		$js[] = "jQuery.ajax({";
		$js[] = "type: 'POST',";
		$js[] = "url: '".JUri::root(false)."index.php?option=com_ajax&plugin=AddItemToKickReminderList&format=raw&group=content',";
		$js[] = "data: {";
		$js[] = "},";
		$js[] = "dataType: 'json',";
		$js[] = "success: function (data) {";
		$js[] = "if (data.delete == 'success') {";
		$js[] = "jQuery('#DeleteSuccess').removeClass('hidden');";
		$js[] = "jQuery('#DeleteError').addClass('hidden');";
		$js[] = "} else {";
		$js[] = "jQuery('#DeleteError').removeClass('hidden');";
		$js[] = "jQuery('#DeleteSuccess').addClass('hidden');";
		$js[] = "}";
		$js[] = "}";
		$js[] = "});";
		$js[] = "});";
		$js[] = '});';
		$js[] = '</script>';

		$js = implode("\n",$js);

		$html = array();
		$html[] = '<div id="DeleteError" class="alert alert-danger hidden">'.JText::_('PLG_KICKIMAGERESIZE_CLEANCACHEFOLDER_ERROR') .'</div>';
		$html[] = '<div id="DeleteSuccess" class="alert alert-success hidden">'.JText::_('PLG_KICKIMAGERESIZE_CLEANCACHEFOLDER_SUCCESS') .'</div>';
		$html[] = '<input type="button" class="btn btn-danger" value="'.JText::_('PLG_KICKIMAGERESIZE_CLEANCACHEFOLDER') .'" id="cleancachefolder">';

		$html = implode("\n",$html);

		return $js . $html;
	}

	public function onAjaxAddItemToKickReminderList()
	{
		$app = JFactory::getApplication();

		if (JFolder::delete($this->params->get('cache_folder', ''))){
			$return = array ('delete'=>'success');
		} else{
			$return = array ('delete'=>'error');
		}

		echo json_encode($return);

		$app->close();
	}
}
