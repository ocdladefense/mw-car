<?php

use \MediaWiki\MediaWikiServices;
use \Salesforce\OAuthConfig;
use \Salesforce\OAuth;
use \Salesforce\OAuthRequest;
use \Salesforce\RestApiRequest;


class SpecialCaseReviews extends SpecialPage {

    private $oauthFlow = "usernamepassword";

    public function __construct() {

        parent::__construct("CaseReviews");
    }


    public function execute($parameter) {}
}