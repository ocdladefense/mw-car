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

		$this->numRows = $numRows;

        return $this->showCars();
    }


    public function showCars() {

		global $wgOut;

		$oldestDate = new DateTime();
		$oldestDate->modify("-1month");

		$query = "SELECT * FROM car WHERE CreateTime >= '" . $oldestDate->format('Y-m-d H:i:s') . "' ORDER BY Year DESC, Month DESC, Day DESC";

		$cars = select($query);

		$grouped = $this->getGrouped($cars);

        $wgOut->addHTML($this->getHTML($grouped));
    }


	public function getGrouped($cars){

		$urls = array();

		foreach($cars as $car){

			$url = !empty($car->getUrl()) ? $car->getUrl() : $car->getCourt() . ", " . $car->getDate(true);

			$urls[$url][] = $car;
		}

		return $urls;
	}


	public function getHTML($grouped) {

		global $wgScriptPath, $wgOcdlaCaseReviewAuthor;

		$limit = !empty($this->numRows) ? $this->numRows : count($grouped);

		$params = array(
			"grouped" => $grouped,
			"limit" => $limit
		);
		
		$template = __DIR__ . "/templates/case-reviews.tpl.php";

		return \Ocdla\View::renderTemplate($template, $params);
	}



	public function getTitleFromUrl($url){

		$urlParts = explode("/", $url);

		return $urlParts[count($urlParts) -1];
	}
}