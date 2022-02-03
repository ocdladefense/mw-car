<?php
    // Represents a single day/court summary for listing on LOD homepage.
    $showMeta = false;
?>


<div class="car-entry">

    <?php foreach($cars as $car) : ?>
    <h2>
        <a href='<?php print "$appDomain/car/list?year=$year&month=$month&day=$day&court=$court"; ?>'>
            <?php print $car["title"]; ?>
        </a>
    </h2>

    <div>
        <P><?php print $car["summary"]; ?></p>
    </div>
    
    <p>
        <a href='<?php print "$appDomain/car/list?year=$year&month=$month&day=$day&court=$court"; ?>'>
            &rarr; read the full summaries...
        </a>
    </p>
    <?php endforeach; ?>

</div>