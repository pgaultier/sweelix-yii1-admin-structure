<?php
/**
 * File _list.php
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
?>
<?php
	//XXX: if we do not set the id, there is a problem during ajax calls because we do not reload the whole page (ie: we do not recalculate all the widgets ids).
	$this->widget('sweelix\yii1\ext\web\widgets\ListView', array(
		'dataProvider' => $contentsDataProvider,
		'id' => 'contentsList',
		'tagName' => 'table',
		'itemView' => '_itemView',
		'headerView' => '_headerView',
		'footerView' => '_footerView',
		// 'summaryView' => '_summaryView',
		'template' => "\n<thead>\n{header}\n{summary}\n</thead>\n<tbody class=\"sortable\">\n{items}\n</tbody>\n<tfoot>\n{footer}\n</tfoot>\n",
		'viewData' => array(
				'title' => '{n} content in node|{n} contents in node, page {pageNum} / {pageCount}',
		),
	));
?>
