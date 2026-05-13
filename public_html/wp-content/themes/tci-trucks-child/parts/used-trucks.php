<div class="pgs_cars_carousel-wrapper">
    <div class="pgs_cars_carousel-items" >
        <?php if( count($trucks) ) : ?>
            <div class="row">
                <?php foreach( $trucks as $truck ) : ?>
                    <?php tciRender( 'parts/truck', ['truck'=>$truck] ); ?></div>
                <?php endforeach; ?>
            </div>
        <?php elseif( $notfound ) : ?>
            <p><?php echo $notfound; ?></p>
        <?php endif; ?>
    </div>
</div><!-- .pgs_cars_carousel-wrapper -->