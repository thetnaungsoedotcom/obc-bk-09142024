<?php
$search_refer = $_REQUEST["site-section"];
if ($search_refer == 'event') :
	$is_default_query = true;
?>
<?php get_header('fullwidth'); ?>
<h1><?php _e("Search Results"); ?></h1>
	<div class="module">
	<?php 
		global $wp_query;
		if( $wp_query->post_count > 0 ){
			//if event is search thru multiple categories, we need to attach the category name to the title
			if( $_REQUEST['caltype'] && $_REQUEST['caltype'] == 'm' ){
				$is_event_search = true;
			}
			get_template_part('loop', 'fbi_event'); 
		}
		else{
			echo '<p>No event is found.</p>';	
		}
	?>
	</div>
<?php	get_footer('fullwidth'); ?>

<?php else: ?>

<?php get_header(); ?>
<h1>
	<?php _e("Search Results"); ?>
</h1>
<div class="module module-content">
	<div class="module-search-results">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>	
	
			<h4><a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
			<p> <!--
				Posted by: <span class="author"><?php the_author() ?></span>.
				<time datetime="<?php the_time('Y-m-d')?>">Posted:
					<?php the_time('j F Y') ?>
				</time>-->
				<!--			
				<?php if ( comments_open() ) : ?>
				<a class="comment" href="<?php the_permalink(); ?>#comments">
				<?php comments_number('0 Comments', '1 Comment', '% Comments'); ?>
				</a>
				<?php endif; ?>
				-->
			</p>
			<p>
			
			<?php echo string_limit_words(strip_tags(get_the_content()), 30) . '...'; ?>
			</p>
		<?php endwhile; else: ?>
			<p>
				<?php _e('Sorry, no posts matched your criteria.'); ?>
			</p>
		<?php endif; ?>
	</div>
</div>
<?php get_footer('fullwidth') ?>
<?php endif; ?>