// Page Manager self-executing anonymous function
const pm = (function($){
  let pages = [];
  let history = [];
  let histStep = 0;

  // Window Movement
  let isWindowBeingMoved = false;
  let windowOffset, mousePos;
  let pageBeingMoved;

  // Window Maximise
  let isWindowMaximised = false;
  let minimisedSize;

  function addHistory(id, link) {

  }

  function addPage(id) {
    const page = $(`#wp98-page-${id}`);
    pages.push(page);
    _repurposeLinks(pages[pages.length - 1]);
    _registerEventListeners(pages[pages.length - 1]);
    page.on('click', {id: id}, focusPage)    ;
    focusPage(id);
  }

  function focusPage(id) {
    if (typeof id === 'object') {
      id = id.data.id;
      op.selectTab(id);
    }
    pages.forEach(page => {
      if (page.attr('id') === `wp98-page-${id}`) {
        page.css('z-index', 900);
        page.css('display', 'block');
      }
      else page.css('z-index', 1);
    });
  }

  function _removePage(id) {
    pages.forEach((page, index, pages) => {
      if (page.attr('id') === `wp98-page-${id}`) {
        pages.splice(index, 1);
        page.remove();
        op.removeTab(id);
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
      url: `/wp-content/themes/wp98/templates/${type}.php?id=${id}`,
      success: content => {
        pageContent.html(content);
        _repurposeLinks(page);
      }
    });
  }

  $(document).on({
    // Deregister page windows from being moved when the mouse button is lifted
    mouseup: () => { isWindowBeingMoved = false; },
    // Track window movement to keep movement consistent when mouse is moved off element being moved
    mousemove: _moveWindow,
  });


  function _registerEventListeners(page) {
    // Window movement
    const titleBar = $(`#${page.prop('id')} .title-bar`);
    titleBar.on('mousedown', {page: page}, _moveWindow);

    // Window resizing
    page.click((e) => {
      if ($(e.target).hasClass('wp98-page') === true && e.button === 0) {
        const pageOffset = $(e.target).offset();
        const pageSize = {
          x: e.target.clientWidth,
          y: e.target.clientHeight
        };
        const mousePos = {
          x: e.originalEvent.clientX - pageOffset.left,
          y: e.originalEvent.clientY - pageOffset.top
        };
      }
    })

    const minimiseBtn = $(`#${page.prop('id')} button[aria-label="Minimize"]`);
    minimiseBtn.click(function() { _minimiseWindow(page) });
    
    const maximiseBtn = $(`#${page.prop('id')} button[aria-label="Maximize"]`);
    maximiseBtn.click(function() { _maximiseWindow(page) });

    const closeBtn = $(`#${page.prop('id')} button[aria-label="Close"]`);
    closeBtn.click((e) => {
      const idArr = $(e.target).closest('.wp98-page').prop('id').split('-');
      const id = idArr[idArr.length - 1];
      _removePage(id);
    });

    const navBackBtn = $(`#${page.prop('id')} .navigation-back`);
    navBackBtn.on('click', _backHistory);
  }

  function _minimiseWindow(page) {
    event.stopPropagation();
    page.css('display', 'none');
    op.selectTab('');
  }

  function _maximiseWindow(page) {
    if (isWindowMaximised === true) {
      page.css({
        width: minimisedSize.width,
        height: minimisedSize.height,
        top: minimisedSize.top,
        left: minimisedSize.left
      });
      
      isWindowMaximised = false;
    }
    else {
      minimisedSize = {
        width: page.css('width'),
        height: page.css('height'),
        top: page.css('top'),
        left: page.css('left')
      };

      page.css({
        width: `${window.innerWidth}px`,
        height: `${window.innerHeight - $('#wp98-taskbar').height() - ($('#wpadminbar').length !== 0 ? $('#wpadminbar').height() : 0)}px`,
        top: $('#wpadminbar').length !== 0 ? $('#wpadminbar').height() : 0,
        left: 0
      });

      isWindowMaximised = true;
    }
  }

  function _moveWindow(e) {
    if ($(e.target).hasClass('title-bar') === true && e.type === 'mousedown' && e.button === 0) {
      e.preventDefault();

      const idArr = e.target.parentElement.id.split('-');
      const id = idArr[idArr.length - 1];
      focusPage(id);

      isWindowBeingMoved = true;
      mousePos = { x: e.originalEvent.clientX, y: e.originalEvent.clientY };
      windowOffset = { x: $(e.data.page).offset().left - mousePos.x, y: $(e.data.page).offset().top - mousePos.y };
      pageBeingMoved = e.data.page;
    }
    else if (e.type === 'mousemove' && isWindowBeingMoved === true) {
      $(pageBeingMoved).css('left', `${e.originalEvent.clientX + windowOffset.x}px`);
      $(pageBeingMoved).css('top', `${e.originalEvent.clientY + windowOffset.y}px`);
    }
  }

  function _backHistory() {

  }
  
  return {
    addHistory: (id, link) => addHistory(id, link),
    addPage: (id) => addPage(id),
    focusPage: (id) => focusPage(id)
  };
})(jQuery);