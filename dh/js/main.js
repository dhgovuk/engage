(function($) {
  $( ".tabify" ).easytabs({animate: false, tabActiveClass: 'ui-tabs-active', updateHash: false});
  
  /**
   * DH: Filters Widget Stuff
   */
  $('.dh-filters-form').submit(function(event) {
    if ($('input:checked', this).length == 1) {
      // Exactly one category selected, so we go to the page of that specific category
      event.preventDefault();
      window.location.href = $('input:checked', this).attr('alt-url');
    }
    else {
      // More (or less) than one category selected... nothing to do other the normal behaviour of a form
    }
  });
  
  var $buttons = $("label input[type='radio'], label input[type='checkbox']");
  GOVUK.selectionButtons($buttons);

	//basic html5 shim just for the elements we're using
	for(var tags = ['details','summary'], i = 0; i < tags.length; i ++)
	{
		document.createElement(tags[i]);
	}

  $("summary").click(function(e) {
    var details = $(this).parents('details');
    $(details).prop('open', !$(details).prop('open'));
    e.preventDefault();
  });

/* Comments */
	var scrollToComment = function() {
		var hash = window.location.hash;
		if (hash != '') {
			var target = $(hash);
			if ($(target).length == 0) {
				// Do AJAX request to get comments
				
				// First extract the comment id
				var commentId = String(hash).replace('#comment-', '');
				if ( parseInt(commentId) == commentId ) {
					$.ajax({
						url:ajaxUrl,
						type:'POST',
						dataType: 'json',
						data:{
							'action':'get_comments_page',
							'post_id':currentPostId,
							'comment_id':commentId,
							'start_page':currentCommentsPage + 1
						},
						success:function(data){
							$('#main-comments-list').append(data.html);
						
							// A bit hackish
							currentHRef = String($('#comments-load-more-button').attr('href'));
							$('#comments-load-more-button').attr('href', currentHRef.replace('/comment-page-' + data.newPage + '/', '/comment-page-' + (parseInt(data.newPage) + 1) + '/'));
							currentCommentsPage = parseInt(data.newPage);
							if (currentCommentsPage == currentCommentsMaxPage) { $('#comments-load-more-button').hide(); }
							scrollToComment();
						}
					});
				}
			}
			else {
				//target.parent().next().slideDown();
				target.parents('details').attr('open', 'open');
				target.children("div").slideDown();
				var destination = $(target).offset().top;
				$("html:not(:animated),body:not(:animated)").animate({ scrollTop: destination-120}, 500);
			}
		}
	};
	
	scrollToComment();


/* truncated comments */
    var sliced_comment = $('div.comment-body');
    var comment_character_count = 300;    

    sliced_comment.each(function(){    
        var t = $(this).text(); 
        var h = $(this).html();

        /** show truncated comment and add link, 
        *   on click hide the short one and show the lond one */

        if(t.length < comment_character_count ) return;

        $(this).html('<p class="first">' + 
            t.slice(0,comment_character_count )+'<span>... </span><a href="#" class="more">More</a></p>'+
            '<div style="display:none;">'+ h +' <a href="#" class="less">Less</a></div>'
        );
    }); 

    $('a.more', sliced_comment).click(function(event){
        event.preventDefault();
        //hide the short version and show full text
        $(this).parents('p.first').hide();
        $(this).parent().next().show(); 
    });

    $('a.less', sliced_comment).click(function(event){
        event.preventDefault();
        //hide the full version and show first paragraph
        $(this).parent().hide().prev().show();  
        $(this).parents('p.first').show();  
    });




/* External links */
  var currentDomainMatches = /[a-z]+\:\/\/[a-z0-9\.]+/g.exec(String(document.URL));
  $('a').not('.external-no-process').each(function() {
    var matches = /[a-z]+\:\/\/[a-z0-9\.]+/g.exec($(this).attr('href'));
    if ( matches && matches.length > 0 && matches[0] != currentDomainMatches[0] ) {
      // Make all links to a different domain open in a new tab/window
      $(this).attr('target', '_blank');
      
      // One last check to make sure it's not a .GOV.UK domain
      var matchGovUk = /.+\.gov\.uk/g.exec( matches[0] );
      if ( ! matchGovUk || matchGovUk.length == 0 ) {
        $(this).attr('rel', 'external');
      }
    }
  });
  
  $('.block-share-this-page .sharelinks').on('click', function(){
    window.open(this.href, 'doh',	'left=20,top=20,width=600,height=600,toolbar=1,resizable=0'); 
    return false;
  });

  $('#tag_posts-load-more-button').click(function(e) {
    e.preventDefault();
    $.ajax({
      url:ajaxUrl,
      type:'POST',
      dataType: 'json',
      data:{
        'action':'get_tag_posts_page',
        'tag_slug':currentTagSlug,
        'page_num':currentPostsPage + 1
      },
      success:function(data){
        $('#content-tag-main-content').append(data.html);
        
        // A bit hackish
        currentHRef = String($('#tag_posts-load-more-button').attr('href'));
        $('#tag_posts-load-more-button').attr('href', currentHRef.replace('/page/' + data.newPage + '/', '/page/' + (parseInt(data.newPage) + 1) + '/'));
        currentPostsPage = parseInt(data.newPage);
        if (currentPostsPage == maxPostsPage) { $('#tag_posts-load-more-button').hide(); }
      }
    });
  });

  $('#comments-load-more-button').click(function(e) {
    e.preventDefault();
    $.ajax({
      url:ajaxUrl,
      type:'POST',
      dataType: 'json',
      data:{
        'action':'get_comments_page',
        'post_id':currentPostId,
        'page_num':currentCommentsPage + 1
      },
      success:function(data){
        $('#main-comments-list').append(data.html);
        
        // A bit hackish
        currentHRef = String($('#comments-load-more-button').attr('href'));
        $('#comments-load-more-button').attr('href', currentHRef.replace('/comment-page-' + data.newPage + '/', '/comment-page-' + (parseInt(data.newPage) + 1) + '/'));
        currentCommentsPage = parseInt(data.newPage);
        if (currentCommentsPage == currentCommentsMaxPage) { $('#comments-load-more-button').hide(); }
      }
    });
  });
  
  var updateRecommendationsList = function(event) {
    if ( typeof dhAllRecommendations != 'undefined' ) {
      var motherForm = $('#recommendation-filters-form')
      // Clear current <ul> of recommendations
      $('#recommendations-main-list li').remove();
      
      // Get the criteria from the filters form
      var targetText = $('#recommendations-text-search').val();
      
      var targetTopics = [];
      var targetOrganisations = [];

      var targetTopicsLabels = [];
      var targetOrganisationsLabels = [];
   
      $('input[type=checkbox]', motherForm).each(function() {
        if ($(this).is(':checked')) {
          var value = parseInt($(this, motherForm).val());
          var label = $('label[for=' + $(this).attr('id') + '] span', motherForm).text();
          if ($(this).attr('name') == 'themes[]') {
            targetTopics.push(value);
            targetTopicsLabels.push(label);
          }
          else if ($(this).attr('name') == 'organisations[]') {
            targetOrganisations.push(value);
            targetOrganisationsLabels.push(label);
          }
        }
      });
      
      for ( var i in dhAllRecommendations ) {
        // Check if recommendation matches criteria
      
        // First check the text in title
        var passText = true;
        if (targetText != '' && dhAllRecommendations[i].titleInLower.search(targetText.toLowerCase()) == -1) {
          passText = false;
        }
        
        // Now check the topics
        var passTopic = true;
        if (targetTopics.length > 0) {
          passTopic = false;
          for (j in targetTopics) {
            if (dhAllRecommendations[i].topic.indexOf(targetTopics[j]) > -1) {
              passTopic = true;
              break;
            }
          }
        }
        
        // Now check the organisations
        var passOrganisation = true;
        if (targetOrganisations.length > 0) {
            passOrganisation = false;
            for (j in targetOrganisations) {
              if (dhAllRecommendations[i].organisation.indexOf(targetOrganisations[j]) > -1) {
                passOrganisation = true;
                break;
              }
            }
          }
        
        if (passText && passTopic && passOrganisation) {
          // add <li> for this recommendation
          var li = $('<li/>', {}).appendTo('#recommendations-main-list');
          $('<div/>', {'class':'small-text', 'text':'Recommendation ' + dhAllRecommendations[i].recommendationNumber}).appendTo(li);
          $('<a/>', {'href':dhAllRecommendations[i].URL, 'text':dhAllRecommendations[i].title}).appendTo(li);
        }
      }
      
      if ($('#recommendations-main-list li').length == 0) {
        $('<li/>', {'text':'No results match the chosen criteria!'}).appendTo('#recommendations-main-list');
      }
    }
  };
  
  var updateQuestionsList = function(event) {
    if ( typeof dhAllQuestions != 'undefined' ) {
      var motherForm = $('#question-filters-form')
      // Clear current <ul> of recommendations
      $('#questions-main-list li').remove();
      
      // Get the criteria from the filters form
      var targetText = $('#questions-text-search').val();
      
      var targetChapters = [];
      var targetChaptersLabels = [];
   
      $('input[type=checkbox]', motherForm).each(function() {
        if ($(this).is(':checked')) {
          var value = parseInt($(this, motherForm).val());
          var label = $('label[for=' + $(this).attr('id') + '] span', motherForm).text();
          if ($(this).attr('name') == 'chapters[]') {
            targetChapters.push(value);
            targetChaptersLabels.push(label);
          }
        }
      });
      
      for ( var i in dhAllQuestions ) {
        // Check if recommendation matches criteria
      
        // First check the text in title
        var passText = true;
        if (targetText != '' && dhAllQuestions[i].titleInLower.search(targetText.toLowerCase()) == -1) {
          passText = false;
        }
        
        // Now check the chapters
        var passChapters = true;
        if (targetChapters.length > 0) {
            passChapters = false;
            for (j in targetChapters) {
              if (dhAllQuestions[i].chapter.indexOf(targetChapters[j]) > -1) {
                passChapters = true;
                break;
              }
            }
          }
        
        if (passText && passChapters) {
          // add <li> for this question
          var li = $('<li/>', {}).appendTo('#questions-main-list');
          $('<div/>', {'class':'small-text', 'text':'Question ' + dhAllQuestions[i].questionNumber}).appendTo(li);
          $('<a/>', {'href':dhAllQuestions[i].URL, 'text':dhAllQuestions[i].title}).appendTo(li);
        }
      }
      
      if ($('#questions-main-list li').length == 0) {
        $('<li/>', {'text':'No results match the chosen criteria!'}).appendTo('#questions-main-list');
      }
    }
  };
  
  var updatePostsList = function(event) {
    if ( typeof dhAllPosts != 'undefined' ) {
      var motherForm = $('#post-filters-form')
      // Clear current <ul> of recommendations
      $('#posts-main-list li').remove();
      
      // Get the criteria from the filters form
      var targetText = $('#posts-text-search').val();
      
      var targetTags = [];
      var targetTagsLabels = [];
   
      $('input[type=checkbox]', motherForm).each(function() {
        if ($(this).is(':checked')) {
          var value = parseInt($(this, motherForm).val());
          var label = $('label[for=' + $(this).attr('id') + '] span', motherForm).text();
          if ($(this).attr('name') == 'tags[]') {// @TODO
            targetTags.push(value);
            targetTagsLabels.push(label);
          }
        }
      });
      
      for ( var i in dhAllPosts ) {
        // Check if recommendation matches criteria
      
        // First check the text in title
        var passText = true;
        if (targetText != '' && dhAllPosts[i].titleInLower.search(targetText.toLowerCase()) == -1) {
          passText = false;
        }
        
        // Now check the chapters
        var passTags = true;
        if (targetTags.length > 0) {
            passTags = false;
            for (j in targetTags) {
              if (dhAllPosts[i].post_tag.indexOf(targetTags[j]) > -1) {
                passTags = true;
                break;
              }
            }
          }
        
        if (passText && passTags) {
          // add <li> for this question
          var li = $('<li/>', {}).appendTo('#posts-main-list');
          $('<div/>', {'class':'small-text'}).appendTo(li);
          $('<a/>', {'href':dhAllPosts[i].URL, 'text':dhAllPosts[i].title}).appendTo(li);
          $('<div/>', {'class':'text-thin download-size', 'text':'Date: ' + dhAllPosts[i].postDate}).appendTo(li);
        }
      }
      
      if ($('#posts-main-list li').length == 0) {
        $('<li/>', {'text':'No results match the chosen criteria!'}).appendTo('#posts-main-list');
      }
    }
  };

  $('#recommendation-filters-form input').change(updateRecommendationsList);
  $('#question-filters-form input').change(updateQuestionsList);
  $('#post-filters-form input').change(updatePostsList);
  $('#quick-text-filter').show();
  $('#quick-text-filter input#recommendations-text-search').keyup(updateRecommendationsList);
  $('#quick-text-filter input#questions-text-search').keyup(updateQuestionsList);
  $('#quick-text-filter input#posts-text-search').keyup(updatePostsList);


  $('#newsletter').on('click', function(e){
      e.preventDefault();
      $('#newsletterform').slideToggle();
    });

  $('.close').on('click', function(e){
    e.preventDefault();
    $('#newsletterform').slideUp();
  });


  if ( !("placeholder" in document.createElement("input")) ) {
    $("input[placeholder], textarea[placeholder]").each(function() {
      var val = $(this).attr("placeholder");
      if ( this.value == "" ) {
        this.value = val;
      }
      $(this).focus(function() {
        if ( this.value == val ) {
          this.value = "";
        }
      }).blur(function() {
        if ( $.trim(this.value) == "" ) {
          this.value = val;
        }
      })
    });

    // Clear default placeholder values on form submit
    $('form').submit(function() {
            $(this).find("input[placeholder], textarea[placeholder]").each(function() {
                if ( this.value == $(this).attr("placeholder") ) {
                    this.value = "";
                }
            });
        });
  }


})(jQuery);