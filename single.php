<?php get_header();
pageBanner(array(
        'subtitle' => 'Learn how the school of your dreams got started.'
));
?>
<?php
while (have_posts()){
    the_post();
    ?>

    <div class="container container--narrow page-section">
        <div class="metabox metabox--position-up metabox--with-home-link">
            <p><a class="metabox__blog-home-link" href="<?php echo site_url('/blog') ?>"><i class="fa fa-home" aria-hidden="true"></i> BlogHome</a> <span class="metabox__main">Posted by <?php the_author_posts_link(); ?> on <?php the_date() ?></span></p>
        </div>
        <div class="generic-content">
            <?php the_content(); ?>
        </div>
    </div>
    <?php
}
?>
<?php get_footer(); ?>
