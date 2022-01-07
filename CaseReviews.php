<?php

if (!defined("MEDIAWIKI")) die();

// White list the special page, so it is public.
$wgWhitelistRead[] = "Special:CaseReviews";


# Autoload classes and files
$wgAutoloadClasses['SpecialCaseReviews'] = __DIR__ . '/SpecialCaseReviews.php';
$wgAutoloadClasses['CaseReviewsHooks'] = __DIR__ . '/CaseReviewsHooks.php';



# Tell MediaWiki about the new special page and its class name
$wgSpecialPages['CaseReviews'] = 'SpecialCaseReviews';
