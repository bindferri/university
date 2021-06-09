<?php

require get_theme_file_path('/includes/costum-rest-api.php');
require get_theme_file_path('/includes/like-route.php');

function university_custom_rest_api(){
    register_rest_field('post','authorName',array(
            'get_callback' => function (){
                return get_the_author();
            }
    ));

    register_rest_field('note','userNoteCount',array(
            'get_callback' => function (){
                return count_user_posts(get_current_user_id(),'note');
            }
    ));
}

add_action('rest_api_init','university_custom_rest_api');

function pageBanner($data = null){ ?>

    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php echo (empty($data['background']) ? (empty(get_field('page_banner_background-image')['sizes']['banner-backgroundimage']) ? get_theme_file_uri('images/ocean.jpg')   : get_field('page_banner_background-image')['sizes']['banner-backgroundimage']) : get_theme_file_uri($data['background'])) ?>"></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php echo (empty($data['title']) == true ? get_the_title() : $data['title']) ?></h1>
            <div class="page-banner__intro">
                <p><?php echo (empty($data['subtitle']) ? get_field('page_banner_subtitle') : $data['subtitle'])?></p>
            </div>
        </div>
    </div>
<?php }

function university_files(){

    wp_enqueue_style('family-fonts','//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome','//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    if (strstr($_SERVER['SERVER_NAME'],'localhost')){
        wp_enqueue_script('main-university-js','http://localhost:3000/bundled.js',NULL,'1.0',true);
    }else{
        wp_enqueue_script('our-vendors-js',get_theme_file_uri('/bundled-assets/vendors~scripts.9678b4003190d41dd438.js'),NULL,'1.0',true);
        wp_enqueue_script('main-university-js',get_theme_file_uri('/bundled-assets/scripts.1f1c4a6280c65c7b6b9a.js'),NULL,'1.0',true);
        wp_enqueue_style('our-main-style',get_theme_file_uri('/bundled-assets/styles.1f1c4a6280c65c7b6b9a.css')) ;
    }

    wp_localize_script('main-university-js','universityData',array(
            'root_url' => get_site_url(),
            'nonce' => wp_create_nonce('wp_rest')
    ));
}

add_action('wp_enqueue_scripts','university_files');

function university_features(){
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('professorLandscape',400,260,true);
    add_image_size('professorPortrait',480,650,true);
    add_image_size('banner-backgroundimage',1500,350,true);
}

add_action('after_setup_theme','university_features');

function university_adjust_queries($query){
    if (!is_admin() && is_post_type_archive('event') && $query->is_main_query()){
        $query->set('meta_key','event_date');
        $query->set('orderby','meta_value_num');
        $query->set('order' , 'ASC');
        $query->set('meta_query' , array(
            array(
                'key' => 'event_date',
                'compare' => '>=',
                'value' => date('Ymd'),
                'type' => 'numeric'
            )));

    }

    if (!is_admin() AND is_post_type_archive('program') AND $query->is_main_query()){
        $query->set('orderby','title');
        $query->set('order' , 'ASC');
        $query->set('posts_per_page' , -1);
    }
}

add_action('pre_get_posts','university_adjust_queries');

function university_map($api){
    $api['key'] = 'AIzaSyBto0pdcvm4Xicac9nJ19c_F9rfW_mvnvo';
    return $api;
}

add_filter('acf/fields/google_map/api','university_map');

function redirect_subs(){

    $current_user = wp_get_current_user();

    if (count($current_user->roles) == 1 AND $current_user->roles[0] == 'subscriber'){
        wp_redirect(site_url('/'));
        exit;
    }
}


add_action('admin_init','redirect_subs');

function remove_adminBar(){

    $current_user = wp_get_current_user();

    if (count($current_user->roles) == 1 AND $current_user->roles[0] == 'subscriber'){
       show_admin_bar(false);
    }
}


add_action('wp_loaded','remove_adminBar');


// Customize Login System

function headerUrl(){
    return esc_url( site_url('/'));
}

    add_filter('login_headerurl','headerUrl');

//Force note posts to be private

function myNote_private($data,$postarr){

    if ($data['post_type'] == 'note'){
        if (count_user_posts(get_current_user_id(),'note') > 5 AND !$postarr['ID']){
            die("You have reached your note limit");
        }

        $data['post_content'] = sanitize_textarea_field($data['post_content']);
        $data['post_title'] = sanitize_text_field($data['post_title']);
    }

    if ($data['post_type'] == 'note' AND $data['post_status'] != 'trash') {
        $data['post_status'] = 'private';
    }
    return $data;
}

    add_filter('wp_insert_post_data','myNote_private',10,2);