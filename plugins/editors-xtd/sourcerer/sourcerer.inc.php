<?php
/**
 * Popup page
 * Displays the Sourcerer Code Helper
 *
 * @package			Sourcerer
 * @version			2.11.3
 *
 * @author			Peter van Westen <peter@nonumber.nl>
 * @link			http://www.nonumber.nl
 * @copyright		Copyright © 2011 NoNumber! All Rights Reserved
 * @license			http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined( '_JEXEC' ) or die();

$user = JFactory::getUser();
if ( $user->get( 'guest' ) ) {
	JError::raiseError( 403, JText::_( "ALERTNOTAUTH" ) );
}

require_once JPATH_PLUGINS.'/system/nnframework/helpers/parameters.php';
$parameters = NNParameters::getInstance();
$params = $parameters->getPluginParamValues( 'sourcerer', 'editors-xtd' );

$app = JFactory::getApplication();
if ( $app->isSite() ) {
	if ( !$params->enable_frontend ) {
		JError::raiseError( 403, JText::_( "ALERTNOTAUTH" ) );
	}
}

$class = new plgButtonSourcererPopup();
$class->render( $params );

class plgButtonSourcererPopup
{
	function render( &$params )
	{
		jimport( 'joomla.filesystem.file' );

		$app = JFactory::getApplication();

		$parameters = NNParameters::getInstance();
		$system_params = $parameters->getPluginParamValues( 'sourcerer' );

		// Load plugin language
		$lang = JFactory::getLanguage();
		$lang->load( 'plg_editors-xtd_sourcerer', JPATH_ADMINISTRATOR );
		$language = 'en';
		foreach ( $lang->getLocale() as $locale ) {
			if ( JFile::exists( JPATH_PLUGINS.'/editors-xtd/sourcerer/editarea/langs/'.$locale.'.js' ) ) {
				$language = $locale;
				break;
			}
		}

		// Add scripts and styles
		JHtml::_( 'behavior.mootools' );

		require_once JPATH_PLUGINS.'/system/nnframework/helpers/versions.php';
		$sversion = NoNumberVersions::getXMLVersion( 'sourcerer', 'editors-xtd', null, 1 );
		$version = NoNumberVersions::getXMLVersion( null, null, null, 1 );

		$document = JFactory::getDocument();
		$document->addScript( JURI::root( true ).'/plugins/editors-xtd/sourcerer/editarea/edit_area_full.js'.$sversion );
		$document->addScript( JURI::root( true ).'/plugins/editors-xtd/sourcerer/js/script.js'.$sversion );

		$script = "
			editAreaLoader.init({
				id: 'source',	// id of the textarea to transform
				start_highlight: true,	// if start with highlight
				allow_resize: 'y',
				allow_toggle: false,
				word_wrap: true,
				language: '".$language."',
				syntax: 'php',
				toolbar: 'fullscreen, |, undo, redo, |, select_font, |, syntax_selection, |, highlight, reset_highlight, word_wrap',
				syntax_selection_allow: 'css,html,js,php'
			});

			var sourcerer_syntax_word = '".$system_params->syntax_word."';
			var sourcerer_editorname = '".JRequest::getString( 'name', 'text' )."';
			var sourcerer_default_addsourcetags = ".(int) $params->addsourcetags.";
			var sourcerer_root = '".JURI::root( true )."';

			window.addEvent( 'domready', function() { sourcerer_init(); });
		";
		$document->addScriptDeclaration( $script );
		$document->addStyleSheet( JURI::root( true ).'/plugins/system/nnframework/css/popup.css'.$version );
		$document->addStyleSheet( JURI::root( true ).'/plugins/editors-xtd/sourcerer/css/popup.css'.$sversion );

		$params->code = '';
		if ( $params->use_example_code == 1 || ( $app->isAdmin() && $params->use_example_code == 2 ) ) {
			$params->code = str_replace( '<br />', "\n", $params->example_code );
		}

		echo $this->getHTML( $params );
	}

	function getHTML( &$params )
	{
		$app = JFactory::getApplication();

		JHtml::_( 'behavior.tooltip' );

		ob_start();
		?>
	<div style="margin: 0 10px;">
		<form action="index.php" id="sourceForm" method="post">
			<fieldset>
				<legend style="display:none;"></legend>
				<div style="float: left">
					<h1><?php echo JText::_( 'SRC_SOURCERER_CODE_HELPER' ); ?></h1>
				</div>
				<div style="float: right; text-align: right;">
					<div class="button2-left">
						<div class="blank hasicon apply">
							<a rel="" onclick="sourcerer_insertText();window.parent.SqueezeBox.close();"
								href="javascript://"
								title="<?php echo JText::_( 'SRC_INSERT' ) ?>"><?php echo JText::_( 'SRC_INSERT' ) ?></a>
						</div>
					</div>
					<div class="button2-left">
						<div class="blank hasicon cancel">
							<a rel=""
								onclick="if ( confirm( '<?php echo JText::_( 'NN_ARE_YOU_SURE' ); ?>' ) ) { window.parent.SqueezeBox.close(); }"
								href="javascript://"
								title="<?php echo JText::_( 'JCANCEL' ) ?>"><?php echo JText::_( 'JCANCEL' ) ?></a>
						</div>
					</div>
				</div>
			</fieldset>

			<textarea id="source" class="source" name="source" cols="" rows=""><?php echo $params->code ?></textarea>

			<fieldset>
				<legend style="display:none;"></legend>
				<div style="float: right; text-align: right;">
					<div class="bar-option">
						<input type="checkbox" name="keepindent" id="keepindent" class="checkbox"
							value="1"<?php if ( $params->keepindent ) {
							echo ' checked="checked"';
						} ?> />
						<label style="clear:none;" class="hasTip"
							title="<?php echo JText::_( 'SRC_PRESERVE_INDENTATION' ).'::'.JText::_( 'SRC_PRESERVE_INDENTATION_DESC' ); ?>">
							<?php echo JText::_( 'SRC_PRESERVE_INDENTATION' ) ?>
						</label>
					</div>
					<div class="bar-option">
						<input type="checkbox" name="keepcolors" id="keepcolors" class="checkbox"
							value="1"<?php if ( $params->keepcolors ) {
							echo ' checked="checked"';
						} ?> />
						<label style="clear:none;" class="hasTip"
							title="<?php echo JText::_( 'SRC_PRESERVE_COLORS' ).'::'.JText::_( 'SRC_PRESERVE_COLORS_DESC' ); ?>">
							<?php echo JText::_( 'SRC_PRESERVE_COLORS' ) ?>
						</label>
					</div>
					<div class="button2-left">
						<div class="blank hasicon apply">
							<a rel="" onclick="sourcerer_insertText();window.parent.SqueezeBox.close();"
								href="javascript://"
								title="<?php echo JText::_( 'SRC_INSERT' ) ?>"><?php echo JText::_( 'SRC_INSERT' ) ?></a>
						</div>
					</div>
					<div class="button2-left">
						<div class="blank hasicon cancel">
							<a rel=""
								onclick="if ( confirm( '<?php echo JText::_( 'NN_ARE_YOU_SURE' ); ?>' ) ) { window.parent.SqueezeBox.close(); }"
								href="javascript://"
								title="<?php echo JText::_( 'JCANCEL' ) ?>"><?php echo JText::_( 'JCANCEL' ) ?></a>
						</div>
					</div>
				</div>

				<div class="button2-left">
					<div class="blank">
						<a rel="" onclick="eAL.toggle( 'source' );return false;" href="javascript://;"
							class="hasTip"
							title="<?php echo JText::_( 'SRC_TOGGLE_EDITOR' ).'::'.JText::_( 'SRC_TOGGLE_EDITOR_DESC' ); ?>">
							<?php echo JText::_( 'SRC_TOGGLE_EDITOR' ) ?></a>
					</div>
				</div>
				<div class="button2-left">
					<div class="blank hasicon sourcetags_0" id="sourcetags_button">
						<a rel="" onclick="sourcerer_toggleSourceTags();return false;" href="javascript://;"
							class="hasTip"
							title="<?php echo JText::_( 'SRC_TOGGLE_SOURCE_TAGS' ).'::'.JText::_( 'SRC_TOGGLE_SOURCE_TAGS_DESC' ); ?>">
							<?php echo JText::_( 'SRC_TOGGLE_SOURCE_TAGS' ) ?></a>
					</div>
				</div>
				<div class="button2-left">
					<div class="blank hasicon tagstyle_0" id="tagstyle_button">
						<a rel="" onclick="sourcerer_toggleTagStyle();return false;" href="javascript://;"
							class="hasTip"
							title="<?php echo JText::_( 'SRC_TOGGLE_TAG_STYLE' ).'::'.JText::_( 'SRC_TOGGLE_TAG_STYLE_DESC' ); ?>">
							<?php echo JText::_( 'SRC_TOGGLE_TAG_STYLE' ) ?></a>
					</div>
				</div>

			</fieldset>
		</form>
	</div>
	<?php
		if ( $app->isAdmin() ) {
			$user = JFactory::getUser();
			if ( $user->authorise( 'core.admin', 1 ) ) {
				$db = JFactory::getDBO();
				$query = "
					SELECT extension_id
					FROM #__extensions
					WHERE `type` = 'plugin'
					AND `folder` = 'editors-xtd'
					AND `element` = 'sourcerer'
					LIMIT 1";
				$db->setQuery( $query );
				$pluginid = $db->loadResult();
				if ( $pluginid ) {
					echo '<em>'.html_entity_decode( JText::_( 'SRC_SEE_THE_PLUGIN_FOR_SETTINGS' ), ENT_COMPAT, 'UTF-8' ).' (<a href="'.JURI::base( true ).'/index.php?option=com_plugins&task=plugin.edit&extension_id='.$pluginid.'" target="_blank">'.JText::_( 'SRC_EDITOR_BUTTON' ).'</a>)</em>';
				}
			}
		}
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
}