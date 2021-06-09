import $ from 'jquery';

class Like{

    constructor() {
        this.events();
    }

    events(){
        $(".like-box").on("click",this.createOrDeleteLike.bind(this));
    }

    createOrDeleteLike(e){
        var currentLikeBox = $(e.target).closest(".like-box");

        if (currentLikeBox.attr('data-exists') == 'yes'){
            this.dislikeProfessor(currentLikeBox);
        }else {
            this.likeProfessor(currentLikeBox);
        }
    }

    likeProfessor(currentLikeBox){

        $.ajax({
            beforeSend : (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce',universityData.nonce);
            },
            url: universityData.root_url + '/wp-json/university/v1/manageLike',
            type: 'POST',
            data: {
              'professor_id' : currentLikeBox.data("professor")
            },
            success: (response) => {
                currentLikeBox.attr('data-exists','yes');
                var likeCount = parseInt(currentLikeBox.find(".like-count").html(),10);
                likeCount++;
                currentLikeBox.find(".like-count").html(likeCount)
                currentLikeBox.attr("data-like",response)
            },
            error: (response) => {
                alert(response)
            }
        })
    }

    dislikeProfessor(currentLikeBox){
        $.ajax({
            beforeSend : (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce',universityData.nonce);
            },
            url: universityData.root_url + '/wp-json/university/v1/manageLike',
            data: {
                'like' : currentLikeBox.attr('data-like')
            },
            type: 'DELETE',
            success: (response) => {
                currentLikeBox.attr('data-exists','no');
                var likeCount = parseInt(currentLikeBox.find(".like-count").html(),10);
                likeCount--;
                currentLikeBox.find(".like-count").html(likeCount);
                currentLikeBox.attr("data-like",'');
            },
            error: (response) => {
                console.log(response)
            }
        })
    }
}
export default Like;