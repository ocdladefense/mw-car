<?php


    /** template subjects.tpl.php 
     * 
     * Rendering the subject_1 and subject_2 for all cases in a day's worth of reviews. 
    */
?>

<div class="car-subjects">

    <p>

        <?php
            $subjects = "";
            
            foreach($cars as $car) $subjects .= strtoupper($car["subject_1"]) . " - " . $car["subject_2"] . ", ";

            print trim($subjects, ", ");
        ?>

    </p>

</div>