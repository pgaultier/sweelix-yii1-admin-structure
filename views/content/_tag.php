<?php
/**
 * File _tag.php
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
?>
<?php
	$this->renderPartial('_contentHeader', array('content'=>$content));
?>
<fieldset>
	<?php
		foreach($groups as $cntGroup => $group):
			$tagData = Html::listData($group->tags, 'tagId', 'tagTitle');
	?>
	<?php echo Html::label($group->groupTitle, false);?><br/>
	<?php echo Html::activeHiddenField($group, '['.$cntGroup.']groupId'); ?>
	<?php
		if($group->groupType === 'multiple'):
			echo Html::activeListBox($group, '['.$cntGroup.']groupSelectedTags', $tagData, array( 'class' => 'classic', 'multiple' => 'multiple'));
		else:
			echo Html::activeListBox($group, '['.$cntGroup.']groupSelectedTags', $tagData, array( 'class' => 'classic', 'prompt' => ''));
		endif;
	?>
	<br/>
	<?php
	endforeach;
?>
<?php echo Html::link(Yii::t('structure', 'Reset'), array('content/tag', 'contentId' => $content->contentId), array('class' => 'button danger'))?>
<?php echo Html::submitButton(Yii::t('structure', 'Ok'), array('class' => 'success'))?>

</fieldset>
<?php
if((isset($notice) === true) && ($notice === true)) {
	if($content->hasErrors() === false) {
		echo Html::script(Html::raiseShowNotice(array(
				'title' => '<span class="icon-bubble-dots light"></span> '. Yii::t('structure', 'Info'),
				'close' => '<span class="icon-circle-cancel light">x</span>',
				'text' => Yii::t('structure', 'Content tags were saved'),
				'cssClass' => 'success'
		)));
	} else {
		echo Html::script(Html::raiseShowNotice(array(
				'title' => '<span class="icon-bubble-exclamation light"></span> '. Yii::t('structure', 'Error'),
				'close' => '<span class="icon-circle-cancel light">x</span>',
				'text' => Yii::t('structure', 'Content tags were not saved'),
				'cssClass' => 'danger'
		)));
	}
}
