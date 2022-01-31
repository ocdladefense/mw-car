<?php


    /** template subjects.tpl.php 
     * 
     * Rendering the subject_1 and subject_2 for all cases in a day's worth of reviews. 
    */
?>

<div class="car-subjects">

    <p>

        <?php
            $subjects = array();
            
            foreach($cars as $car) {
                
                $subject = strtoupper($car["subject_1"]) . " - " . $car["subject_2"];
                $subjects[] = $subject;
            }

            print implode(",",$subjects);
        ?>

    </p>

</div>