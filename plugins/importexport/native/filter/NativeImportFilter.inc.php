<?php

/**
 * @file plugins/importexport/native/filter/NativeImportFilter.inc.php
 *
 * Copyright (c) 2000-2013 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class NativeImportFilter
 * @ingroup plugins_importexport_native
 *
 * @brief Base class that converts a Native XML document to a DataObject
 */

import('lib.pkp.plugins.importexport.native.filter.NativeImportExportFilter');

class NativeImportFilter extends NativeImportExportFilter {
	/**
	 * Constructor
	 * @param $filterGroup FilterGroup
	 */
	function NativeImportFilter($filterGroup) {
		parent::NativeImportExportFilter($filterGroup);
	}


	//
	// Implement template methods from Filter
	//
	/**
	 * @see Filter::process()
	 * @param $document DOMDocument|string
	 * @return array Array of imported documents
	 */
	function &process(&$document) {
		// If necessary, convert $document to a DOMDocument.
		if (is_string($document)) {
			$xmlString = $document;
			$document = new DOMDocument();
			$document->loadXml($xmlString);
		}
		assert(is_a($document, 'DOMDocument'));

		$deployment = $this->getDeployment();
		$submissions = array();
		if ($document->documentElement->tagName == $this->getPluralElementName()) {
			// Multiple element (plural) import
			for ($n = $document->documentElement->firstChild; $n !== null; $n=$n->nextSibling) {
				if (!is_a($n, 'DOMElement')) continue;
				$submissions[] = $this->handleElement($n);
			}
		} else {
			assert($document->documentElement->tagName == $this->getSingularElementName());

			// Single element (singular) import
			$submissions[] = $this->handleElement($document->documentElement);
		}

		return $submissions;
	}

	/**
	 * Return the plural element name
	 * @return string
	 */
	function getPluralElementName() {
		assert(false); // Must be overridden by subclasses
	}

	/**
	 * Get the singular element name
	 * @return string
	 */
	function getSingularElementName() {
		assert(false); // Must be overridden by subclasses
	}

	/**
	 * Handle a singular element import
	 * @param $node DOMElement
	 */
	function handleElement($node) {
		assert(false); // Must be overridden by subclasses
	}

	/**
	 * Parse a localized element
	 * @param $element DOMElement
	 * @return array Array("locale_KEY", "Localized Text")
	 */
	function parseLocalizedContent($element) {
		assert($element->hasAttribute('locale'));
		return array($element->getAttribute('locale'), $element->textContent);
	}
}

?>