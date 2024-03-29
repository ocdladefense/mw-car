

<?php

use function Mysql\select;
use Mysql\Database;
use Mysql\DbHelper;
use Ocdla\Template;


class SpecialCaseReviews extends SpecialPage {
	
    public function __construct() {

		global $wgCaseReviewsDBtype;
		global $wgCaseReviewsDBserver;
		global $wgCaseReviewsDBname;
		global $wgCaseReviewsDBuser;
		global $wgCaseReviewsDBpassword;

        parent::__construct("CaseReviews");

		$this->getOutput()->addModules("ext.caseReviews");

		$this->mIncludable = true;

		$dbCredentials = array(
			"host"       =>  $wgCaseReviewsDBserver,
			"user"  	 =>  $wgCaseReviewsDBuser,
			"password"   =>  $wgCaseReviewsDBpassword,
			"name"       =>  $wgCaseReviewsDBname,
		);


		Database::setDefault($dbCredentials);		
    }




    public function execute($params) {

		global $wgOcdlaCaseReviewsDefaultRecordLimit;

		$params = empty($params) ? "50" : $params;

		list($numRows, $field, $value) = explode("/", $params);
		$field = "subject_1" == $field ? "subject" : $field;
		
		$useAlternateTemplate = (!empty($value) && !empty($field));

		$output = $this->getOutput();

		$template = __DIR__ . "/templates/summary.tpl.php";

		if(!$this->including()) {

			$numRows = !empty($wgOcdlaCaseReviewsDefaultRecordLimit) ? $wgOcdlaCaseReviewsDefaultRecordLimit : 500;
		}

		// Define a subject query, too.
		$query = "SELECT court, year, month, day, published_date, subject, secondary_subject FROM car WHERE COALESCE(is_draft, 0) != 1 ORDER BY year DESC, month DESC, day DESC LIMIT {$numRows}";


		if($useAlternateTemplate) {

			$query = "SELECT id, year, month, day, summary, subject, title FROM car WHERE COALESCE(is_draft,0) != 1 AND {$field} = '{$value}' ORDER BY year DESC, month DESC, day DESC LIMIT {$numRows}";
			// print $query; exit;
			$template = __DIR__ . "/templates/alternate-summary.tpl.php";
		}


		$cars = select($query);

		$days = $this->group($cars);

		$html = $useAlternateTemplate ? $this->getAlternateHTML($cars, $template) : $this->getHTML($days, $template);

		$output->addHTML($html);
    }



	
	public function group($cars){
		
		$days = array();

		// Assumes results are already sorted DESC by year, month, and day, so array will start with most recent cars.
		foreach($cars as $car){

			// Normally we can accept 2021-11-04 but could we try 2021-11-4 (our database doesn't store leading zeros)?
			$key = implode("-", array($car["year"], $car["month"], $car["day"], $car["court"]));

			$days[$key][] = $car;
		}

		return $days;
	}


	public function getHTML($days, $summaryTemplate) {

		$subjectTemplate = __DIR__ . "/templates/subjects.tpl.php";

		// If the page is being rendered as a standalone page, add the additional html.
		$html = !$this->including() ? $this->getSummaryLinksHTML() : "";
		
		// Opening container tags
		$html .= "<div class='car-wrapper'>";
		$html .= "<div class='car-roll'>";


		foreach($days as $key => $cars){

			$params["cars"] = $cars;

			$params = $this->preprocess($key, $cars);

			$params["subjectsHTML"] = Template::renderTemplate($subjectTemplate, $params);

			$html .= Template::renderTemplate($summaryTemplate, $params);
		}

		// Closing container tags
		$html .= "</div></div>";

		return str_replace(array("\r", "\n"), '', $html);
	}


	public function getAlternateHTML($cars, $template) {

		global $wgOcdlaAppDomain;

		$subject = $cars[0]["subject"];

		$html = "<h5>Showing " . ucwords($cars[0]["subject"]) . " Case Reviews</h5>";
		$html .= "<a href='$wgOcdlaAppDomain/car/list?subject=$subject'>Show all $subject case reviews</a>";
		
		// Opening container tags
		$html .= "<div class='car-wrapper'>";
		$html .= "<div class='car-roll'>";


		foreach($cars as $car){

			$year = $car["year"];
			$month = $car["month"];
			$day = $car["day"];

			$titleDate = new DateTime("$year-$month-$day");
			$titleDate = $titleDate->format("F jS, Y");

			$car["titleDate"] = $titleDate;
			$car["appDomain"] = $wgOcdlaAppDomain;

			$html .= Template::renderTemplate($template, $car);
		}

		// Closing container tags
		$html .= "</div></div>";

		return str_replace(array("\r", "\n"), '', $html);
	}



	public function preprocess($key, $cars){

		global $wgOcdlaAppDomain, $wgOcdlaCaseReviewAuthor;

		list($year, $month, $day, $court) = explode("-", $key);
		

		// Doing this for the title only.  Don't want to change the value of "$court".
		$titleCourt = !empty($court) ? $court : "Case Reviews";
		$titleDate = new DateTime("$year-$month-$day");
		$titleDate = $titleDate->format("F jS, Y");

		$title = "$titleCourt, $titleDate";

		// Build the published date, but only if the create time is a valid timestamp.
		if($this->timestampIsValid($cars[0]["published_date"])){

			$publishDate = $cars[0]["published_date"];
			$publishDate = new DateTime($publishDate);
			$publishDate = $publishDate->format("F jS, Y");
		}




		$data = array(
			"title"		   => $title,
			"titleDate"    => $titleDate,
			"publishDate"  => $publishDate,
			"author"	   => $wgOcdlaCaseReviewAuthor,
			"year"		   => $year,
			"month"		   => $month,
			"day"		   => $day,
			"court"		   => $court,
			"appDomain"	   => $wgOcdlaAppDomain,
			"cars"		   => $cars
		);

		return $data;
	}


	public function getSummaryLinksHTML(){

		global $wgServer, $wgOcdlaAppDomain;

		$template = __DIR__ . "/templates/summary-links.tpl.php";

		$years = DbHelper::getDistinctFieldValues("car", "year");


		// These are the links to case reviews that are not available in the app.
		$legacyLinks = array(
			"2015"	=>  "$wgServer/2015_Case_Summaries_by_Topic",
			"2016"	=>  "$wgServer/2016_Case_Summaries_by_Topic",
			"2017"	=>  "$wgServer/2017_Case_Summaries_by_Topic"

		);


		// These are the links to summaries in the app.
		$appLinks = array();

		foreach($years as $year) {
			
			$appLinks[$year] = "$wgOcdlaAppDomain/car/list?year=$year&summarize=1";
		}

		$allSummaryLinks = $legacyLinks + $appLinks;

		return Template::renderTemplate($template, array("allSummaryLinks" => array_reverse($allSummaryLinks, true)));
	}


	public function timestampIsValid($timestamp){

		$year = (int) explode("-", $timestamp)[0];

		return $year > 2000;
	}
}
