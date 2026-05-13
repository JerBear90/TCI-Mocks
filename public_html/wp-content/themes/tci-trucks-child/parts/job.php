<?php
$job['link'] = get_option('job_site','https://careers.tcitransportation.com' ).'/job/'.$job['JobID'];
// d(get_object_vars($job));
// d($job);
?>
<div class='item col-sm-4'>
    
    <div class='car-item job-item text-center car-item-2 white-bg'>
        <div class='car-image'>
            
            <img src="<?php echo $job['CategoryIcon']; ?>"
                 class="img-responsive job-image" alt="<?php echo $job['JobTitle']; ?>"/>								        <div class='car-overlay-banner'>
            <ul>
                <li>
                    <a href="<?php echo $job['link']; ?>" data-toggle="tooltip" title="View">
                        <i class="fas fa-link"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="car-list">
        <ul class="list-inline">
            <li><i class="fas fa-map-marker-alt"></i> 
                <?php echo $job['Location']['City'].', '.$job['Location']['State']; ?>
            </li>
        </ul>
    </div>		
    <div class='car-content'>
        <a href="<?php echo $job['link']; ?>">
            <?php echo $job['JobTitle']; ?>
        </a>
        <div class="separator"></div>
    </div>
</div>
