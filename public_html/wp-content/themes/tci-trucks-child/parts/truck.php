<div class='item col-sm-4'>
    
    <div class='car-item text-center car-item-2 white-bg'>
        <div class='car-image'>
            
            <img src="<?php echo $truck['featured_image_url']; ?>"
                 class="img-responsive" class="img-responsive" alt="<?php echo $data['title']['rendered']; ?>"/>								        <div class='car-overlay-banner'>
            <ul>
                <li>
                    <a href="<?php echo $truck['link']; ?>" data-toggle="tooltip" title="View">
                        <i class="fas fa-link"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="car-list">
        <ul class="list-inline">
            <li><i class="fas fa-map-marker-alt"></i> <?php echo $truck['data']['location'][0]['name']; ?></li>
        </ul>
    </div>		
    <div class='car-content'>
        <a href="<?php echo $truck['link']; ?>">
            <?php echo $truck['title']['rendered']; ?>
        </a>
        <div class="separator"></div>
    </div>
</div>
