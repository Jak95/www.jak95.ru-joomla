<?php
/**
 * @package JCE Image Manager Extened
 * @copyright Copyright (C) 2005 - 2010 Ryan Demmer. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see licence.txt
 * JCE Image Manager Extened is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

defined('_JEXEC') or die('RESTRICTED');

class WFImageEditor extends JObject {
	/**
	 * @access	protected
	 */

	function __construct($config = array()) {
		// Call parent
		parent::__construct($config);

		$this->setProperties($config);
	}

	/**
	 * Returns a reference to a ImageProcessor object
	 *
	 * This method must be invoked as:
	 * 		<pre>  $process =ImageProcessor::getInstance();</pre>
	 *
	 * @access	public
	 * @return	ImageProcessor  The ImageProcessor object.
	 * @since	1.5
	 */

	function & getInstance($config = array()) {
		static $instance;

		if(!is_object($instance)) {
			$instance = new WFImageEditor($config);
		}
		return $instance;
	}

	function resize($src, $dest = null, $width, $height, $quality, $sx = null, $sy = null, $sw = null, $sh = null) {
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		require_once (dirname(__FILE__) . DS . 'wideimage' . DS . 'WideImage.php');
		
		if (!isset($dest) || $dest == '') {
			$dest = $src;
		}

		$ext = strtolower(JFile::getExt($src));
		$src = @JFile::read($src);

		if($src) {
			$image = @WideImage::loadFromString($src);

			// cropped thumbnail
			if(($sx || $sy) && $sw && $sh) {
				$result = @$image->crop($sx, $sy, $sw, $sh)->resize($width, $height, 'fill');
			} else {
				$result = @$image->resize($width, $height);
			}

			switch ($ext) {
				case 'jpg' :
				case 'jpeg' :
					$quality = intval($quality);
					if($this->get('ftp', 0)) {
						@JFile::write($dest, $result->asString($ext, $quality));
					} else {
						@$result->saveToFile($dest, $quality);
					}
					break;
				default :
					if($this->get('ftp', 0)) {
						@JFile::write($dest, $result->asString($ext));
					} else {
						@$result->saveToFile($dest);
					}
					break;
			}

			unset($image);
			unset($result);
		}

		if(file_exists($dest)) {
			@JPath::setPermissions($dest);
			return $dest;
		}

		return false;
	}

	function crop($file, $sx, $sy, $width, $height, $quality = 100) {
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		return false;
	}

	function rotate($file, $angle, $quality = 100) {
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		require_once (dirname(__FILE__) . DS . 'wideimage' . DS . 'WideImage.php');

		$ext = strtolower(JFile::getExt($file));
		$file = @JFile::read($file);

		if($file) {
			$image = @WideImage::loadFromString($file);

			switch ($angle) {
				case '-90' :

				case '180' :

				case '90' :
					$result = @$image->rotate($angle * -1);
					break;
				case 'vertical' :
					$result = @$image->flip();
					break;
				case 'horizontal' :
					$result = @$image->mirror();
					break;
			}
			unset($image);

			if($this->get('ftp', 0)) {
				@JFile::write($file, $result->output($ext));
			} else {
				@$result->saveToFile($file);
			}
		}

		return false;
	}

	function filter($file, $filter, $args) {
		require_once (dirname(__FILE__) . DS . 'wideimage' . DS . 'WideImage.php');

		$ext = strtolower(JFile::getExt($file));
		$src = @JFile::read($file);

		if($src) {
			$image = @WideImage::loadFromString($src);

			switch ($filter) {
				case 'desaturate' :
					$result = @$image->asGrayscale();
					break;
				case 'sepia' :
					$result = @$image->asSepia();
					break;
				case 'invert' :
					$result = @$image->asNegative();
					break;
				case 'lighten' :
					break;
				case 'darken' :
					break;
			}
			unset($image);

			if($this->get('ftp', 0)) {
				@JFile::write($file, $result->output($ext));
			} else {
				@$result->saveToFile($file);
			}

			if(file_exists($file)) {
				@JPath::setPermissions($file);
				return $file;
			}
		}

		return false;
	}

}
