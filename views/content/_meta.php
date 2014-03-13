<?php
/**
 * File _meta.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.0.1
 * @link      http://www.sweelix.net
 * @category  views
 * @package   sweelix.yii1.admin.structure.views.content
 */
use sweelix\yii1\web\helpers\Html;
?>
<?php
	$this->renderPartial('_contentHeader', array('content'=>$content));
?>
		<fieldset>
<?php
			foreach($metas as $i=>$meta):
?>
				<?php echo Html::label($meta->metaId, false); ?><br/>
				<?php echo Html::activeHiddenField($meta, '['.$i.']metaId');?>
				<?php echo Html::radioButton(Html::activeName($meta, '['.$i.']metaModeOverride'), !$meta->metaModeOverride, array('value'=>0));?>
				<?php echo Html::activeLabel($meta, '['.$i.']metaDefaultValue');?><br/>
				<?php echo Html::activeTextarea($meta, '['.$i.']metaDefaultValue', array('class'=>'classic', 'readonly'=>'readonly'));?><br/>
				<?php echo Html::radioButton(Html::activeName($meta, '['.$i.']metaModeOverride'), $meta->metaModeOverride, array('value'=>1)); ?>
				<?php echo Html::activeLabel($meta, '['.$i.']metaValue');?><br/>
				<?php echo Html::activeTextarea($meta, '['.$i.']metaValue', array('class'=>'classic')); ?><br/>

<?php
			endforeach;
?>
			<?php echo Html::link(Yii::t('structure', 'Reset'), array('content/meta', 'contentId' => $content->contentId), array('class' => 'button danger'))?>
			<?php echo Html::submitButton(Yii::t('structure', 'Ok'), array('class' => 'success')); ?>
		</fieldset>
<?php
if((isset($notice) === true) && ($notice === true)) {
		echo Html::script(Html::raiseShowNotice(array(
			'title' => '<span class="icon-bubble-dots light"></span> '. Yii::t('structure', 'Info'),
			'close' => '<span class="icon-circle-cancel light">x</span>',
			'text' => Yii::t('structure', 'Content metas were saved'),
			'cssClass' => 'success'
		)));
	}
