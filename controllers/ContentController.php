<?php
/**
 * File NodeController.php
 *
 * PHP version 5.4+
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   2.0.1
 * @link      http://www.sweelix.net
 * @category  controllers
 * @package   sweelix.yii1.admin.structure.controllers
 */

namespace sweelix\yii1\admin\structure\controllers;
use sweelix\yii1\admin\core\web\Controller;
use sweelix\yii1\ext\db\CriteriaBuilder;
use sweelix\yii1\ext\entities\Node;
use sweelix\yii1\ext\entities\Content;
use sweelix\yii1\ext\entities\Meta;
use sweelix\yii1\ext\entities\ContentTag;
use sweelix\yii1\ext\entities\ContentMeta;
use sweelix\yii1\admin\core\models\Content as FormContent;
use sweelix\yii1\admin\core\models\Meta as FormMeta;
use sweelix\yii1\web\helpers\Html;

/**
 * Class NodeController
 *
 * @author    Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2014 Sweelix
 * @license   http://www.sweelix.net/license license
 * @version   2.0.1
 * @link      http://www.sweelix.net
 * @category  controllers
 * @package   sweelix.yii1.admin.structure.controllers
 * @since     1.0.0
 *
 * @property mixed $templateConfig
 */
class ContentController extends Controller {

	/**
	 * @var array breadcrumbs
	 */
	private $_breadCrumb=array();

	/**
	 * Lazy load and build breadcrumb for selected id.
	 *
	 * @param integer $currentNodeId target node of the breadcrumb
	 *
	 * @return array
	 * @since  XXX
	*/
	public function buildBreadcrumb($currentContentId) {
		if(isset($this->_breadCrumb[$currentContentId]) === false) {
			$currentContent = Content::model()->findByPk($currentContentId);
			if($currentContent !== null) {
				if($currentContent->node !== null) {
					$criteriaBuilder = new CriteriaBuilder('node');
					$criteriaBuilder->filterBy('nodeLeftId', $currentContent->node->nodeLeftId, '<=');
					$criteriaBuilder->filterBy('nodeRightId', $currentContent->node->nodeRightId, '>=');
					$criteriaBuilder->orderBy('nodeLeftId', 'asc');
					$path = $criteriaBuilder->findAll();
					foreach($path as $element) {
						$this->_breadCrumb[$currentContentId][] = array(
								'content' => $element->nodeTitle,
								'url' => array('node/', 'nodeId' => $element->nodeId),
						);
					}
				} else {
					$this->_breadCrumb[$currentContentId][] = array(
							'content' => \Yii::t('structure', 'Orphan'),
					);
				}
			}
		}
		return $this->_breadCrumb[$currentContentId];
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
	 * @since  XXX
	 */
	public function buildMainMenu($mainOption=null, $secondaryOption=null) {
		$mainMenu = array(
				'main' => array(
						array(
								'content' => \Yii::t('structure', 'Node Content'),
								'url' => array('node/', 'nodeId'=>($this->currentNode !== null)?$this->currentNode->nodeId:''),
								'active' => false,
						),
						array(
								'content' => \Yii::t('structure', 'Node Configuration'),
								'url' => array('node/detail', 'nodeId'=>($this->currentNode !== null)?$this->currentNode->nodeId:''),
								'active' => false,
						),
						array(
								'content' => \Yii::t('structure', 'Content Configuration'),
								'url' => array('node/detail', 'nodeId'=>($this->currentNode !== null)?$this->currentNode->nodeId:''),
								'active' => false,
						)
				),
				'secondary' => array(
						array(
								'content' => \Yii::t('structure', 'Content detail'),
								'url' => array('content/detail', 'contentId'=>$this->currentContent->contentId),
								'active' => false,
						),
						array(
								'content' => \Yii::t('structure', 'Properties'),
								'url' => array('content/property', 'contentId'=>$this->currentContent->contentId),
								'active' => false,
						),
						array(
								'content' => \Yii::t('structure', 'Associated tags'),
								'url' => array('content/tag', 'contentId'=>$this->currentContent->contentId),
								'active' => false,
						),
						array(
								'content' => \Yii::t('structure', 'Metadata'),
								'url' => array('content/meta', 'contentId'=>$this->currentContent->contentId),
								'active' => false,
						),
						array(
								'content' => \Yii::t('structure', 'Admin'),
								'url' => array('content/admin', 'contentId'=>$this->currentContent->contentId),
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
	 * @see Sweeml::activeAsyncFileUpload
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
			'content/detail',
			'contentId'=>\Yii::app()->request->getParam('contentId', 0)
		));
	}

	/**
	 * Admin will be a placeholder for specific actions on content
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionAdmin() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			$this->setCurrentNode($this->currentContent->node);
			$content = new FormContent();
			$content->targetContentId = $this->currentContent->contentId;
			$content->selected = false;

			$contentCriteriaBuilder = new CriteriaBuilder('content');
			$contentCriteriaBuilder->filterBy('contentId', $this->currentContent->contentId);

			$this->render('admin', array(
				'breadcrumb' => $this->buildBreadcrumb($this->currentContent->contentId),
				'mainMenu' => $this->buildMainMenu(2, 4),
				'content'=>$content,
				'sourceContent'=>$this->currentContent,
				'node' => $this->currentNode,
				'contentsDataProvider' => $contentCriteriaBuilder->getActiveDataProvider(array('pagination' => false))
			));
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}

	/**
	 * Start content creation
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionNew() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			\Yii::app()->session['newcontent'] = array();
			$this->redirect(array('step1', 'nodeId'=>$this->currentNode->nodeId));
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}

	/**
	 * Collect base information to create a content
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionStep1() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			$newContent = new Content();
			$newContent->setScenario('createStep1');
			$newContent->nodeId = $this->currentNode->nodeId;
			$newContent->attributes = \Yii::app()->session['newcontent'];
			if(isset($_POST[Html::modelName($newContent)]) === true) {
				$newContent->attributes = $_POST[Html::modelName($newContent)];
				if($newContent->validate() === true) {
					// clean request to avoid warnings
					$swContent = $_POST[Html::modelName($newContent)];
					unset($swContent['contentStartDateActive']);
					unset($swContent['contentEndDateActive']);
					\Yii::app()->session['newcontent'] = $swContent;
					$this->redirect(array('step2', 'nodeId'=>$this->currentNode->nodeId));
				}
			}
			$criteriaBuilder = new CriteriaBuilder('node');
			$criteriaBuilder->orderBy('nodeLeftId', 'asc');
			$treeDataProvider = $criteriaBuilder->getActiveDataProvider(array('pagination' => false));

			$criteriaBuilder = new CriteriaBuilder('template');
			$criteriaBuilder->filterBy('templateType', 'single');
			$criteriaBuilder->orderBy('templateTitle');
			$templatesDataProvider = $criteriaBuilder->getActiveDataProvider(array('pagination' => false));

			if(\Yii::app()->getRequest()->isAjaxRequest === false) {
				$this->render('step1', array(
					'create' => true,
					//'breadcrumb' => $this->buildBreadcrumb($this->currentNode->nodeId),
					'sourceNode' => $this->currentNode,
					'content'=>$newContent,
					'templatesDataProvider'=>$templatesDataProvider,
					'treeDataProvider' => $treeDataProvider,
				));
			} else {
				$this->renderPartial('_property', array(
					'create' => true,
					'content'=>$newContent,
					'templatesDataProvider'=>$templatesDataProvider,
					'treeDataProvider' => $treeDataProvider,
				));
			}
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}

	/**
	 * Collect base information to create a content
	 *
	 * @return void
	 */
	public function actionStep2() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			$newContent = new Content();
			$newContent->setScenario('createStep2');
			$newContent->templateId = \Yii::app()->session['newcontent']['templateId'];
			$newContent->reconfigure();
			$newContent->attributes = \Yii::app()->session['newcontent'];
			if($newContent->templateId === null) {
				throw new \CHttpException('500', \Yii::t('structure', 'Template is not defined for current conntent'));
			}
			if(isset($_POST[Html::modelName($newContent)]) === true) {
				$newContent->authorId = \Yii::app()->user->id;
				$newContent->nodeId = $this->currentNode->nodeId;
				$newContent->attributes = $_POST[Html::modelName($newContent)];
				$contentStatus = $newContent->validate();

				if($contentStatus === true) {
					if($newContent->save() === true) {
						$newContent->node->reOrder();
						$this->redirect(array('index', 'contentId'=>$newContent->contentId));
					}
				}
			}
			if(\Yii::app()->getRequest()->isAjaxRequest === false) {
				$this->render('step2', array(
					'create' => true,
					'sourceNode' => $this->currentNode,
					'content'=>$newContent,
				));
			} else {
				$this->renderPartial('_detail', array(
					'create' => true,
					'content'=>$newContent,
				));
			}
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}

	/**
	 * Change data properties of content.
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionDetail() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			if($this->currentContent->templateId === null) {
				throw new \CHttpException('500', \Yii::t('structure', 'Template is not defined for current content'));
			}
			$notice = false;

			if(isset($_POST[Html::modelName($this->currentContent)]) === true) {
				$this->currentContent->setScenario('updateDetail');
				$this->currentContent->attributes = $_POST[Html::modelName($this->currentContent)];

				$contentStatus = $this->currentContent->validate();
				if($contentStatus === true) {
					if($this->currentContent->save() === true) {
						$this->currentContent->refresh();
					}
				}
				$notice = true;

			}

			if(\Yii::app()->getRequest()->isAjaxRequest === false) {
				$this->setCurrentNode($this->currentContent->node);
				$this->render('detail', array(
					'create' => false,
					'breadcrumb' => $this->buildBreadcrumb($this->currentContent->contentId),
					'mainMenu' => $this->buildMainMenu(2, 0),
					'node' => $this->currentNode,
					'content' => $this->currentContent,
					'notice' => $notice,
				));
			} else {
				$this->renderPartial('_detail', array(
					'create' => false,
					'content' => $this->currentContent,
					'notice' => $notice,
				));
			}
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}

	/**
	 * Change presentation properties of content
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionProperty() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			$notice = false;
			if(isset($_POST[Html::modelName($this->currentContent)]) === true) {
				$originalNodeId = $this->currentContent->nodeId;
				$this->currentContent->setScenario('updateProperty');
				$this->currentContent->attributes = $_POST[Html::modelName($this->currentContent)];
				if(\CPropertyValue::ensureBoolean($_POST[Html::modelName($this->currentContent)]['contentStartDateActive']) === false) {
					$this->currentContent->contentStartDate = null;
				}
				if(\CPropertyValue::ensureBoolean($_POST[Html::modelName($this->currentContent)]['contentEndDateActive']) === false) {
					$this->currentContent->contentEndDate = null;
				}

				$this->currentContent->authorId = \Yii::app()->user->id;
				if($this->currentContent->validate() === true) {
					if($this->currentContent->save() === true) {
						if($this->currentContent->nodeId !== $originalNodeId) {
							$this->currentContent->move('top');
							$originalNode = Node::model()->findByPk($originalNodeId);
							if($originalNode !== null) {
								$originalNode->reOrder();
								$this->redirect(array('property', 'contentId'=>$this->currentContent->contentId));
							}
						}
					}
				}
				$notice = true;
			}

			$criteriaBuilder = new CriteriaBuilder('node');
			$criteriaBuilder->orderBy('nodeLeftId', 'asc');
			$treeDataProvider = $criteriaBuilder->getActiveDataProvider(array('pagination' => false));

			$criteriaBuilder = new CriteriaBuilder('template');
			$criteriaBuilder->filterBy('templateType', 'single');
			$criteriaBuilder->orderBy('templateTitle');
			$templatesDataProvider = $criteriaBuilder->getActiveDataProvider(array('pagination' => false));

			$contentCriteriaBuilder = new CriteriaBuilder('content');
			$contentCriteriaBuilder->filterBy('contentId', $this->currentContent->contentId);

			if(\Yii::app()->request->isAjaxRequest === true) {
				$this->renderPartial('_property', array(
					'create' => false,
					'content'=>$this->currentContent,
					'treeDataProvider'=>$treeDataProvider,
					'templatesDataProvider'=>$templatesDataProvider,
					'contentsDataProvider' => $contentCriteriaBuilder->getActiveDataProvider(array('pagination' => false)),
					'notice' => $notice,
				));
			} else {
				$this->setCurrentNode($this->currentContent->node);
				$this->render('property', array(
					'create' => false,
					'breadcrumb' => $this->buildBreadcrumb($this->currentContent->contentId),
					'mainMenu' => $this->buildMainMenu(2, 1),
					'content' => $this->currentContent,
					'treeDataProvider'=>$treeDataProvider,
					'templatesDataProvider'=>$templatesDataProvider,
					'contentsDataProvider' => $contentCriteriaBuilder->getActiveDataProvider(array('pagination' => false)),
					'notice' => $notice,
				));
			}
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}

	/**
	 * manage tags affected to current content
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
			// $criteriaBuilder->orderBy('tagTitle');
			$groups = $criteriaBuilder->findAll();

			if(isset($_POST[Html::modelName('sweelix\yii1\ext\entities\Group')]) === true) {
				ContentTag::model()->deleteAllByAttributes(array('contentId'=>$this->currentContent->contentId));
				foreach($_POST[Html::modelName('sweelix\yii1\ext\entities\Group')] as $i => $selectedTagIds) {
				if(isset($selectedTagIds['groupSelectedTags']) === true) {
						if(is_array($selectedTagIds['groupSelectedTags']) === true) {
							$groups[$i]->groupSelectedTags = $selectedTagIds['groupSelectedTags'];
						} elseif(is_string($selectedTagIds['groupSelectedTags']) === true) {
							$groups[$i]->groupSelectedTags = array($selectedTagIds['groupSelectedTags']);
						}
					}
					foreach($groups[$i]->groupSelectedTags as $tagId) {
						$contentTag = new ContentTag();
						$contentTag->contentId = $this->currentContent->contentId;
						$contentTag->tagId = $tagId;
						$contentTag->save();
						unset($contentTag);
					}
				}
				$notice = true;
			} else {
				for($i=0; $i<count($groups); $i++) {
					$selectedTags = ContentTag::model()->findAllByAttributes(array('contentId'=>$this->currentContent->contentId));
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
		$this->setCurrentNode($this->currentContent->node);
		$contentCriteriaBuilder = new CriteriaBuilder('content');
		$contentCriteriaBuilder->filterBy('contentId', $this->currentContent->contentId);

		if(\Yii::app()->request->isAjaxRequest === true) {
			$this->renderPartial('_tag', array(
				'groups'=>$groups,
				'content'=>$this->currentContent,
				'contentsDataProvider' => $contentCriteriaBuilder->getActiveDataProvider(array('pagination' => false)),
				'notice' => $notice,
			));
		} else {
			$this->render('tag', array(
				'breadcrumb' => $this->buildBreadcrumb($this->currentContent->contentId),
				'mainMenu' => $this->buildMainMenu(2, 2),
				'groups'=>$groups,
				'content' => $this->currentContent,
				'node' => $this->currentNode,
				'contentsDataProvider' => $contentCriteriaBuilder->getActiveDataProvider(array('pagination' => false)),
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
	 * @return void
	 * @since  XXX
	 */
	public function actionChangeStatus() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			$content = Content::model()->findByPk(\Yii::app()->getRequest()->getParam('contentId', 0));
			$mode = \Yii::app()->getRequest()->getParam('mode', 'draft');
			if($content !== null) {
				$content->contentStatus = $mode;
				$content->save();
			}
			$this->renderPartial('_contentHeader', array('content' => $content));
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}

	/**
	 * manage metadata affected to current content
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
					$formMeta = new FormMeta('content');
					$formMeta->attributes = $swfMeta;
					$formMeta->contentId = $this->currentContent->contentId;
					if($formMeta->validate() === true) {
						if(\CPropertyValue::ensureBoolean($formMeta->metaModeOverride) === true) {
							$contentMeta = ContentMeta::model()->findByPk(array('contentId'=>$formMeta->contentId, 'metaId'=>$formMeta->metaId));
							if($contentMeta === null) {
								$contentMeta = new ContentMeta();
								$contentMeta->contentId = $formMeta->contentId;
								$contentMeta->metaId = $formMeta->metaId;
							}
							$contentMeta->contentMetaValue = $formMeta->metaValue;
							$contentMeta->save();
							unset($contentMeta);
						} else {
							ContentMeta::model()->deleteByPk(array(
								'contentId'=>$this->currentContent->contentId,
								'metaId'=>$formMeta->metaId
							));
						}
					}
				}
				$notice = true;
			}
			$realMetas = Meta::model()->findAll(array('order'=>'metaId ASC'));
			$metas = array();
			foreach($realMetas as $realMeta) {
				$contentMeta = ContentMeta::model()->findByAttributes(array(
					'contentId'=>$this->currentContent->contentId,
					'metaId'=>$realMeta->metaId
				));
				$formMeta = new FormMeta('content');
				$formMeta->attributes = $realMeta->attributes;
				$formMeta->contentId = $this->currentContent->contentId;
				if($contentMeta !== null) {
					$formMeta->metaValue = $contentMeta->contentMetaValue;
					$formMeta->metaModeOverride = true;
				} else {
					$formMeta->metaModeOverride = false;
				}
				$metas[] = $formMeta;
			}
			$contentCriteriaBuilder = new CriteriaBuilder('content');
			$contentCriteriaBuilder->filterBy('contentId', $this->currentContent->contentId);

			$this->setCurrentNode($this->currentContent->node);
			if(\Yii::app()->request->isAjaxRequest === true) {
				$this->renderPartial('_meta', array(
						'content' => $this->currentContent,
						'metas'=>$metas,
						'contentsDataProvider' => $contentCriteriaBuilder->getActiveDataProvider(array('pagination' => false)),
						'notice' => $notice,
				));
			} else {
				$this->render('meta', array(
					'breadcrumb' => $this->buildBreadcrumb($this->currentContent->contentId),
					'mainMenu' => $this->buildMainMenu(2, 3),
					'metas'=>$metas,
					'content' => $this->currentContent,
					'node' => $this->currentNode,
					'contentsDataProvider' => $contentCriteriaBuilder->getActiveDataProvider(array('pagination' => false)),
					'notice' => $notice,
				));
			}
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			throw $e;
		}
	}

	/**
	 * verify tag status for current content
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
			$contentTag = ContentTag::model()->countByAttributes(array(
				'contentId'=>$this->currentContent->contentId,
				'tagId'=>$tagId
			));
			if($contentTag > 0) {
				$result = true;
			}
			return $result;
		} catch(\Exception $e) {
			\Yii::log('Error in '.__METHOD__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix.yii1.admin.structure.controllers');
			return false;
		}
	}


	/**
	 * delete selected content
	 *
	 * @return void
	 * @since  1.2.0
	 */
	public function actionDelete() {
		try {
			\Yii::trace(__METHOD__.'()', 'sweelix.yii1.admin.structure.controllers');
			$redirectUrl = array('admin', 'contentId' => $this->currentContent->contentId);
			$content = new FormContent();
			if(isset($_POST[Html::modelName($content)]) === true) {
				$content->scenario = 'deleteContent';
				$content->attributes = $_POST[Html::modelName($content)];
				//TODO: fix node reoder
				if($content->validate() === true) {
					$originalNode = $this->currentContent->node;
					$content = Content::model()->findbyPk($content->targetContentId);
					$content->delete();

					if($originalNode instanceof Node) {
						$originalNode->reOrder();
						$redirectUrl = array('node/index', 'nodeId' => $originalNode->nodeId);
					} else {
						$redirectUrl = array('node/index');
					}
				}
			}
			$this->redirect($redirectUrl);
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
				'sweelix\yii1\admin\core\filters\ContextContent - new, step1, step2, asyncUpload, asyncDelete, asyncPreview'
			),
			array(
				'sweelix\yii1\admin\core\filters\ContextNode + new, step1, step2'
			),
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
