<?php
/**
 * File admin.php
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
<?php $this->widget('sweelix\yii1\admin\core\widgets\Breadcrumb', array(
	'elements' => $breadcrumb,
)); ?>
<nav>
	<br><br><br>
	<ul class="shortcuts">
		<li>
			<?php echo Html::link(
					Yii::t('structure', 'Create new content'),
					array('content/new', 'nodeId'=>(($node !== null) ? $node->nodeId:'')),
					array('title'=>Yii::t('structure', 'Create new content'))
			);?>
		</li>
		<li>
			<?php echo Html::link(
					Yii::t('structure', 'Create new node'),
					array('node/new', 'nodeId'=>(($node !== null) ? $node->nodeId:'')),
					array('title'=>Yii::t('structure', 'Create new node'))
			);?>
		</li>
	</ul>
	<?php $this->widget('sweelix\yii1\admin\structure\widgets\TreeNodes', array('node' => $node)); ?>
</nav>

<section>
		<div id="content">
			<?php $this->widget('sweelix\yii1\admin\core\widgets\ContextMenu', $mainMenu); ?>
			<?php $this->renderPartial('_admin', array('content' => $sourceContent));?>
			<?php echo Html::beginForm(array('content/delete', 'contentId'=>$sourceContent->contentId)); ?>
			<fieldset>
				<label><?php echo Yii::t('structure', 'Delete content'); ?></label><br/>
				<?php echo Html::activeCheckBox($content, 'selected'); ?>
				<?php echo Html::label(
					Yii::t('structure', 'Delete content "{content}"',
						array(
							'{content}'=>$sourceContent->contentTitle)
						),
						Html::activeId($content, 'selected'));
				?><br/>
				<?php echo Html::activeHiddenField($content, 'targetContentId'); ?>
				<?php echo Html::submitButton(Yii::t('structure', 'Delete'), array('class' => 'medium danger')); ?><br />
				<label><?php echo Yii::t('structure', 'View'); ?></label><br/>
				<?php
					$realUrl = Yii::app()->getModule('sweeft')->getRealSiteUrl();
					if($realUrl !== '') {
						$realUrl = $realUrl . str_replace(Yii::app()->getBaseUrl(), '', $sourceContent->getUrl());
					} else {
						$realUrl = $sourceContent->getUrl();
					}
					echo Html::link(
						Yii::t('structure', 'Open content'),
						$realUrl,
						array('target' => '_blank', 'class' => 'button info medium')
					);
				?>
			</fieldset>
			<?php echo Html::endForm(); ?>
		</div>
</section>
