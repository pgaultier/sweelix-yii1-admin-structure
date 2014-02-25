<?php
/**
 * File step1.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   2.0.1
 * @link      http://www.sweelix.net
 * @category  views
 * @package   sweelix.yii1.admin.structure.views.node
 */
use sweelix\yii1\web\helpers\Html;

?>

<?php $this->widget('sweelix\yii1\admin\core\widgets\Breadcrumb', array(
	'elements' => array(
		array(
			'content' => Yii::t('structure', 'Step {n}', array('{n}' => 1 )),
			// 'url' => array('step1', 'nodeId' => $sourceNode->nodeId),
		),
	)
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
		<li class="active">
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
		<?php echo Html::beginAjaxForm(); ?>
			<?php $this->renderPartial('_property', array(
					'node' => $node,
					'treeDataProvider'=>$treeDataProvider,
					'templatesDataProvider'=>$templatesDataProvider,
					'notice' => (isset($notice)?$notice:false)
			)); ?>
		<?php echo Html::endForm();?>
	</div>
</section>
