<?php get_header() ?><div class="container"><div class="row"><div class="col-md-8"><h1 class="my-2 mb-4 page-header">Danh mục: <small><?php single_cat_title() ?></small></h1><?php if ( have_posts() ) : ?><?php while ( have_posts() ) : the_post(); ?><?php get_template_part( 'template-parts/content', get_post_format() ); ?><?php endwhile; ?><?php endif; ?><?php mini_blog_pagination() ?></div><?php get_sidebar() ?></div></div><?php get_footer() ?>