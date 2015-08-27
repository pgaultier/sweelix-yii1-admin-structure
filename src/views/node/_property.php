<?php
/**
 * File _property.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.1.0
 * @link      http://www.sweelix.net
 * @category  views
 * @package   sweelix.yii1.admin.structure.views.node
 */
use sweelix\yii1\web\helpers\Html;
use sweelix\yii1\ext\components\Helper;
use sweelix\yii1\ext\entities\Node;
?>
		<fieldset>
			<?php echo Html::activeLabel($node, 'nodeStatus')?><br/>
			<?php echo Html::activeRadioButtonList($node, 'nodeStatus', Node::getAvailableStatus(), array('separator'=>"\n", 'labelOptions'=>array('class'=>'fixed')))?><br/>
			<?php echo Html::activeLabel($node, 'nodeDisplayMode')?><br/>
			<?php echo Html::activeRadioButtonList($node, 'nodeDisplayMode', Node::getAvailableDisplayModes(), array('separator'=>' ', 'class'=>'displayMode', 'labelOptions'=>array('class'=>'fixed')))?><br/>
			<?php echo Html::activeLabel($node, 'templateId', array('class'=>'modeList'))?><br/>
			<?php echo Html::activeDropDownList($node, 'templateId', Html::listDataFromActiveDataProvider($templatesDataProvider, 'templateId', 'templateTitle'), array('class'=>'modeList classic'))?><br/>
			<?php echo Html::activeLabel($node, 'nodeRedirection', array('class'=>'modeRedirection'))?><br/>
			<?php echo Html::activeDropDownList($node, 'nodeRedirection', Helper::linearizeNodesToDropDownList($treeDataProvider), array('class'=>'modeRedirection classic'))?><br/>
			<?php echo Html::link(Yii::t('structure', 'Reset'), array('node/property', 'nodeId' => $node->nodeId), array('class' => 'button danger'))?>
			<?php echo Html::htmlButton(Yii::t('structure', 'Ok'), array('type' => 'submit', 'class' => 'success'))?>

		</fieldset>
<?php
if((isset($notice) === true) && ($notice === true)) {
	if($node->hasErrors() === false) {
		echo Html::script(Html::raiseShowNotice(array(
				'title' => '<span class="icon-bubble-dots light"></span> '. Yii::t('structure', 'Info'),
				'close' => '<span class="icon-circle-cancel light">x</span>',
				'text' => Yii::t('structure', 'Node properties were saved'),
				'cssClass' => 'success'
		)));
	} else {
		echo Html::script(Html::raiseShowNotice(array(
				'title' => '<span class="icon-bubble-exclamation light"></span> '. Yii::t('structure', 'Error'),
				'close' => '<span class="icon-circle-cancel light">x</span>',
				'text' => Yii::t('structure', 'Node properties were not saved'),
				'cssClass' => 'danger'
		)));
	}
}
