<?php
/**
 * File _itemView.php
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

$class = $data->contentStatus;
if($data->isPublishable(false) === false) {
	$class = $class." unpublished";
}
?>
<?php echo Html::openTag('tr', array(
		'class' => $class,
		'data-target' => '#'.$widget->getId(),
		'data-content-id' => $data->contentId,
		'data-mode' => 'replace',
		'data-url-move' => Html::normalizeUrl(array('moveContent', 'contentId'=>$data->contentId, 'nodeId' => $data->node->nodeId, 'page' => $widget->dataProvider->pagination->currentPage))
	)); ?>
	<td class="main-id">
		<?php echo Html::link(
			$data->contentId,
			array('content/', 'contentId' => $data->contentId ),
			array('title' => Yii::t('structure', 'ID'))
		); ?>
	</td>
	<td class="status">
		<?php
			if($data->contentStatus === 'offline')
				echo Html::tag('span', array('class' => 'icon-circle-block', 'title' => Yii::t('structure', $data->contentStatus)), $data->contentStatus);
			else
				echo Html::link(Yii::t('structure', 'offline'),
						array('changeContentStatus', 'contentId' => $data->contentId, 'nodeId' => $data->nodeId, 'mode' => 'offline', 'page' => $widget->dataProvider->pagination->currentPage),
						array(
							'class' => 'icon-circle-block light inverse ajaxRefresh', 'title' => Yii::t('structure', 'offline'),
							'data-target' => '#'.$widget->getId(),
							'data-mode' => 'replace'
						)
				);
		?>
		<?php
			if($data->contentStatus === 'draft')
				echo Html::tag('span', array('class' => 'icon-file-lines', 'title' => Yii::t('structure', $data->contentStatus)), $data->contentStatus);
			else
				echo Html::link(Yii::t('structure', 'draft'),
						array('changeContentStatus', 'contentId' => $data->contentId, 'nodeId' => $data->nodeId, 'mode' => 'draft', 'page' => $widget->dataProvider->pagination->currentPage),
						array(
							'class' => 'icon-file-lines light inverse ajaxRefresh', 'title' => Yii::t('structure', 'draft'),
							'data-target' => '#'.$widget->getId(),
							'data-mode' => 'replace'
						)
				);
		?>
		<?php
			if($data->contentStatus === 'online')
				echo Html::tag('span', array('class' => 'icon-circle-check', 'title' => Yii::t('structure', $data->contentStatus)), $data->contentStatus);
			else
				echo Html::link(Yii::t('structure', 'online'),
						array('changeContentStatus', 'contentId' => $data->contentId, 'nodeId' => $data->nodeId, 'mode' => 'online', 'page' => $widget->dataProvider->pagination->currentPage),
						array('class' => 'icon-circle-check light inverse ajaxRefresh', 'title' => Yii::t('structure', 'online'),
							'data-target' => '#'.$widget->getId(),
							'data-mode' => 'replace'
						)
				);
		?>
	</td>
	<td>
		<?php echo Html::link(
			$data->contentTitle,
			array('content/', 'contentId' => $data->contentId ),
			array('title' => $data->contentTitle)
		); ?>
	</td>
	<td class="author">
		<?php
			if (Yii::app()->user->checkAccess('users') === true) {
				echo Html::link(
					$data->author->authorFirstname.' '.$data->author->authorLastname,
					array('/sweeft/users/user/edit', 'id' => $data->author->authorId),
					array('title'=>$data->author->authorFirstname.' '.$data->author->authorLastname)
				);
			} else {
				echo Html::tag('span', array('title' => $data->author->authorFirstname.' '.$data->author->authorLastname), $data->author->authorFirstname.' '.$data->author->authorLastname);
			}
		?>
	</td>
	<td class="order">
		<?php echo Html::link(
			Yii::t('structure', 'Top'),
				array('moveContent', 'target'=>'top', 'contentId'=>$data->contentId, 'nodeId'=>$data->nodeId, 'page' => $widget->dataProvider->pagination->currentPage),
				array('class' => 'icon-arrow-top ajaxRefresh', 'title' => Yii::t('structure', 'Top'), 'data-target' => '#'.$widget->getId(), 'data-mode' => 'replace')
			) ;
		?>
		<?php echo Html::link(
			Yii::t('structure', 'Up'),
				array('moveContent', 'target'=>'up', 'contentId'=>$data->contentId, 'nodeId'=>$data->nodeId, 'page' => $widget->dataProvider->pagination->currentPage),
				array('class' => 'icon-arrow-up ajaxRefresh', 'title' => Yii::t('structure', 'Up'), 'data-target' => '#'.$widget->getId(), 'data-mode' => 'replace')
			);
		?>
		<span title="<?php echo Yii::t('structure', 'Order'); ?>"><?php echo str_pad($data->contentOrder,2,'0', STR_PAD_LEFT);?></span>
		<?php echo Html::link(
			Yii::t('structure', 'Down'),
				array('moveContent', 'target'=>'down', 'contentId'=>$data->contentId, 'nodeId'=>$data->nodeId, 'page' => $widget->dataProvider->pagination->currentPage),
				array('class' => 'icon-arrow-down ajaxRefresh', 'title' => Yii::t('structure', 'Down'), 'data-target' => '#'.$widget->getId(), 'data-mode' => 'replace')
			);
		?>
		<?php echo Html::link(
			Yii::t('structure', 'Bottom'),
				array('moveContent', 'target'=>'bottom', 'contentId'=>$data->contentId, 'nodeId'=>$data->nodeId, 'page' => $widget->dataProvider->pagination->currentPage),
				array('class' => 'icon-arrow-bottom ajaxRefresh', 'title' => Yii::t('structure', 'Bottom'), 'data-target' => '#'.$widget->getId(), 'data-mode' => 'replace')
			);
		?>
	</td>
	<td class="action">
		<?php echo Html::link(
			Yii::t('structure', 'Edit'),
			array('content/', 'contentId' => $data->contentId ),
			array('title' => Yii::t('structure', 'Edit'), 'class' => 'icon-edit')
		); ?>

		<?php echo Html::link(
			Yii::t('structure', 'Delete'),
			array('detachContent','contentId' => $data->contentId, 'nodeId'=>$data->nodeId, 'page' => $widget->dataProvider->pagination->currentPage),
			array('class' => 'icon-trash ajaxRefresh', 'title' => Yii::t('structure', 'Delete'), 'data-target' => '#'.$widget->getId(), 'data-mode' => 'replace')
		); ?>
	</td>
<?php echo Html::closeTag('tr')?>
