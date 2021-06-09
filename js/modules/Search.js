import $ from 'jquery';
class Search{

    constructor() {
        this.addSearchHTML();
        this.openButton = $(".js-search-trigger");
        this.closeButton = $(".search-overlay__close");
        this.searchOverlay = $(".search-overlay");
        this.searchInput = $("#search-term");
        this.isOpen = false;
        this.isSpinnerVisible = false;
        this.resetTimer;
        this.previousValue;
        this.resultsDiv = $("#search-overlay__results");
        this.events();

    }

    events(){
        this.openButton.on("click",this.openOverlay.bind(this));
        this.closeButton.on("click",this.closeOverlay.bind(this));
        $(document).on("keydown",this.keyPress.bind(this));
        this.searchInput.on("keyup",this.typingSuggestion.bind(this));

    }

    typingSuggestion(){
        if (this.searchInput.val() != this.previousValue){
            clearTimeout(this.resetTimer);
            if (this.searchInput.val()){
                if (!this.isSpinnerVisible) {
                    this.resultsDiv.html('<div class="spinner-loader"></div>');
                    this.isSpinnerVisible = true;
                }
                this.resetTimer = setTimeout(this.getResults.bind(this),500);
            }else {
                this.resultsDiv.html('');
                this.isSpinnerVisible = false;
            }

        }

        this.previousValue = this.searchInput.val();
    }

    getResults(){
        $.getJSON(universityData.root_url + '/wp-json/universiteti/v1/search?key=' + this.searchInput.val(),mainArray => {
            this.resultsDiv.html(`
            <div class="row">
                <div class="one-third">
                <h2 class="search-overlay__section-title">General Information</h2>
                      ${mainArray.generalInfo.length ? '<ul class="link-list min-list">' : '<p>No general information matches that search</p>'}
            ${mainArray.generalInfo.map(item => `<li><a href="${item.url}">${item.title}</a> ${item.postType == 'post' ?  `by ${item.author}` : ''} </li>`).join('')}
            ${mainArray.generalInfo.length ? '</ul>' : ''}
           
                </div>
                <div class="one-third">
                <h2 class="search-overlay__section-title">Programs</h2>
                ${mainArray.programs.length ? '<ul class="link-list min-list">' : '<p>No progams match that search</p>'}
            ${mainArray.programs.map(item => `<li><a href="${item.url}">${item.title}</a> </li>`).join('')}
            ${mainArray.programs.length ? '</ul>' : ''}
                
                <h2 class="search-overlay__section-title">Professors</h2>
                ${mainArray.professors.length ? '<ul class="professor-cards">' : '<p>No professors match that search</p>'}
            ${mainArray.professors.map(item => `  <li class="professor-card__list-item">
        <a class="professor-card" href="${item.url}">
            <img src="${item.photo}" alt="" class="professor-card__image">
            <span class="professor-card__name">${item.title}</span>
        </a>
        </li>`).join('')}
            ${mainArray.professors.length ? '</ul>' : ''}
                </div>
                <div class="one-third">
                <h2 class="search-overlay__section-title">Canmpuses</h2>
                ${mainArray.campuses.length ? '<ul class="link-list min-list">' : '<p>No campuses match that search</p>'}
            ${mainArray.campuses.map(item => `<li><a href="${item.url}">${item.title}</a></li>`).join('')}
            ${mainArray.campuses.length ? '</ul>' : ''}
                
                <h2 class="search-overlay__section-title">Events</h2>
                ${mainArray.events.length ? '' : '<p>No events match that search</p>'}
            ${mainArray.events.map(item => `
            <div class="event-summary">
                    <a class="event-summary__date event-summary__date--beige t-center" href="${item.url}">
                        <span class="event-summary__month">${item.month}</span>
                        <span class="event-summary__day">${item.day}</span>
                    </a>
                    <div class="event-summary__content">
                        <h5 class="event-summary__title headline headline--tiny"><a href="${item.url}">${item.title}</a></h5>
                        <p>${item.description}<a href="${item.url}" class="nu gray">Read more</a></p>
                    </div>
                </div>
            `).join('')}
           
                </div>
            </div>
            `)
            this.isSpinnerVisible = false;
        })
    }

    keyPress(e){
        if (e.keyCode == 83 && this.isOpen == false && !$("input,textarea").is(':focus')){
             this.openOverlay();
        }

        if (e.keyCode == 27 && this.isOpen){
            this.closeOverlay();
        }
    }

    openOverlay(){
        this.searchOverlay.addClass("search-overlay--active")
        $("body").addClass("body-no-scroll");
        this.searchInput.val('');
        setTimeout(() => this.searchInput.focus(),301)
        this.isOpen = true;
        return false;
    }

    closeOverlay(){
        this.searchOverlay.removeClass("search-overlay--active")
        $("body").removeClass("body-no-scroll");
        this.isOpen = false;
    }

    addSearchHTML(){
        $("body").append(`
        <div class="search-overlay">
<div class="search-overlay__top">
    <div class="container">
        <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
        <input type="text" class="search-term" placeholder="What are you looking for" id="search-term" autocomplete="off">
        <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
    </div>
</div>
    <div class="container">
        <div id="search-overlay__results">

        </div>
    </div>
</div>
        `)
    }
}

export default Search;