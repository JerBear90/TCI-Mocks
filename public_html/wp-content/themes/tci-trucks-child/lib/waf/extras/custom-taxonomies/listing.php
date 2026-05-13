<?php $fname = 'loop-'.$post_type.'.php'; ?>
<?php if( $template = locate_template( $fname, false ) ) : ?>
	<?php include $template; ?>
<?php else : ?>
	<?php if( have_posts() ) while( have_posts() ) : the_post(); ?>
		<div <?php post_class('clearfix'); ?>>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<div class="entry-content">
				<?php if( has_post_thumbnail() ) the_post_thumbnail(); ?>
				<?php the_content(); ?>
			</div><!-- end .entry-content -->
		</div><!-- end .post -->
	<?php endwhile; ?>
<?php endif; ?>
