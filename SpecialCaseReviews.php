<?php

use function Mysql\select;
use Ocdla\View;


class SpecialCaseReviews extends SpecialPage {

	private $numRows;

	
    public function __construct() {

        parent::__construct("CaseReviews");

		$this->getOutput()->addModules("ext.caseReviews");

		$this->mIncludable = true;
    }


    public function execute($numRows) {

		global $wgOut;

		$query = "SELECT court, year, month, day, createtime, subject_1, subject_2 FROM car ORDER BY year DESC, month DESC, day DESC LIMIT {$numRows}";

		$cars = select($query);

		$days = $this->group($cars);

		$html = $this->getHTML($days);

		// The syntax for this will be "$this->getOutput()" in later MediaWiki versions.
		$wgOut->addHTML($html);
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


	public function getHTML($days) {

		$subjectTemplate = __DIR__ . "/templates/subjects.tpl.php";
		$summaryTemplate = __DIR__ . "/templates/summary.tpl.php";

		$html = "";
		
		// Opening container tags
		$html .= "<div class='car-wrapper'>";
		$html .= "<div class='car-roll'>";


		foreach($days as $key => $cars){

			$params = $this->preprocess($key, $cars);

			$params["subjectsHTML"] = View::renderTemplate($subjectTemplate, $params);

			$html .= View::renderTemplate($summaryTemplate, $params);
		}

		// Closing container tags
		$html .= "</div></div>";

		return $html;
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
		if($this->timestampIsValid($cars[0]["createtime"])){

			$publishDate = $cars[0]["createtime"];
			$publishDate = new DateTime($publishDate);
			$publishDate = $publishDate->format("F jS, Y");
		}




		$data = array(
			"title"		   => $title,
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

	public function timestampIsValid($timestamp){

		$year = (int) explode("-", $timestamp)[0];

		return $year > 2000;
	}
}
