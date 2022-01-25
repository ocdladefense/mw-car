<?php
    // Represents a single day/court summary for listing on LOD homepage.
?>


<div class="cr-entry">
    <h2>
        <a href="<?php print $appURL; ?>"><?php print $title; ?></a>
    </h2>
    <div class="cr-summary-header">
        <p>by: <?php print $author . " &#8226; " . $publishDate . " &#8226; " ?><a href="<?php print $commentsURL; ?>">comments</a></p>
    </div>

    <?php print $subjectHTML; ?>
    
    <p><a href="<?php print $appURL; ?>">&rarr; read the full summaries...</a></p>

</div>