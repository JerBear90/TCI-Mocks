<?php
function contact_information() {
    ini_set('display_errors','on');
    ob_start();
    global $post;
    // d(get_post_custom());
    list( $contact ) = wp_get_object_terms( $post->ID, 'contact' );
    list( $location ) = wp_get_object_terms( $post->ID, 'location' );
    d('CONTACT:',$contact);
    if( @$contact ) {
        $phone_num = get_term_meta( $contact->term_id, 'phone', true );
        $email = get_term_meta( $contact->term_id, 'email', true );
    }
    ?>
    <div class="location">
        <h4>Contact Information</h4>
        <div class="city capitalize">
            <?php
            /*<a href="<?php echo get_term_link( $location ); ?>">
                TCI <?php echo $location->name; ?>
            </a>
            */ ?>
        </div>
        <div class="address">
            <?php echo $location->name; ?>, <?php echo get_post_meta( $post->ID, 'LocationState', true ); ?> <?php echo get_post_meta( $post->ID, 'LocationPostalCode', true ); ?>
        </div>
        <?php if( @$contact ) : ?>
            <div class="phone"><strong>Phone:</strong> <a href="tel:<?php echo $phone_num; ?>"><?php echo $phone_num; ?></a></div>
            <div class="contact"><strong>Contact:</strong> <?php echo $contact->name; ?></div>
        <?php endif; ?>
    </div>
    <?php
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}
add_shortcode( 'truck_contact_info', 'contact_information' );

function used_trucks_shortcode( $atts=[] ) {
    $feed = get_option( 'trucks_feed' );
    $_GET['debug'] = 1;
    
    $contents = file_get_contents( $feed );
    // d($contents);
    if( $contents ) {
        $data = @json_decode( $contents, 1 );
        $trucks = [];
        // d('feed:',$data );
        $location = $atts['location'];
        
        foreach( $data as $item ) {
            foreach( $item['data']['location'] as $tl ) {
                if( $tl['name']  == $location || $tl['slug']  == $location ){
                    $trucks[] = $item;
                }
            }
        }
    }
    // d('trucks:',count($trucks));
    // foreach( $trucks as $truck ) d($truck['name'], $truck['location']);
    if( !$trucks ) $trucks = [];
    $trucks = array_slice( $trucks, 0, 3 );
    return tciRender( 'parts/used-trucks', ['trucks'=>$trucks,'notfound' => @$atts['notfound'] ], false );
}
add_shortcode( 'used_trucks', 'used_trucks_shortcode' );

function jobs_shortcode( $atts=[] ) {
    $feed = get_option( 'jobs_feed', 'https://careers.tcitransportation.com/wp-json/tci/v1/jobs' );
    
    $contents = file_get_contents( $feed );
    if( $contents ) {
        $data = json_decode($contents,1);
        $jobs = [];
        
        $location = $atts['location'];
        // d($data);
        foreach( $data['jobs'] as $job ) {
            // d($job);
            foreach( $job['Locations'] as $jl) {
                // d($jl,$location);
                if( strtolower($jl['City']) == strtolower($location) ) $jobs[] = $job;
            }
        }
    }
    // d('trucks:',count($trucks));
    // d('jobs:',$atts);
    // foreach( $trucks as $truck ) d($truck['name'], $truck['location']);
    $jobs = array_slice( $jobs, 0, 3 );
    return tciRender( 'parts/latest-jobs', ['jobs'=>$jobs,'notfound' => @$atts['notfound'] ], false );
}
add_shortcode( 'latest_jobs', 'jobs_shortcode' );