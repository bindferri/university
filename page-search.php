<?php get_header() ?>
<?php
while (have_posts()){
    the_post();
    pageBanner();
    ?>

    <div class="container container--narrow page-section">
        <?php if (wp_get_post_parent_id(get_the_ID()) > 0) {?>
            <div class="metabox metabox--position-up metabox--with-home-link">
                <p><a class="metabox__blog-home-link" href="<?php echo get_permalink(wp_get_post_parent_id(get_the_ID())) ?>"><i class="fa fa-home" aria-hidden="true"></i> Back to <?php echo get_the_title(wp_get_post_parent_id(get_the_ID())) ?></a> <span class="metabox__main"><?php the_title() ?></span></p>
            </div>
        <?php } ?>

        <?php
        $checkForChildren = get_pages(array(
            'child_of' =>  get_the_ID()
        ));
        if (wp_get_post_parent_id(get_the_ID()) or $checkForChildren){ ?>
            <div class="page-links">
                <h2 class="page-links__title"><a href="#"><?php echo get_the_title(wp_get_post_parent_id(get_the_ID()))?></a></h2>
                <ul class="min-list">
                    <?php
                    wp_list_pages(array(
                        'title_li' => NULL,
                        'child_of' => wp_get_post_parent_id(get_the_ID()) > 0 ? wp_get_post_parent_id(get_the_ID()) : get_the_ID()
                    ));
                    ?>
                </ul>
            </div>
        <?php } ?>

        <div class="generic-content">
            <form class="search-form" action="<?php echo esc_url(site_url("/")) ?>" method="get">
                <label class="headline headline--medium" for="s">Perform a New Search</label>
                <div class="search-form-row">
                <input class="s" id="s" type="search" name="s" placeholder="Enter a value">
                <input class="search-submit" type="submit" value="Search">
                </div>
            </form>
        </div>

    </div>
<?php } ?>

<?php get_footer(); ?>
