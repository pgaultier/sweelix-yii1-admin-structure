<?php
/**
 * File _footerView.php
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

$currentPage = $widget->dataProvider->pagination->currentPage;
$previousPage = max($currentPage - 1, 0);
$nexPage = min($currentPage + 1, $widget->dataProvider->pagination->pageCount - 1);
?>
<tr>
	<th colspan="6">
		<?php if($currentPage > 0) echo Html::link('<', array('node/listContent', 'nodeId' => $this->currentNode->nodeId, 'page' => $previousPage), array('class' => 'button small'));?>
		<?php if($widget->dataProvider->totalItemCount > 0):?>
			<span class="in-button"><?php echo(Yii::t('structure', $title, array($widget->dataProvider->totalItemCount, '{pageNum}' => ($currentPage + 1), '{pageCount}' => $widget->dataProvider->pagination->pageCount)));?></span>
		<?php endif;?>
		<?php if($currentPage < ($widget->dataProvider->pagination->pageCount-1)) echo Html::link('>', array('node/listContent', 'nodeId' => $this->currentNode->nodeId, 'page' => $nexPage), array('class' => 'button small'));?>
	</th>
</tr>
