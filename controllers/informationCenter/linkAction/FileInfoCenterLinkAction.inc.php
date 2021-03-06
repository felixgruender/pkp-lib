<?php
/**
 * @file controllers/informationCenter/linkAction/FileInfoCenterLinkAction.inc.php
 *
 * Copyright (c) 2014-2016 Simon Fraser University Library
 * Copyright (c) 2003-2016 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class FileInfoCenterLinkAction
 * @ingroup controllers_informationCenter
 *
 * @brief A base action to open up the information center for a file.
 */

import('lib.pkp.controllers.api.file.linkAction.FileLinkAction');

class FileInfoCenterLinkAction extends FileLinkAction {

	/**
	 * Constructor
	 * @param $request Request
	 * @param $submissionFile SubmissionFile the submission file
	 * to show information about.
	 * @param $stageId int (optional) The stage id that user is looking at.
	 */
	function FileInfoCenterLinkAction($request, $submissionFile, $stageId = null) {
		// Instantiate the information center modal.
		$ajaxModal = $this->getModal($request, $submissionFile, $stageId);

		// Configure the file link action.
		parent::FileLinkAction(
			'moreInformation', $ajaxModal,
			__('grid.action.moreInformation'), 'more_info'
		);
	}

	/**
	 * returns the modal for this link action.
	 * @param $request PKPRequest
	 * @param $submissionFile SubmissionFile
	 * @param $stageId int
	 * @return AjaxModal
	 */
	function getModal($request, $submissionFile, $stageId) {
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		$router = $request->getRouter();

		$title = (isset($submissionFile)) ? implode(': ', array(__('informationCenter.informationCenter'), $submissionFile->getLocalizedName())) : __('informationCenter.informationCenter');

		$ajaxModal = new AjaxModal(
			$router->url(
				$request, null,
				'informationCenter.FileInformationCenterHandler', 'viewInformationCenter',
				null, $this->getActionArgs($submissionFile, $stageId)
			),
			$title,
			'modal_information'
		);

		return $ajaxModal;
	}
}

?>
