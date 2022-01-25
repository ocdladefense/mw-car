<?php

use function Mysql\select;


class SpecialCaseReviews extends SpecialPage {

	private $numRows;




    public function __construct() {

        parent::__construct("CaseReviews");

		$this->getOutput()->addModules("ext.caseReviews");

		$this->mIncludable = true;
    }


    public function execute($numRows) {


		global $wgOut;

		$template = __DIR__ . "/templates/case-reviews.tpl.php";
		// $out = $this->getOutput();


		$query = "SELECT year, month, day, createtime, subject_1, subject_2 FROM car ORDER BY year DESC, month DESC, day DESC LIMIT {$numRows}"; // court


		// https://dev.mysql.com/doc/refman/8.0/en/aggregate-functions.html#function_group-concat
		// SELECT LEFT(GROUP_CONCAT(subject_1), 40), year, month, day FROM car GROUP BY year, month, day ORDER BY year DESC, month DESC, day DESC LIMIT 50
		// alter table car ADD createtime 
		// ALTER TABLE car ADD createtime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP

		// SELECT COUNT(*) FROM car WHERE year=2021 AND month=11 AND day=24

		// Do we have an option NOT to use a custom class.
		$cars = select($query);

		$data = $this->preprocess($cars);

		$html = \Ocdla\View::renderTemplate($template, $params);
		
		$out->addHTML("<div class='car-wrapper'>");
		$out->addHTML("<div class='car-roll'>");

        $wgOut->addHTML($html);

		$out->addHTML("</div></div>");
    }




	public function preprocess($cars) {

		global $wgScriptPath, $wgOcdlaCaseReviewAuthor, $wgOcdlaAppCarSummaryURL;

		$wgOcdlaAppCarSummaryURL = "https://ocdla.app";
		
		$days = array();



		

		$index = 0;

		// Assumes results are already sorted DESC by year, month, day
		// so array will start with most recent cars.
		foreach($cars as $car) {

			$index++;

			$year = $car["year"];
			$month = $car["month"];
			$day = $car["day"];


			// A URL only needs to be encoded if trying to access a url outside of MediaWiki install????
			$appUrl = "$wgOcdlaAppCarSummaryURL?year=$year&month=$month&day=$day&court=$court&summarize=1";


			// Organize car cases by date; @TODO AND court!
			// Normally we can accept 2021-11-04 but could we try 2021-11-4 (our database doesn't store leading zeros)?
			// $key = new DateTime("2021-11-4");
			$key = new DateTime($car["year"] ."-". $car["month"] ."-". $car["day"]); 
			
			$car["some-nice-formatted-date-value"] = "some new calculated or formatted value";
			// $url = !empty($car->getUrl()) ? $car->getUrl() : $car->getCourt() . ", " . $car->getDate(true);

			$days[$key][] = $car;
		}

		return $days;
	}



}