<?php
/**
 * File _detail.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.0.1
 * @link      http://www.sweelix.net
 * @category  views
 * @package   sweelix.yii1.admin.structure.views.node
 */
use sweelix\yii1\web\helpers\Html;
use sweelix\yii1\admin\core\components\ElasticForm;

$dataForm = new ElasticForm($node);
?>
		<fieldset>
			<?php echo Html::activeHiddenField($node, 'nodeSignature')?>
			<?php echo Html::activeLabel($node, 'nodeTitle')?><br/>
			<?php echo Html::activeTextField($node, 'nodeTitle', array('class' => 'classic'))?><br/>
			<?php echo Html::activeLabel($node, 'nodeUrl')?><br/>
			<?php echo Html::activeTextField($node, 'nodeUrl', array('class' => 'classic'))?><br/>
			<?php echo $dataForm->render(); ?><br/>
			<?php echo Html::link(Yii::t('structure', 'Reset'), array('node/detail', 'nodeId' => $node->nodeId), array('class' => 'button danger'))?>
			<?php echo Html::htmlButton(Yii::t('structure', 'Ok'), array('type' => 'submit', 'class' => 'success'))?>
		</fieldset>
<?php
	if((isset($notice) === true) && ($notice === true)) {
		if($node->hasErrors() === false) {
			echo Html::script(Html::raiseShowNotice(array(
					'title' => '<span class="icon-bubble-dots light"></span> '. Yii::t('structure', 'Info'),
					'close' => '<span class="icon-circle-cancel light">x</span>',
					'text' => Yii::t('structure', 'Node details were saved'),
					'cssClass' => 'success'
			)));
		} else {
			echo Html::script(Html::raiseShowNotice(array(
					'title' => '<span class="icon-bubble-exclamation light"></span> '. Yii::t('structure', 'Error'),
					'close' => '<span class="icon-circle-cancel light">x</span>',
					'text' => Yii::t('structure', 'Node details were not saved'),
					'cssClass' => 'danger'
			)));
		}
	}
