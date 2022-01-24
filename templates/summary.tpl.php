<?php


    /** Template <summary class="tpl php">
     * 
     *  Represents a single day/court summary for listing on LOD homepage.
     * 
     *   
     * 
     * 
     * </summary> */

?>

<div class="cr-wrapper">
    <div class="cr-roll">


            <div class="cr-entry">
                <h2>
                    <a href="<?php print $appUrl; ?>"><?php print $cleanTitle; ?></a>
                </h2>
                <div class="cr-summary-header">
                    <p>by: <?php print $wgOcdlaCaseReviewAuthor . " &#8226; " . $createTime . " &#8226; " ?><a href="<?php print $commentsUrl; ?>">comments</a></p>
                </div>


                <?php print $subjects; ?>

                
                <p><a href="<?php print $appUrl; ?>">&rarr; read the full summaries...</a></p>
            </div>

            <?php if($limit == $index) break; ?>

        <?php endforeach; ?>

    </div>
</div>