<h1>OCDLA Library of Defense - Case Reviews</h1>


<ul>
    <?php foreach($allSummaryLinks as $year => $url) : ?>
        <li class="summary-link">
            <a href="<?php print $url; ?>"><?php print $year; ?> Case Summaries by Topic</a>
        </li>
    <?php endforeach; ?>
</ul>