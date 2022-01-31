<?php


    /** template subjects.tpl.php 
     * 
     * Rendering the subject_1 and subject_2 for all cases in a day's worth of reviews. 
    */
?>

<div class="car-subjects">

    <?php foreach($cars as $car): ?>
        <p class="car-subject">
            <?php print strtoupper($car["subject_1"]); ?> - <?php print $car["subject_2"]; ?>
        </p>
    <?php endforeach; ?>

</div>