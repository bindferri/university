<?php

add_action('rest_api_init','like_rest_api');

function like_rest_api(){
   register_rest_route('university/v1','manageLike',array(
       'methods' => 'POST',
       'callback' => 'createLike'
   ));

   register_rest_route('university/v1','manageLike',array(
       'methods' => 'DELETE',
       'callback' => 'deleteLike'
   ));
}

function createLike($data){
    if (is_user_logged_in()){
        $id = $data['professor_id'];

        $existsLike = new WP_Query(array(
            'author' => get_current_user_id(),
            'post_type' => 'like',
            'meta_query' => array(array(
                'key' => 'liked_professor_id',
                'compare' => '=',
                'value' => $id
            ))
        ));
        if ($existsLike->found_posts == 0 AND get_post_type($id) == 'professor'){
            return  wp_insert_post(array(
                'post_type' => 'like',
                'author' => get_current_user_id(),
                'post_status' => 'publish',
                'meta_input' => array(
                    'liked_professor_id' => $id
                )
            ));
        }else{
            die("invalid professor id");
        }


    }else{
        die("You must be logged in to have access");
    }


}

function deleteLike($data){
    $id = $data['like'];
    if (is_user_logged_in()) {
        if (get_current_user_id() == get_post_field('post_author',$id) AND get_post_type($id) == 'like'){
            wp_delete_post($id,true);
            return 'Congrats, like deleted';
        }else{
            die("YOU DONT HAVE PERMISSION TO DELETE");
        }
    }

}