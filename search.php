<?php get_header();
pageBanner(array(
    'title' => 'Search results for ' . get_search_query(),
    'subtitle' => 'All results for ' . get_search_query(),
    'background' => 'images/barksalot.jpg'
));
?>

<div class="container container--narrow page-section">
    <?php
    if (have_posts()) {
        while (have_posts()) {
            the_post();
            get_template_part('template-parts/content', get_post_type());
    }
    }else{
        echo '<h2 class="headline headline--small-plus">No results match found.</h2>';
    }
    ?>
    <form class="search-form" action="<?php echo esc_url(site_url("/")) ?>" method="get">
        <label class="headline headline--medium" for="s">Perform a New Search</label>
        <div class="search-form-row">
            <input class="s" id="s" type="search" name="s" placeholder="Enter a value">
            <input class="search-submit" type="submit" value="Search">
        </div>
    </form>
</div>
<?php get_footer() ?>
