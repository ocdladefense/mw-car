<?php


    /** template subjects.tpl.php 
     * 
     * 
     *  Renders the subject_1 and any subject_2 for a given case review.
    */
?>



<div class="car-subjects">
    <p><?php print $subject; ?></p>
    <p><?php print implode(", ", $subjects); ?></p>
</div>