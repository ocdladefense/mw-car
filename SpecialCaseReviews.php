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

		// The syntax for this will be "$this->getOutput()" in later versions.
		$wgOut->addHTML($html);
    }


	public function group($cars){
		
		$days = array();

		// Assumes results are already sorted DESC by year, month, and day, so array will start with most recent cars.
		foreach($cars as $car){

			// Normally we can accept 2021-11-04 but could we try 2021-11-4 (our database doesn't store leading zeros)?
			$key = new DateTime($car["year"] ."-". $car["month"] ."-". $car["day"]);

			$key = $key->format("F j, Y");

			$days[$key][] = $car;
		}

		return $days;
	}


	public function getHTML($days) {

		global $wgScriptPath, $wgOcdlaCaseReviewAuthor;

		$subjectTemplate = __DIR__ . "/templates/subjects.tpl.php";
		$summaryTemplate = __DIR__ . "/templates/summary.tpl.php";

		$html = "";
		
		// Opening container tags
		$html .= "<div class='car-wrapper'>";
		$html .= "<div class='car-roll'>";


		foreach($days as $decisionDate => $cars){

			$params = $this->preProcess($decisionDate, $cars);

			$params["subjectHTML"] = View::renderTemplate($subjectTemplate, $params);

			$html .= View::renderTemplate($summaryTemplate, $params);
		}

		// Closing containser tags
		$html .= "</div></div>";

		return $html;
	}

	public function preProcess($decisionDate, $cars){

		global $wgOcdlaAppCarSummaryURL, $wgOcdlaCaseReviewAuthor, $wgScriptPath;

		$year = $cars[0]["year"];
		$month = $cars[0]["month"];
		$day = $cars[0]["day"];
		$court = $cars[0]["court"];
		$title = "$court, $decisionDate";
		$subjects = $this->getSubjects($cars);

		// Build the published date.
		$publishDate = $cars[0]["createtime"];
		$publishDate = new DateTime($publishDate);
		$publishDate = $publishDate->format("l, F jS, Y");

		// Build the url for the comments page.
		$resource = str_replace(" ", "_", $title);
		$commentsURL = "$wgScriptPath/Blog_talk:Case_Reviews/$resource";


		$data = array(
			"firstSubject" => array_shift($subjects),
			"subjects"	   => $subjects,
			"title"		   => $title,
			"author"	   => $wgOcdlaCaseReviewAuthor,
			"commentsURL"  => $commentsURL,
			"publishDate"  => $publishDate,
			"appURL"	   => "$wgOcdlaAppCarSummaryURL?year=$year&month=$month&day=$day&court=$court&summarize=1"
		);

		return $data;
	}

	public function getSubjects($cars){

		$subjects = array();

		foreach($cars as $car){

			$subjects[] = $car["subject_1"] ." - ". $car["subject_2"];
		}

		return $subjects;
	}
}
