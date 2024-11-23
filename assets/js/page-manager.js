// Page Manager self-executing anonymous function
const pm = (function($){
  let pages = [];

  function addPage(id) {
    pages.push($(`#wp98-page-${id}`));
    _repurposeLinks(pages[pages.length - 1]);
  }

  function removePage(id) {
    pages.forEach((page, index, pages) => {
      if (page.attr('id') === `wp98-page-${id}`) {
        pages.splice(index, 1);
        return;
      }
    })
  }

  // Prevent <a> links with hrefs to the site domain from activating and instead load
  // the content of the requested page within the container on the homepage
  function _repurposeLinks(page) {
    // Fetch and assign an event listener to every <a> link on the page
    const links = $(`#${page.attr('id')} a`);

    // Add a click event listener to each required link
    links.each(function() {
      if (this.href.startsWith(document.URL)) {
        $(this).click(function(e) {
          e.preventDefault();

          _loadPageContent(page, $(this).data('type'), $(this).data('id'));
        });
      }
    });
  }

  // Loads the requested page content inside the page container
  function _loadPageContent(page, type, id) {
    // The content area of the page container
    const pageContent = $(`#${page.attr('id')} .wp98-content`);
    
    $.ajax({
      url: `/wp-content/themes/wp98/templates/page.php?id=${id}`,
      success: content => {
        pageContent.html(content);
        _repurposeLinks(page);
      }
    });
  }
  
  return {
    addPage: (id) => addPage(id),
    removePage: (id) => removePage(id)
  };
  
  
  $(document).ready(function($) {
    console.log('woot')
  });

   
    /*
    // Get the element for the page loaded  
    const page = $('.wp98-page').last();
    
    // Fetch and assign an event listener to every <a> link on the page
    let links = $(`#${page.attr('id')} a`);
    
    $.each(links, function(keys, link) {
      console.log(link)
      if (link.href.startsWith(document.URL)) {
        $(link).click(function(e) {
          e.preventDefault();

          // Fetch the page content
          $.ajax({
            url: `/wp-content/themes/wp98/templates/page.php?id=${id}`,
            success: content => {
              page.html(content);
              page.append(`<script id="wp98-page-${id}-js" type="text/javascript" src="${document.URL}wp-content/themes/wp98/assets/js/page.js""></script>`);
            }
          });
        });
      }
      
    });
      
    
    
    
    
    /* AJAX
    .on('click', '.some-element', function(e){
      var ipc = jQuery(this).data('collection-id');
      jQuery('.some-other-element').show();
    
      jQuery.ajax({
          method: 'post',
          url: ipAjaxVar.ajaxurl,
          data: {
              collection_id: ipc,
              action: 'my_function',
          }
      }).done(function(msg) {
          // Do something when done
      });
    
      e.preventDefault();
    });
    */
})(jQuery);