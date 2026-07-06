<?php get_header(); ?>

<div class="section">
  <div class="container" style="max-width:860px; padding-top:3rem; padding-bottom:4rem">
    <?php while ( have_posts() ) : the_post(); ?>
      <h1><?php the_title(); ?></h1>
      <div class="entry-content" style="margin-top:2rem; line-height:1.8; font-size:1.05rem">
        <?php the_content(); ?>
      </div>
    <?php endwhile; ?>
  </div>
</div>

<?php get_footer(); ?>
