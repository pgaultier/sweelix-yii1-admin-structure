<?php
/**
 * File TreeNodes.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.0.2
 * @link      http://www.sweelix.net
 * @category  widgets
 * @package   sweelix.yii1.admin.structure.widgets
 */

namespace sweelix\yii1\admin\structure\widgets;
use sweelix\yii1\ext\entities\Node;
use sweelix\yii1\ext\components\Helper;
use sweelix\yii1\web\helpers\Html;

/**
 * Class TreeNodes
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.0.2
 * @link      http://www.sweelix.net
 * @category  widgets
 * @package   sweelix.yii1.admin.structure.widgets
 */
class TreeNodes extends \CWidget {
	/**
	 * @var $_nodes collection list of nodes
	 */
	private $_nodes = null;

	/**
	 * @var Node current active node
	 */
	public $node = null;

	/**
	 * Init widget
	 * Called by CController::beginWidget()
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function init() {
		\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.widgets');
		$criteria = new \CDbCriteria();
		$criteria->order = 'nodeLeftId asc';
		//XXX: be carefull, we must pass full tree structure.
	}

	/**
	 * Render widget
	 * Called by CController::endWidget()
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function run() {
		\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.widgets');
		echo Html::openTag('div', array('id'=>'treemenu'));
		echo Html::tag('div', array('class' => 'masking'), '');
		\Yii::beginProfile('sweelix.yii1.admin.structure.widgets.linearizeNodes');
		$this->_nodes = Helper::linearizeNodesToArray(Node::model()->findAll(array('order' => 'nodeLeftId')));
		echo $this->convertNodesToHtml($this->_nodes, true);
		\Yii::endProfile('sweelix.yii1.admin.structure.widgets.linearizeNodes');
		echo Html::closeTag('div');
	}

	/**
	 * Render tree structure
	 *
	 * @param array   $data          array of Node
	 * @param int     $currentNodeId current nodeId
	 * @param boolean $start         first call of the method
	 *
	 * @return string
	 * @since  1.2.0
	 */
	public function convertNodesToHtml($data, $start = false) {
		\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.widgets');
		if($start === true) {
			$str = Html::openTag('ul', array('class'=>'sortableTree'));
		} else {
			$str = Html::openTag('ul', array());
		}
		foreach($data as $row) {
			$linkClass = '';
			if(($this->node !== null) && ($row['node']->nodeLeftId <= $this->node->nodeLeftId) && ($this->node->nodeRightId <= $row['node']->nodeRightId)) {
				$linkClass = 'path';
			}
			$str .= Html::tag('li',
				array(
					'data-target' => "#treemenu",
					'data-node-id' => $row['node']->nodeId,
					'data-mode' => 'replace',
					'data-url-move' => Html::normalizeUrl(array('node/move', 'nodeId' => ($this->node !== null)?$this->node->nodeId:'')),
				),
				Html::link($row['node']->nodeTitle,
					array('node/', 'nodeId' => $row['node']->nodeId),
					array(
						'title'=>$row['node']->nodeTitle,
						'class' => $linkClass,
					)
				),
				false
			);
			if($row['children'] !== null) {
				$str .= $this->convertNodesToHtml($row['children']);
			}
			$str .= Html::closeTag('li');
		}
		$str .= Html::closeTag('ul');
		return $str;
	}
}
