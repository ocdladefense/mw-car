<?php

use function Mysql\select;
use function \Ocdla\View\renderTemplate;


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

		$wgOut->addHTML($html);

		var_dump($days);exit;
    }


	public function group($cars){

		$days = array();

		// Assumes results are already sorted DESC by year, month, and day, so array will start with most recent cars.
		foreach($cars as $car){

			// Organize car cases by date; @TODO AND court!
			// Normally we can accept 2021-11-04 but could we try 2021-11-4 (our database doesn't store leading zeros)?
			$key = new DateTime($car["year"] ."-". $car["month"] ."-". $car["day"]);
			$key = $key->format("F, d, Y");
			$court = $car["court"];

			$keyString = $court .", ". $key;

			$days[$keyString][] = $car;
		}

		return $days;
	}


	public function getHTML($days) {

		global $wgScriptPath, $wgOcdlaCaseReviewAuthor;

		$subjectTemplate = __DIR__ . "/templates/subjects.tpl.php";
		$summaryTemplate = __DIR__ . "/templates/summary.tpl.php";
		
		// Opening container tags
		$html .= "<div class='car-wrapper'>";
		$html .= "<div class='car-roll'>";


		foreach($days as $title => $cars){

			$params = $this->preProcess($title, $cars);

			$subjects = $this->renderTemplate($subjectTemplate, $params);

			var_dump($subjects);exit;
		}


		// Closing containser tags
		$html .= "</div></div>";

		return $html;
	}

	public function preProcess($title, $cars){

		$subjects = $this->getSubjects($cars);

		$data = array(
			"firstSubject" => array_shift($data["subjects"]),
			"subjects"	   => $subjects
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

	public function renderTemplate($template, $params) {

		extract($params);

		ob_start();

		require $template;
		
		$content = ob_get_contents();

		ob_end_clean();
		
		return $content;
	}

}