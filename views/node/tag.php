<?php
/**
 * File tag.php
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

Yii::app()->getClientScript()->registerSweelixScript('callback');
?>

<?php $this->widget('sweelix\yii1\admin\core\widgets\Breadcrumb', array(
	'elements' => $breadcrumb,
	'id' => 'breadcrumb',
)); ?>


<nav>
	<br><br><br>
	<ul class="shortcuts">
		<li>
			<?php echo Html::link(
					Yii::t('structure', 'Create new content'),
					array('content/new', 'nodeId'=>$node->nodeId),
					array('title'=>Yii::t('structure', 'Create new content'))
			);?>
		</li>
		<li>
			<?php echo Html::link(
					Yii::t('structure', 'Create new node'),
					array('node/new', 'nodeId'=>$node->nodeId),
					array('title'=>Yii::t('structure', 'Create new node'))
			);?>
		</li>
	</ul>
	<?php $this->widget('sweelix\yii1\admin\structure\widgets\TreeNodes', array('node' => $node)); ?>
</nav>
<section>
	<div id="content">
		<?php $this->widget('sweelix\yii1\admin\core\widgets\ContextMenu', $mainMenu); ?>
		<?php echo Html::beginAjaxForm(); ?>
		<?php $this->renderPartial('_tag', array(
				'node'=>$node,
				'groups'=>$groups,
				'notice' => (isset($notice)?$notice:false),
		)); ?>
		<?php echo Html::endForm();?>
	</div>
</section>

