import $ from 'jquery';

class MyNotes{

    constructor() {
        this.deleteButton = $(".delete-note");
        this.editButton = $(".edit-note");
        this.updateButton = $(".update-note");
        this.createButton = $(".submit-note");
        this.events();
    }

    events(){
        this.deleteButton.on("click",this.deleteNote);
        this.editButton.on("click",this.editNote.bind(this));
        this.updateButton.on("click",this.updateNote.bind(this));
        this.createButton.on("click",this.createNote.bind(this));
    }

    createNote(e){
        var createPost = {
            'title' : $(".new-note-title").val(),
            'content' : $(".new-note-body").val(),
            'status' : 'private'
        }

        $.ajax({
            beforeSend : (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce',universityData.nonce);
            },
            url : universityData.root_url + '/wp-json/wp/v2/note/',
            type : 'POST',
            data : createPost,
            success : (response) => {
                $(".new-note-title, .new-note-body").val('');
                $(`<li data-id = "${response.id}">
                  <input readonly class="note-title-field" value="${response.title.rendered}">
                  <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i>Edit</span>
                  <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i>Delete</span>
                  <textarea readonly class="note-body-field">${response.content.raw}</textarea>
                  <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i>Save</span>
              </li>`).prependTo("#my-notes").hide().slideDown();
            },
            error : (response) => {
                if (response.responseText == "You have reached your note limit"){
                    $(".note-limit-message").addClass("active");
                }
            }
        })
    }

    updateNote(e){
        var thisNote = $(e.target).parents("li");
        var updatePost = {
            'title' : thisNote.find(".note-title-field").val(),
            'content' : thisNote.find(".note-body-field").val()
        }

        $.ajax({
            beforeSend : (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce',universityData.nonce);
            },
            url : universityData.root_url + '/wp-json/wp/v2/note/' + thisNote.data('id'),
            type : 'POST',
            data : updatePost,
            success : () => {
               this.makeNoteReadOnly(thisNote);
            },
            error : () => {
                alert('sorry')
            }
        })
    }

    editNote(e){
        var thisNote = $(e.target).parents("li");

        if (thisNote.data("state") == "editable"){
            this.makeNoteReadOnly(thisNote);
        }else{
            this.makeNoteEditable(thisNote);
        }
    }

    makeNoteEditable(thisNote){
        thisNote.find(".edit-note").html('<i class="fa fa-times " aria-hidden="true"></i> Cancel')
        thisNote.find(".note-title-field, .note-body-field").removeAttr("readonly").addClass("note-active-field");
        thisNote.find(".update-note").addClass("update-note--visible");
        thisNote.data("state","editable");
    }

    makeNoteReadOnly(thisNote){
        thisNote.find(".edit-note").html('<i class="fa fa-pencil" aria-hidden="true"></i> Edit')
        thisNote.find(".note-title-field, .note-body-field").attr("readonly","readonly").removeClass("note-active-field");
        thisNote.find(".update-note").removeClass("update-note--visible");
        thisNote.data("state","readonly");
    }

    deleteNote(e){
        var thisNote = $(e.target).parents("li");

       $.ajax({
           beforeSend : (xhr) => {
               xhr.setRequestHeader('X-WP-Nonce',universityData.nonce);
           },
           url : universityData.root_url + '/wp-json/wp/v2/note/' + thisNote.data('id'),
           type : 'DELETE',
           success : (response) => {
               thisNote.slideUp();
               if (response.userNoteCount < 6){
                   $(".note-limit-message").removeClass("active");
               }
           },
           error : () => {
               alert('sorry')
           }
       })
    }
}

export default MyNotes;