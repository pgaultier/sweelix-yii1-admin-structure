<?php
/**
 * File Module.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   2.0.1
 * @link      http://www.sweelix.net
 * @category  structure
 * @package   sweelix.yii1.admin.structure
 */

namespace sweelix\yii1\admin\structure;
use sweelix\yii1\admin\core\components\BaseModule;

/**
 * Class Module
 *
 * This module handle the whole CMS structure (rubrics and articles)
 * @see Module in components.
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   2.0.1
 * @link      http://www.sweelix.net
 * @category  structure
 * @package   sweelix.yii1.admin.structure
 * @since     1.0.0
 */
class Module extends BaseModule {
	/**
	 * @var string controllers namespace
	 */
	public $controllerNamespace = 'sweelix\yii1\admin\structure\controllers';

	/**
	 * @var string default controller name
	 */
	public $defaultController = 'node';

	/**
	 * @var integer page size
	 */
	public $pageSize = 20;

	/**
	 * Init the module with specific information.
	 * @see CModule::init()
	 *
	 * @return void
	 * @since  1.2.0
	 */
	protected function init() {
		$this->basePath = __DIR__;
		\Yii::setPathOfAlias($this->getShortId(), __DIR__);
		\Yii::app()->getMessages()->extensionPaths[$this->getShortId()] = $this->getShortId().'.messages';
		parent::init();
	}
}
