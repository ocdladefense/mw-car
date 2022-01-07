<?php

if (!defined("MEDIAWIKI")) die();


final class CaseReviewHooks {
	
	public static function onBeforePageDisplay(OutputPage &$out, Skin &$skin ) {
		
		return true;
	}


	public static function setup() {}
}