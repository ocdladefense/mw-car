<?php
    global $wgScriptPath, $wgOcdlaCaseReviewAuthor, $wgOcdlaAppCarSummaryURL;

    $index = 0;
?>

<div class="cr-wrapper">
    <div class="cr-roll">

        <?php foreach($grouped as $url => $cars) : 

            $index++;
            $title = $this->getTitleFromUrl($url);
            $cleanTitle = str_replace("_", " ", $title);
            $createTime = $cars[0]->getCreateTime();
            $year = $cars[0]->getYear();
            $month = $cars[0]->getMonth();
            $day = $cars[0]->getDay();
            $court = $cars[0]->getCourt();

            // A URL only needs to be encoded if trying to access a url outside of MediaWiki install????
            $commentsUrl = "$wgScriptPath/Blog_talk:Case_Reviews/$title";
            $appUrl = "$wgOcdlaAppCarSummaryURL?year=$year&month=$month&day=$day&court=$court&summarize=1";

            $subjects = array();
            foreach($cars as $car){

                $subjects[] = $car->getSubject1() . " - " . $car->getSubject2();
            }

            $firstSubject = array_shift($subjects);

        ?>
            <div class="cr-entry">
                <h2>
                    <a href="<?php print $appUrl; ?>"><?php print $cleanTitle; ?></a>
                </h2>
                <div class="cr-summary-header">
                    <p>by: <?php print $wgOcdlaCaseReviewAuthor . " &#8226; " . $createTime . " &#8226; " ?><a href="<?php print $commentsUrl; ?>">comments</a></p>
                </div>
                <p><?php print $firstSubject; ?></p>
                <p><?php print implode(", ", $subjects); ?></p>
                <p><a href="<?php print $appUrl; ?>">&rarr; read the full summaries...</a></p>
            </div>

            <?php if($limit == $index) break; ?>

        <?php endforeach; ?>

    </div>
</div>