<?php
/**
 * File _detail.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   2.0.1
 * @link      http://www.sweelix.net
 * @category  views
 * @package   sweelix.yii1.admin.structure.views.content
 */
use sweelix\yii1\web\helpers\Html;
use sweelix\yii1\admin\core\components\ElasticForm;

$dataForm = new ElasticForm($content);
?>
<?php
	if ($create === false)
		$this->renderPartial('_contentHeader', array('content'=>$content));
?>
		<fieldset>
			<?php echo Html::activeHiddenField($content, 'contentSignature')?>
			<?php echo Html::activeLabel($content, 'contentTitle')?><br/>
			<?php echo Html::activeTextField($content, 'contentTitle', array('class' => 'classic'))?><br/>
			<?php echo Html::activeLabel($content, 'contentSubtitle')?><br/>
			<?php echo Html::activeTextField($content, 'contentSubtitle', array('class' => 'classic'))?><br/>
			<?php echo Html::activeLabel($content, 'contentUrl')?><br/>
			<?php echo Html::activeTextField($content, 'contentUrl', array('class' => 'classic'))?><br/>
			<?php echo $dataForm->render(); ?><br/>
			<?php if(empty($content->contentId) === true): ?>
			<?php echo Html::link(Yii::t('structure', 'Reset'), array('node/listContent', 'nodeId' => $content->nodeId), array('class' => 'button danger'))?>
			<?php else : ?>
			<?php echo Html::link(Yii::t('structure', 'Reset'), array('content/detail', 'contentId' => $content->contentId), array('class' => 'button danger'))?>
			<?php endif;?>
			<?php echo Html::submitButton(Yii::t('structure', 'Ok'), array('class' => 'success'))?>
		</fieldset>
<?php
if((isset($notice) === true) && ($notice === true)) {
	if($content->hasErrors() === false) {
		echo Html::script(Html::raiseShowNotice(array(
				'title' => '<span class="icon-bubble-dots light"></span> '. Yii::t('structure', 'Info'),
				'close' => '<span class="icon-circle-cancel light">x</span>',
				'text' => Yii::t('structure', 'Content details were saved'),
				'cssClass' => 'success'
		)));
	} else {
		echo Html::script(Html::raiseShowNotice(array(
				'title' => '<span class="icon-bubble-exclamation light"></span> '. Yii::t('structure', 'Error'),
				'close' => '<span class="icon-circle-cancel light">x</span>',
				'text' => Yii::t('structure', 'Content details were not saved'),
				'cssClass' => 'danger'
		)));
	}
}
