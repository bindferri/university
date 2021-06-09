<?php get_header();
pageBanner(array(
    'subtitle' => 'Learn how the school of your dreams got started.'
));
?>
<?php
$relatedPrograms = new WP_Query(array(
   'posts_per_page' => -1,
   'post_type' => 'program',
   'orderby' => 'title',
   'order' => 'ASC',
    'meta_query' => array(
        array(
            'key' => 'related_campuses',
            'compare' => 'LIKE',
            'value' => '"' . get_the_ID() . '"'
        )
    )
));
while (have_posts()){
    the_post();
    ?>

    <div class="container container--narrow page-section">
        <div class="metabox metabox--position-up metabox--with-home-link">
            <p><a class="metabox__blog-home-link" href="<?php echo site_url('/campuses') ?>"><i class="fa fa-home" aria-hidden="true"></i> All Campuses</a> <span class="metabox__main"><?php the_title() ?></span></p>
        </div>
        <div class="generic-content">
            <?php the_content(); ?>
        </div>

    <?php
}
    if ($relatedPrograms){ ?>
        <hr class="section-break">
        <h2 class="headline headline--medium">Program Available At This Campus</h2>
        <ul class="min-list link-list">
        <?php while ($relatedPrograms->have_posts()){
            $relatedPrograms->the_post();
            ?>
            <a href="<?php the_permalink(); ?>"><?php the_title() ?></a>
    <?php }
    ?>
        </ul>
    </div>
        <?php }
?>

<?php get_footer(); ?>
