<?php
add_action('rest_api_init','costumRestAPI');

function costumRestAPI(){
    register_rest_route('universiteti/v1','search',array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'universitySearchResults'
    ));
}

function universitySearchResults($data){
    $mainQuery = new WP_Query(array(
        'post_type' => array('post','page','professor','program','campus','event'),
        's' => $data['key']
    ));

    $mainArray = array(
        'generalInfo' => array(),
        'professors' => array(),
        'programs' => array(),
        'events' => array(),
        'campuses' => array()
    );

    while ($mainQuery->have_posts()){
        $mainQuery->the_post();

        if (get_post_type() == 'post' OR get_post_type() == 'page'){
            array_push($mainArray['generalInfo'],array(
                'title' => get_the_title(),
                'url' => get_the_permalink(),
                'postType' => get_post_type(),
                'author' => get_the_author()
            ));
        }
        if (get_post_type() == 'professor'){
            array_push($mainArray['professors'],array(
                'title' => get_the_title(),
                'url' => get_the_permalink(),
                'photo' => get_the_post_thumbnail_url(0,'professorLandscape')
            ));
        }
        if (get_post_type() == 'campus'){
            array_push($mainArray['campuses'],array(
                'title' => get_the_title(),
                'url' => get_the_permalink()
            ));
        }
        if (get_post_type() == 'program'){

            $relatedCampuses = get_field('related_campuses');

            if ($relatedCampuses){
                foreach ($relatedCampuses as $campus){
                    array_push($mainArray['campuses'],array(
                        'title' => get_the_title($campus),
                        'url' => get_the_permalink($campus)
                    ));
                }
            }

            array_push($mainArray['programs'],array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'url' => get_the_permalink()
            ));
        }
        if (get_post_type() == 'event'){
            $eventDate = new DateTime(get_field('event_date'));
            array_push($mainArray['events'],array(
                'title' => get_the_title(),
                'url' => get_the_permalink(),
                'month' => $eventDate->format('M'),
                'day' => $eventDate->format('d'),
                'description' => wp_trim_words(get_the_content(),18)
            ));
        }
    }

    if ($mainArray['programs']) {

        $programMetaQuery = array('relation' => 'OR');

        foreach ($mainArray['programs'] as $program) {
            array_push($programMetaQuery, array(
                'post_type' => 'professor',
                'meta_query' => array(array(
                    'key' => 'related_program',
                    'compare' => 'LIKE',
                    'value' => '"' . $program['id'] . '"'
                ))
            ));
        }

        $programRelationshipQuery = new WP_Query(array(
            'post_type' => array('professor','event'),
            'meta_query' => $programMetaQuery
        ));

        while ($programRelationshipQuery->have_posts()) {
            $programRelationshipQuery->the_post();

            if (get_post_type() == 'professor') {
                array_push($mainArray['professors'], array(
                    'title' => get_the_title(),
                    'url' => get_the_permalink(),
                    'photo' => get_the_post_thumbnail_url(0, 'professorLandscape')
                ));

            }
            if (get_post_type() == 'event') {
                $eventDate = new DateTime(get_field('event_date'));
                array_push($mainArray['events'], array(
                    'title' => get_the_title(),
                    'url' => get_the_permalink(),
                    'month' => $eventDate->format('M'),
                    'day' => $eventDate->format('d'),
                    'description' => wp_trim_words(get_the_content(),18)
                ));

            }
        }

        $mainArray['professors'] = array_values(array_unique($mainArray['professors'], SORT_REGULAR));
        $mainArray['events'] = array_values(array_unique($mainArray['events'], SORT_REGULAR));
    }
    return $mainArray;
}
