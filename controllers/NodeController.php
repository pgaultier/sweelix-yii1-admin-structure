<?php
/**
 * File NodeController.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.1.0
 * @link      http://www.sweelix.net
 * @category  controllers
 * @package   sweelix.yii1.admin.structure.controllers
 */

namespace sweelix\yii1\admin\structure\controllers;
use sweelix\yii1\admin\core\web\Controller;
use sweelix\yii1\ext\db\CriteriaBuilder;
use sweelix\yii1\ext\entities\Node;
use sweelix\yii1\ext\entities\Content;
use sweelix\yii1\ext\entities\NodeTag;
use sweelix\yii1\ext\entities\NodeMeta;
use sweelix\yii1\admin\core\models\Node as FormNode;
use sweelix\yii1\admin\core\models\Meta as FormMeta;
use sweelix\yii1\web\helpers\Html;

/**
 * Class NodeController
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   3.1.0
 * @link      http://www.sweelix.net
 * @category  controllers
 * @package   sweelix.yii1.admin.structure.controllers
 * @since     1.0.0
 *
 * @property mixed $templateConfig
 */
class NodeController extends Controller {

	/**
	 * @var array breadcrumbs
	 */
	private $_breadCrumb=[];

	/**
	 * Lazy load and build breadcrumb for selected id.
	 *
	 * @param integer $currentNodeId target node of the breadcrumb
	 *
	 * @return array
	 * @since  2.0.0
	 */
	public function buildBreadcrumb($currentNodeId) {
		if(isset($this->_breadCrumb[$currentNodeId]) === false) {
			$currentNode = Node::model()->findByPk($currentNodeId);
			if($currentNode !== null) {
				$criteriaBuilder = new CriteriaBuilder('node');
				$criteriaBuilder->filterBy('nodeLeftId', $currentNode->nodeLeftId, '<=');
				$criteriaBuilder->filterBy('nodeRightId', $currentNode->nodeRightId, '>=');
				$criteriaBuilder->orderBy('nodeLeftId', 'asc');
				$path = $criteriaBuilder->findAll();
				foreach($path as $element) {
					$this->_breadCrumb[$currentNodeId][] = array(
							'content' => $element->nodeTitle,
							'url' => array('node/', 'nodeId' => $element->nodeId),
					);
				}
			}
		}
		return $this->_breadCrumb[$currentNodeId];
	}

	/**
	 * Build main menu using positions (0 indexed)
	 * if mainOption or secondaryOption is set to false, thei part of the
	 * menu is not shown
	 *
	 * @param mixed $mainOption      index of selected option. false if main options should be hidden
	 * @param mixed $secondaryOption index of selected option. false if secondary options should be hidden
	 *
	 * @return array
	 * @since  2.0.0
	 */
	public function buildMainMenu($mainOption=null, $secondaryOption=null) {
		$mainMenu = array(
			'main' => array(
				array(
						'content' => \Yii::t('structure', 'Node Content'),
						'url' => array('node/', 'nodeId'=>$this->currentNode->nodeId),
						'active' => false,
				),
				array(
						'content' => \Yii::t('structure', 'Node Configuration'),
						'url' => array('node/detail', 'nodeId'=>$this->currentNode->nodeId),
						'active' => false,
				)
			),
			'secondary' => array(
				array(
						'content' => \Yii::t('structure', 'Node detail'),
						'url' => array('node/detail', 'nodeId'=>$this->currentNode->nodeId),
						'active' => false,
				),
				array(
						'content' => \Yii::t('structure', 'Properties'),
						'url' => array('node/property', 'nodeId'=>$this->currentNode->nodeId),
						'active' => false,
				),
				array(
						'content' => \Yii::t('structure', 'Associated tags'),
						'url' => array('node/tag', 'nodeId'=>$this->currentNode->nodeId),
						'active' => false,
				),
				array(
						'content' => \Yii::t('structure', 'Metadata'),
						'url' => array('node/meta', 'nodeId'=>$this->currentNode->nodeId),
						'active' => false,
				),
				array(
						'content' => \Yii::t('structure', 'Admin'),
						'url' => array('node/admin', 'nodeId'=>$this->currentNode->nodeId),
						'active' => false,
				),
			)
		);
		if($mainOption === false) {
			unset($mainMenu['main']);
		} if($mainOption !== null) {
			$mainOption = \CPropertyValue::ensureInteger($mainOption);
			if(isset($mainMenu['main'][$mainOption]) === true) {
				unset($mainMenu['main'][$mainOption]['url']);
				$mainMenu['main'][$mainOption]['active'] = true;
			}
		}

		if($secondaryOption === false) {
			unset($mainMenu['secondary']);
		} if($secondaryOption !== null) {
			$secondaryOption = \CPropertyValue::ensureInteger($secondaryOption);
			if(isset($mainMenu['secondary'][$secondaryOption]) === true) {
				unset($mainMenu['secondary'][$secondaryOption]['url']);
				$mainMenu['secondary'][$secondaryOption]['active'] = true;
			}
		}
		return $mainMenu;
	}

	/**
	 * Adding asynchronous upload / delete actions
	 * @see Html::activeAsyncFileUpload
	 *
	 * @return array
	 * @since  1.2.0
	 */
	public function actions() {
		return array(
			'asyncUpload' => 'sweelix\yii1\web\actions\UploadFile',
			'asyncDelete' => 'sweelix\yii1\web\actions\DeleteFile',
			'asyncPreview' => 'sweelix\yii1\web\actions\PreviewFile',
		);
	}

	/**
	 * Default action. Should redirect to real action
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionIndex() {
		\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
		$this->redirect(array(
			'listContent',
			'nodeId'=>\Yii::app()->request->getParam('nodeId', 0)
		));
	}


	/**
	 * Admin will be a placeholder for specific actions on node
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionAdmin() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			$node = new FormNode();
			$node->targetNodeId = $this->currentNode->nodeId;
			$node->selected = false;
			$this->render('admin', array(
				'breadcrumb' => $this->buildBreadcrumb($this->currentNode->nodeId),
				'mainMenu' => $this->buildMainMenu(1, 4),
				'node'=>$node,
				'sourceNode'=>$this->currentNode,
			));
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}

	/**
	 * Start node creation
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionNew() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			\Yii::app()->session['newnode'] = array();
			$this->redirect(array('step1', 'nodeId'=>$this->currentNode->nodeId));
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}

	/**
	 * Collect base information to create a node
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionStep1() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			$newNode = new Node();
			$newNode->setScenario('createStep1');
			$newNode->attributes = \Yii::app()->session['newnode'];
			if(isset($_POST[Html::modelName($newNode)]) === true) {
				$newNode->attributes = $_POST[Html::modelName($newNode)];
				if($newNode->validate() === true) {
					\Yii::app()->session['newnode'] = $_POST[Html::modelName($newNode)];
					$this->redirect(array('step2', 'nodeId'=>$this->currentNode->nodeId));
				}
			}
			$criteriaBuilder = new CriteriaBuilder('node');
			$criteriaBuilder->orderBy('nodeLeftId', 'asc');
			$treeDataProvider = $criteriaBuilder->getActiveDataProvider(array('pagination' => false));

			$criteriaBuilder = new CriteriaBuilder('template');
			$criteriaBuilder->filterBy('templateType', 'list');
			$criteriaBuilder->orderBy('templateTitle');
			$templatesDataProvider = $criteriaBuilder->getActiveDataProvider(array('pagination' => false));

			if(\Yii::app()->getRequest()->isAjaxRequest === false) {
				$this->render('step1', array(
					'breadcrumb' => $this->buildBreadcrumb($this->currentNode->nodeId),
					'mainMenu' => $this->buildMainMenu(false, false),
					'sourceNode' => $this->currentNode,
					'node'=>$newNode,
					'treeDataProvider'=>$treeDataProvider,
					'templatesDataProvider'=>$templatesDataProvider,
				));
			} else {
				$this->renderPartial('_property', array(
					'node' => $newNode,
					'treeDataProvider'=>$treeDataProvider,
					'templatesDataProvider'=>$templatesDataProvider,
				));

			}
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}

	/**
	 * Collect base information to create a node
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionStep2() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			$newNode = new Node();
			$newNode->setScenario('createStep2');
			$newNode->templateId = \Yii::app()->session['newnode']['templateId'];
			$newNode->reconfigure();
			$newNode->attributes = \Yii::app()->session['newnode'];
			if(($newNode->nodeDisplayMode == 'list') &&  ($newNode->templateId === null)) {
				throw new \CHttpException('500', \Yii::t('structure', 'Template is not defined for current node'));
			}
			if(isset($_POST[Html::modelName($newNode)]) === true) {
				$newNode->authorId = \Yii::app()->user->id;
				$newNode->attributes = $_POST[Html::modelName($newNode)];
				$nodeStatus = $newNode->validate();
				if ($nodeStatus === true) {
					if($newNode->save(true, null, $this->currentNode->nodeId) === true) {
						$newNode->refresh();
						$this->redirect(array('detail', 'nodeId' => $newNode->nodeId));
					}
				}
			}
			if(\Yii::app()->getRequest()->isAjaxRequest === false) {
				$this->render('step2', array(
					'breadcrumb' => $this->buildBreadcrumb($this->currentNode->nodeId),
					'mainMenu' => $this->buildMainMenu(false, false),
					'sourceNode' => $this->currentNode,
					'node' => $newNode,
				));
			} else {
				$this->renderPartial('_detail', array(
					'node' => $newNode,
				));
			}
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}

	/**
	 * Change data properties of node.
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionDetail() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			if(($this->currentNode->nodeDisplayMode == 'list') &&  ($this->currentNode->templateId === null)) {
				throw new \CHttpException('500', \Yii::t('structure', 'Template is not defined for current node'));
			}
			$notice = false;

			if(isset($_POST[Html::modelName($this->currentNode)]) === true) {
				$this->currentNode->setScenario('updateDetail');
				$this->currentNode->attributes = $_POST[Html::modelName($this->currentNode)];

				$nodeStatus = $this->currentNode->validate();

				if($nodeStatus === true) {
					if($this->currentNode->save() === true) {
						$this->currentNode->refresh();
					}
				}
				$notice = true;
			}
			if(\Yii::app()->getRequest()->isAjaxRequest === false) {
				$this->render('detail', array(
					'breadcrumb' => $this->buildBreadcrumb($this->currentNode->nodeId),
					'mainMenu' => $this->buildMainMenu(1, 0),
					'node' => $this->currentNode,
					'notice' => $notice,
				));
			} else {
				$this->renderPartial('_detail', array(
					'node' => $this->currentNode,
					'notice' => $notice,
				));
			}
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}

	/**
	 * Change presentation properties of node
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionProperty() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			$notice = false;
			if(isset($_POST[Html::modelName($this->currentNode)]) === true) {
				$this->currentNode->setScenario('updateProperty');
				$this->currentNode->attributes = $_POST[Html::modelName($this->currentNode)];
				if($this->currentNode->validate() === true) {
					$this->currentNode->save();
				}
				$notice = true;
			}

			$criteriaBuilder = new CriteriaBuilder('node');
			$criteriaBuilder->orderBy('nodeLeftId', 'asc');
			$treeDataProvider = $criteriaBuilder->getActiveDataProvider(array('pagination' => false));

			$criteriaBuilder = new CriteriaBuilder('template');
			$criteriaBuilder->filterBy('templateType', 'list');
			$criteriaBuilder->orderBy('templateTitle');
			$templatesDataProvider = $criteriaBuilder->getActiveDataProvider(array('pagination' => false));

			if(\Yii::app()->request->isAjaxRequest === true) {
				$this->renderPartial('_property', array(
					'node' => $this->currentNode,
					'treeDataProvider'=>$treeDataProvider,
					'templatesDataProvider'=>$templatesDataProvider,
					'notice' => $notice,
				));
			} else {
				$this->render('property', array(
					'breadcrumb' => $this->buildBreadcrumb($this->currentNode->nodeId),
					'mainMenu' => $this->buildMainMenu(1, 1),
					'node' => $this->currentNode,
					'treeDataProvider'=>$treeDataProvider,
					'templatesDataProvider'=>$templatesDataProvider,
					'notice' => $notice,
				));
			}
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}

	/**
	 * manage tags affected to current node
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionTag() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			$notice = false;
			$criteriaBuilder = new CriteriaBuilder('group');
			$criteriaBuilder->orderBy('groupTitle');
			$groups = $criteriaBuilder->findAll();

			if(isset($_POST[Html::modelName('sweelix\yii1\ext\entities\Group')]) === true) {
				$notice = true;
				NodeTag::model()->deleteAllByAttributes(array('nodeId'=>$this->currentNode->nodeId));
				foreach($_POST[Html::modelName('sweelix\yii1\ext\entities\Group')] as $i => $selectedTagIds) {
					if(isset($selectedTagIds['groupSelectedTags']) === true) {
						if(is_array($selectedTagIds['groupSelectedTags']) === true) {
							$groups[$i]->groupSelectedTags = $selectedTagIds['groupSelectedTags'];
						} elseif(is_string($selectedTagIds['groupSelectedTags']) === true) {
							$groups[$i]->groupSelectedTags = array($selectedTagIds['groupSelectedTags']);
						}
					}
					foreach($groups[$i]->groupSelectedTags as $tagId) {
						$nodeTag = new NodeTag();
						$nodeTag->nodeId = $this->currentNode->nodeId;
						$nodeTag->tagId = $tagId;
						$nodeTag->save();
						unset($nodeTag);
					}
				}
			} else {
				for($i=0; $i<count($groups); $i++) {
					$selectedTags = NodeTag::model()->findAllByAttributes(array('nodeId'=>$this->currentNode->nodeId));
					$selectedTagIds = array();
					foreach($selectedTags as $selectedTag) {
						$selectedTagIds[] = $selectedTag->tagId;
					}
					$availableTags = $groups[$i]->tags;
					$availableTagIds = array();
					foreach($availableTags as $availableTag) {
						$availableTagIds[] = $availableTag->tagId;
					}
					$groups[$i]->groupSelectedTags = array_intersect($selectedTagIds, $availableTagIds);
				}
			}
			if(\Yii::app()->request->isAjaxRequest === true) {
				$this->renderPartial('_tag', array(
					'groups'=>$groups,
					'node' => $this->currentNode,
					'notice' => $notice,
				));
			} else {

				$this->render('tag', array(
					'breadcrumb' => $this->buildBreadcrumb($this->currentNode->nodeId),
					'mainMenu' => $this->buildMainMenu(1, 2),
					'groups'=>$groups,
					'node' => $this->currentNode,
					'notice' => $notice,
				));
			}
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}

	/**
	 * Change status of current selected content and go back to list
	 *
	 * @param integer $page current page to display
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionChangeContentStatus($page=0) {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			$content = Content::model()->findByPk(\Yii::app()->getRequest()->getParam('contentId', 0));
			$mode = \Yii::app()->getRequest()->getParam('mode', 'draft');
			if($content !== null) {
				$content->contentStatus = $mode;
				$content->save();
			}
			$this->_renderListContent($page);
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}

	/**
	 * Reorder content and redraw list
	 *
	 * @param integer $contentId contentId to move
	 * @param integer $page      current page to display
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionMoveContent($contentId, $page=0) {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			$content = Content::model()->findByPk(\Yii::app()->request->getParam('contentId', 0));
			if($content !== null) {
				$target = \Yii::app()->request->getParam('target', 'top');
				$targetId = \Yii::app()->request->getParam('targetId', false);
				if(($content !== null) && (in_array($target, array('before', 'after')) === true ) && ($targetId !== false)) {
					$res = $content->move($target, $targetId);
				} elseif($content !== null) {
					$res = $content->move($target);
				}
			}
			$this->_renderListContent($page);
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}

	/**
	 * List contents for selected node
	 *
	 * @param integer $page current page to display
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionListContent($page=0) {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			$this->_renderListContent($page);
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}

	/**
	 * Render content list for current node
	 * currentNode must be initialised
	 *
	 * @param integer $page current page to display
	 *
	 * @return void
	 * @since  1.2.0
	 */
	private function _renderListContent($page=0) {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			$contentCriteriaBuilder = new CriteriaBuilder('content');
			$contentCriteriaBuilder->filterBy('nodeId', $this->currentNode->nodeId);
			$contentCriteriaBuilder->orderBy('contentOrder');
			if(\Yii::app()->request->isAjaxRequest === true) {
				$this->renderPartial('_list', array(
					'contentsDataProvider'=>$contentCriteriaBuilder->getActiveDataProvider([
						'pagination' => [
							'pageSize' => $this->module->pageSize,
							'currentPage' => $page,
						]
					]),
				));
			} else {
				$this->render('list', array(
					'breadcrumb' => $this->buildBreadcrumb($this->currentNode->nodeId),
					'mainMenu' => $this->buildMainMenu(0, false),
					'node' => $this->currentNode,
					'contentsDataProvider'=>$contentCriteriaBuilder->getActiveDataProvider([
						'pagination' => [
							'pageSize' => $this->module->pageSize,
							'currentPage' => $page,
						]
					]),
				));
			}
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}

	/**
	 * manage metadata affected to current node
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionMeta() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			$notice = false;
			if(isset($_POST[Html::modelName('sweelix\yii1\admin\core\models\Meta')]) === true) {
				foreach($_POST[Html::modelName('sweelix\yii1\admin\core\models\Meta')] as $swfMeta) {
					$formMeta = new FormMeta('node');
					$formMeta->attributes = $swfMeta;
					$formMeta->nodeId = $this->currentNode->nodeId;
					if($formMeta->validate() === true) {
						if(\CPropertyValue::ensureBoolean($formMeta->metaModeOverride) === true) {
							$nodeMeta = NodeMeta::model()->findByPk(array(
								'nodeId'=>$formMeta->nodeId,
								'metaId'=>$formMeta->metaId
							));
							if($nodeMeta === null) {
								$nodeMeta = new NodeMeta();
								$nodeMeta->nodeId = $formMeta->nodeId;
								$nodeMeta->metaId = $formMeta->metaId;
							}
							$nodeMeta->nodeMetaValue = $formMeta->metaValue;
							$nodeMeta->save();
							unset($nodeMeta);
						} else {
							NodeMeta::model()->deleteByPk(array(
								'nodeId'=>$this->currentNode->nodeId,
								'metaId'=>$formMeta->metaId
							));
						}
					}
					$notice = true;
				}
			}
			// prepare data
			$criteriaBuilder = new CriteriaBuilder('meta');
			$criteriaBuilder->orderBy('metaId');
			$realMetas = $criteriaBuilder->findAll();
			$metas = array();
			foreach($realMetas as $realMeta) {
				$nodeMeta = NodeMeta::model()->findByAttributes(
					array(
						'nodeId'=>$this->currentNode->nodeId,
						'metaId'=>$realMeta->metaId
					)
				);
				$formMeta = new FormMeta('node');
				$formMeta->attributes = $realMeta->attributes;
				$formMeta->nodeId = $this->currentNode->nodeId;
				if($nodeMeta !== null) {
					$formMeta->metaValue = $nodeMeta->nodeMetaValue;
					$formMeta->metaModeOverride = true;
				} else {
					$formMeta->metaModeOverride = false;
				}
				$metas[] = $formMeta;
			}

			if(\Yii::app()->request->isAjaxRequest === true) {
				$this->renderPartial('_meta', array(
					'node' => $this->currentNode,
					'metas'=>$metas,
					'notice' => $notice,
				));
			} else {
				$this->render('meta', array(
					'breadcrumb' => $this->buildBreadcrumb($this->currentNode->nodeId),
					'mainMenu' => $this->buildMainMenu(1, 3),
					'node' => $this->currentNode,
					'metas'=>$metas,
					'notice' => $notice,
				));
			}
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}

	/**
	 * verify tag status for current node
	 *
	 * @param integer $tagId id of tag we want to check
	 *
	 * @return boolean
	 * @since  1.2.0
	 */
	public function isTagChecked($tagId) {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			$result = false;
			$nodeTag = NodeTag::model()->countByAttributes(array(
				'nodeId'=>$this->currentNode->nodeId,
				'tagId'=>$tagId
			));
			if($nodeTag > 0) {
				$result = true;
			}
			return $result;
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			return false;
		}
	}

	/**
	 * remove content from selected node
	 *
	 * @param integer $page current page to display
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionDetachContent($page=0) {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			if($this->currentContent !== null) {
				$this->currentContent->nodeId = null;
				$this->currentContent->contentOrder = 0;
				$this->currentContent->save(array('nodeId'));
				$this->currentNode->reOrder();
			}
			$this->_renderListContent($page);
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}
	/**
	 * delete selected node
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionDelete() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			$redirectUrl = array('admin', 'nodeId' => $this->currentNode->nodeId);
			$node = new FormNode();
			if(isset($_POST[Html::modelName($node)]) === true) {
				$node->scenario = 'deleteNode';
				$node->attributes = $_POST[Html::modelName($node)];
				if($node->validate() === true) {
					$redirectUrl = array('index', 'nodeId' => $this->currentNode->parent->nodeId);
					$node = Node::model()->findbyPk($node->targetNodeId);
					$node->delete();
				}
			}
			$this->redirect($redirectUrl);
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}

	/**
	 * move node from one place to another
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionMove($nodeId) {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			if(\Yii::app()->request->isAjaxRequest === true) {
				$sourceNode = Node::model()->findByPk(\Yii::app()->getRequest()->getParam('sourceId'));
				$targetNode = Node::model()->findByPk(\Yii::app()->getRequest()->getParam('targetId'));
				if(($sourceNode !== null) && ($targetNode !== null)) {
					$sourceNode->move(\Yii::app()->getRequest()->getParam('target'), $targetNode->nodeId);
					$node = Node::model()->findByPk($nodeId);
					$this->widget('structure.widgets.TreeNodesWidget', array('node' => $node));
				} else {
					throw new \CHttpException(400);
				}
			}
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}

	/**
	 * Define filtering rules
	 *
	 * @return array
	 * @since  1.2.0
	 */
	public function filters() {
		return array(
			'accessControl',
			array(
				'sweelix\yii1\admin\core\filters\ContextContent + detachContent'
			),
			array(
				'sweelix\yii1\admin\core\filters\ContextNode - move, asyncUpload, asyncDelete, search, asyncPreview'
			)
		);
	}

	/**
	 * Define access rules / rbac stuff
	 *
	 * @return array
	 * @since  1.2.0
	 */
	public function accessRules() {
		return array(
			array(
				'allow', 'roles' => array($this->getModule()->getName())
			),
			array(
				'deny', 'users'=>array('*'),
			),
		);
	}
}
