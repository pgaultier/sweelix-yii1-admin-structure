<?php
/**
 * File _headerView.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.0.1
 * @link      http://www.sweelix.net
 * @category  views
 * @package   sweelix.yii1.admin.structure.views.node
 */

?>
<tr>
	<th class="main-id">
		<?php echo Yii::t('structure', 'ID') ?>
	</th>
	<th class="status">
		<?php echo Yii::t('structure', 'Status') ?>
	</th>
	<th>
		<?php echo Yii::t('structure', 'Title') ?>
	</th>
	<th class="author">
		<?php echo Yii::t('structure', 'Author') ?>
	</th>
	<th class="order">
		<?php echo Yii::t('structure', 'Order') ?>
	</th>
	<th class="action">
		<?php echo Yii::t('structure', 'Action') ?>
	</th>
</tr>