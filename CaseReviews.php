<?php

if (!defined("MEDIAWIKI")) die();

// White list the special page, so it is public.
$wgWhitelistRead[] = "Special:CaseReviews";


# Autoload classes and files
$wgAutoloadClasses["SpecialCaseReviews"] = __DIR__ . "/SpecialCaseReviews.php";


# Tell MediaWiki about the new special page and its class name
$wgSpecialPages["CaseReviews"] = "SpecialCaseReviews";


$wgResourceModules["ext.caseReviews"] = array(
    "scripts" => array(),
    "styles" => array(
        "css/case-reviews.css"
    ),
    "position" => "bottom",
    "remoteBasePath" => "/extensions/CaseReviews",
    "localBasePath" => "extensions/CaseReviews"
);