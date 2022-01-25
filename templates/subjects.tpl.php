<?php


    /** template subjects.tpl.php 
     * 
     * Rendering the subject_1 and subject_2 for all cases in a day's worth of reviews. 
    */
?>

<div class="car-subjects">
    <p><?php print $firstSubject; ?></p>
    <p><?php print implode(", ", $subjects); ?></p>
</div>