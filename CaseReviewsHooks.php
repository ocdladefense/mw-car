<?php

if (!defined("MEDIAWIKI")) die();


class CaseReviewHooks {
	
	public static function onBeforePageDisplay(OutputPage &$out, Skin &$skin ) {

		//$out->addModules("ext.caseReviews");

		return true;
	}
}