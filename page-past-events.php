<?php get_header();
pageBanner(array(
        'title' => 'Past Events',
        'subtitle' => 'Our pas events and their success.',
        'background' => 'images/barksalot.jpg'
));
?>

<div class="container container--narrow page-section">
    <?php
    $events = new WP_Query(array(
            'paged' => get_query_var('paged',1),
            'post_type' => 'event',
            'meta_key' => 'event_date',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key' => 'event_date',
                    'compare' => '<',
                    'value' => date('Ymd'),
                    'type' => 'numeric'
                )
            )
        ));
    while ($events->have_posts()){
        $events->the_post();
        get_template_part("template-parts/content",get_post_type());
         }
    echo paginate_links(array(
            'total' => $events->max_num_pages
    ));
    ?>
</div>
<?php get_footer() ?>
