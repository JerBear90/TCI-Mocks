
<div class="pgs_cars_carousel-wrapper">
    <div class="pgs_cars_carousel-items" >
        <?php if( count($jobs) ) : ?>
            <div class="row">
                <?php foreach( $jobs as $job ) : ?>
                    <?php tciRender( 'parts/job', ['job'=>$job] ); ?></div>
                <?php endforeach; ?>
            </div>
        <?php elseif( $notfound ) : ?>
            <p><?php echo $notfound; ?></p>
        <?php endif; ?>
    </div>
</div><!-- .pgs_cars_carousel-wrapper -->