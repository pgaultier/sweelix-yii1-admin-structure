<?php
/**
 * File _property.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.0.2
 * @link      http://www.sweelix.net
 * @category  views
 * @package   sweelix.yii1.admin.structure.views.content
 */
use sweelix\yii1\web\helpers\Html;
use sweelix\yii1\ext\components\Helper;
?>
<?php
	if ($create === false)
		$this->renderPartial('_contentHeader', array('content'=>$content));
?>
	<fieldset>
		<?php echo Html::activeHiddenField($content, 'contentViewed')?>
		<?php echo Html::activeLabel($content, 'contentStartDate')?><br/>
		<?php echo Html::activeTextField($content, 'contentStartDate', array('class'=>'calendar startdate medium'))?>
		<?php echo Html::activeCheckbox($content, 'contentStartDateActive', array('class'=>'activeDate'));?>
		<?php echo Html::activeLabel($content, 'contentStartDateActive');?><br/>
		<?php echo Html::activeLabel($content, 'contentEndDate')?><br/>
		<?php echo Html::activeTextField($content, 'contentEndDate', array('class'=>'calendar enddate medium'))?>
		<?php echo Html::activeCheckbox($content, 'contentEndDateActive', array('class'=>'activeDate'));?>
		<?php echo Html::activeLabel($content, 'contentEndDateActive');?><br/>
		<?php echo Html::activeLabel($content, 'templateId')?><br/>
		<?php echo Html::activeDropDownList($content, 'templateId', Html::listDataFromActiveDataProvider($templatesDataProvider, 'templateId', 'templateTitle'), array('class' => 'classic'))?><br/>
		<?php echo Html::activeLabel($content, 'nodeId')?><br/>
		<?php echo Html::activeDropDownList($content, 'nodeId', Helper::linearizeNodesToDropDownList($treeDataProvider), array('class' => 'classic'))?><br/>
		<?php if(empty($content->contentId) === true) :?>
		<?php echo Html::link(Yii::t('structure', 'Reset'), array('node/listContent', 'nodeId' => $content->nodeId), array('class' => 'button danger'))?>
		<?php else: ?>
		<?php echo Html::link(Yii::t('structure', 'Reset'), array('content/property', 'contentId' => $content->contentId), array('class' => 'button danger'))?>
		<?php endif; ?>
		<?php echo Html::htmlButton(Yii::t('structure', 'Ok'), array('type' => 'submit', 'class' => 'success'))?>
	</fieldset>

<?php
	if((isset($notice) === true) && ($notice === true)) {
		if($content->hasErrors() === false) {
			echo Html::script(Html::raiseShowNotice(array(
					'title' => '<span class="icon-bubble-dots light"></span> '. Yii::t('structure', 'Info'),
					'close' => '<span class="icon-circle-cancel light">x</span>',
					'text' => Yii::t('structure', 'Content properties were saved'),
					'cssClass' => 'success'
			)));
		} else {
			echo Html::script(Html::raiseShowNotice(array(
					'title' => '<span class="icon-bubble-exclamation light"></span> '. Yii::t('structure', 'Error'),
					'close' => '<span class="icon-circle-cancel light">x</span>',
					'text' => Yii::t('structure', 'Content properties were not saved'),
					'cssClass' => 'danger'
			)));
		}
	}
