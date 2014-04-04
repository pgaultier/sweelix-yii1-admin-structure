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
 * @package   sweelix.yii1.admin.structure.views.node
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
					array('content/new', 'nodeId'=>$sourceNode->nodeId),
					array('title'=>Yii::t('structure', 'Create new content'))
			);?>
		</li>
		<li>
			<?php echo Html::link(
					Yii::t('structure', 'Create new node'),
					array('node/new', 'nodeId'=>$sourceNode->nodeId),
					array('title'=>Yii::t('structure', 'Create new node'))
			);?>
		</li>
	</ul>
	<?php $this->widget('sweelix\yii1\admin\structure\widgets\TreeNodes', array('node' => $sourceNode)); ?>
</nav>


<section>
	<div id="content">
		<?php $this->widget('sweelix\yii1\admin\core\widgets\ContextMenu', $mainMenu); ?>
		<?php echo Html::beginForm(array('node/delete', 'nodeId'=>$sourceNode->nodeId)); ?>
		<fieldset>
			<label><?php echo Yii::t('structure', 'Delete node'); ?></label><br/>
			<?php echo Html::activeCheckBox($node, 'selected'); ?>
			<?php echo Html::label(
				Yii::t('structure', 'Delete node "{node}"',
					array(
						'{node}'=>$sourceNode->nodeTitle)
					),
					Html::activeId($node, 'selected'));
			?><br/>
			<?php echo Html::activeHiddenField($node, 'targetNodeId'); ?>
			<?php echo Html::htmlButton(Yii::t('structure', 'Delete'), array('type' => 'submit', 'class' => 'medium danger'))?>
			<br />
			<label><?php echo Yii::t('structure', 'View'); ?></label><br/>
			<?php
				$realUrl = Yii::app()->getModule('sweeft')->getRealSiteUrl();
				if($realUrl !== '') {
					$realUrl = $realUrl . str_replace(Yii::app()->getBaseUrl(), '', $sourceNode->getUrl());
				} else {
					$realUrl = $sourceNode->getUrl();
				}
				echo Html::link(
					Yii::t('structure', 'Open node'),
					$realUrl,
					array('target' => '_blank', 'class' => 'button info medium')
				);
			?>
		</fieldset>
		<?php echo Html::endForm(); ?>
	</div>
</section>
