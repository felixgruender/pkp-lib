<?php
/**
 * @file classes/security/authorization/internal/QueryRequiredPolicy.inc.php
 *
 * Copyright (c) 2014-2016 Simon Fraser University Library
 * Copyright (c) 2000-2016 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class QueryRequiredPolicy
 * @ingroup security_authorization_internal
 *
 * @brief Policy that ensures that the request contains a valid query.
 */

import('lib.pkp.classes.security.authorization.DataObjectRequiredPolicy');

class QueryRequiredPolicy extends DataObjectRequiredPolicy {
	/**
	 * Constructor
	 * @param $request PKPRequest
	 * @param $args array request parameters
	 * @param $submissionParameterName string the request parameter we expect
	 *  the submission id in.
	 */
	function QueryRequiredPolicy($request, &$args, $parameterName = 'queryId', $operations = null) {
		parent::DataObjectRequiredPolicy($request, $args, $parameterName, 'user.authorization.invalidQuery', $operations);
	}

	//
	// Implement template methods from AuthorizationPolicy
	//
	/**
	 * @see DataObjectRequiredPolicy::dataObjectEffect()
	 */
	function dataObjectEffect() {
		$queryId = (int)$this->getDataObjectId();
		if (!$queryId) return AUTHORIZATION_DENY;

		// Make sure the query belongs to the submission/representation.
		$queryDao = DAORegistry::getDAO('QueryDAO');
		$query = $queryDao->getById($queryId);
		if (!is_a($query, 'Query')) return AUTHORIZATION_DENY;
		switch ($query->getAssocType()) {
			case ASSOC_TYPE_SUBMISSION:
				$submission = $this->getAuthorizedContextObject(ASSOC_TYPE_SUBMISSION);
				if (!is_a($submission, 'Submission')) return AUTHORIZATION_DENY;
				if ($query->getAssocId() != $submission->getId()) return AUTHORIZATION_DENY;
				break;
			case ASSOC_TYPE_REPRESENTATION:
				$representation = $this->getAuthorizedContextObject(ASSOC_TYPE_REPRESENTATION);
				if (!is_a($representation, 'Representation')) return AUTHORIZATION_DENY;
				if ($query->getAssocId() != $representation->getId()) return AUTHORIZATION_DENY;
				break;
			default:
				return AUTHORIZATION_DENY;
		}

		// Save the query to the authorization context.
		$this->addAuthorizedContextObject(ASSOC_TYPE_QUERY, $query);
		return AUTHORIZATION_PERMIT;
	}
}

?>
