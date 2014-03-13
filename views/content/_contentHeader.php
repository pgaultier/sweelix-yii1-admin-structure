<?php
/**
 * File _contentHeader.php
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

$class = $content->contentStatus;
$id = uniqid('contentHead-');
if($content->isPublishable(false) === false) {
	$class = $class." unpublished";
}
?>
<?php echo Html::openTag('table', array(
		'id' => $id,
	)); ?>
	<tbody>
<?php echo Html::openTag('tr', array(
		'class' => $class,
	)); ?>
	<td class="main-id">
		<?php echo Html::link(
			$content->contentId,
			array('content/', 'contentId' => $content->contentId ),
			array('title' => Yii::t('structure', 'ID'))
		); ?>
	</td>
	<td class="status">
		<?php
			if($content->contentStatus === 'offline')
				echo Html::tag('span', array('class' => 'icon-circle-block', 'title' => Yii::t('structure', $content->contentStatus)), $content->contentStatus);
			else
				echo Html::link(Yii::t('structure', 'offline'),
						array('changeStatus', 'contentId' => $content->contentId, 'nodeId' => $content->nodeId, 'mode' => 'offline'),
						array(
							'class' => 'icon-circle-block light inverse ajaxRefresh', 'title' => Yii::t('structure', 'offline'),
							'data-target' => '#'.$id,
							'data-mode' => 'replace'
						)
				);
		?>
		<?php
			if($content->contentStatus === 'draft')
				echo Html::tag('span', array('class' => 'icon-file-lines', 'title' => Yii::t('structure', $content->contentStatus)), $content->contentStatus);
			else
				echo Html::link(Yii::t('structure', 'draft'),
						array('changeStatus', 'contentId' => $content->contentId, 'nodeId' => $content->nodeId, 'mode' => 'draft'),
						array(
							'class' => 'icon-file-lines light inverse ajaxRefresh', 'title' => Yii::t('structure', 'draft'),
							'data-target' => '#'.$id,
							'data-mode' => 'replace'
						)
				);
		?>
		<?php
			if($content->contentStatus === 'online')
				echo Html::tag('span', array('class' => 'icon-circle-check', 'title' => Yii::t('structure', $content->contentStatus)), $content->contentStatus);
			else
				echo Html::link(Yii::t('structure', 'online'),
						array('changeStatus', 'contentId' => $content->contentId, 'nodeId' => $content->nodeId, 'mode' => 'online'),
						array('class' => 'icon-circle-check light inverse ajaxRefresh', 'title' => Yii::t('structure', 'online'),
							'data-target' => '#'.$id,
							'data-mode' => 'replace'
						)
				);
		?>
	</td>
	<td>
		<?php echo Html::link(
			$content->contentTitle,
			array('content/', 'contentId' => $content->contentId ),
			array('title' => $content->contentTitle)
		); ?>
	</td>
	<td class="author">
		<?php
			if (Yii::app()->user->checkAccess('users') === true) {
				echo Html::link(
					$content->author->authorFirstname.' '.$content->author->authorLastname,
					array('/sweeft/users/user/edit', 'id' => $content->author->authorId),
					array('title'=>$content->author->authorFirstname.' '.$content->author->authorLastname)
				);
			} else {
				echo Html::tag('span', array('title' => $content->author->authorFirstname.' '.$content->author->authorLastname), $content->author->authorFirstname.' '.$content->author->authorLastname);
			}
		?>
	</td>
<?php echo Html::closeTag('tr')?>
	</tbody>
</table>