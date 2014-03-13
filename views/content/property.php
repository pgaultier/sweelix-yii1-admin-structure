<?php
/**
 * File property.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.0.0
 * @link      http://www.sweelix.net
 * @category  views
 * @package   sweelix.yii1.admin.structure.views.content
 */
use sweelix\yii1\web\helpers\Html;

$sweeftModule = Yii::app()->getModule('sweeft');
Yii::app()->getClientScript()->registerCssFile($sweeftModule->getAssetsUrl().'/css/jquery-ui.css');
Yii::app()->getClientScript()->registerCoreScript('jquery.ui');
Yii::app()->getClientScript()->registerScriptFile(Yii::app()->getClientScript()->getCoreScriptUrl().'/jui/js/jquery-ui-i18n.min.js');
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
					array('content/new', 'nodeId'=>($content->node !== null)?$content->node->nodeId:''),
					array('title'=>Yii::t('structure', 'Create new content'))
			);?>
		</li>
		<li>
			<?php echo Html::link(
					Yii::t('structure', 'Create new node'),
					array('node/new', 'nodeId'=>($content->node !== null)?$content->node->nodeId:''),
					array('title'=>Yii::t('structure', 'Create new node'))
			);?>
		</li>
	</ul>
	<?php $this->widget('sweelix\yii1\admin\structure\widgets\TreeNodes', array('node' => $content->node)); ?>
</nav>

<section>
	<div id="content">
		<?php $this->widget('sweelix\yii1\admin\core\widgets\ContextMenu', $mainMenu); ?>
		<?php echo Html::beginAjaxForm(); ?>
			<?php $this->renderPartial('_property', array(
					'create' => $create,
					'treeDataProvider'=>$treeDataProvider,
					'templatesDataProvider'=>$templatesDataProvider,
					'content'=>$content,
					'contentsDataProvider' => $contentsDataProvider,
					'notice' => (isset($notice)?$notice:false),
			)); ?>
		<?php echo Html::endForm();?>
	</div>
</section>
