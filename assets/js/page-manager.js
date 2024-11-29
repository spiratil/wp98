// Page Manager self-executing anonymous function
const pm = (function($){
  let pages = {};

  // Window Movement
  let isWindowBeingMoved = false, isWindowBeingResized = false;
  let windowOffset, windowSize, mousePos;
  let pageBeingManipulated;

  // Window Maximise
  let isWindowMaximised = false;
  let minimisedSize;

  function addHistory(id, link) {

  }

  function addPage(id) {
    const page = $(`#wp98-page-${id}`);
    pages[id] = {
      page: page,
      history: [],
      histStep: 0
    };
    _repurposeLinks(id);
    _registerEventListeners(id);
    page.on('click', {id: id}, focusPage)    ;
    focusPage(id);
  }

  function focusPage(id) {
    if (typeof id === 'object') {
      id = id.data.id;
      op.selectTab(id);
    }
    for (const key in pages) {
      if (key === id) {
        pages[key].page.css('z-index', 900);
        pages[key].page.css('display', 'flex');
      }
      else pages[key].page.css('z-index', 1);
    }
  }

  function _removePage(id) {
    for (const [key, page] of Object.entries(pages)) {
      if (key === id) {
        pages[id].page.remove();
        op.removeTab(id);
        delete pages[key];
        return;
      }
    }
  }

  // Prevent <a> links with hrefs to the site domain from activating and instead load
  // the content of the requested page within the container on the homepage
  function _repurposeLinks(id) {
    // Fetch and assign an event listener to every <a> link on the page
    const links = $(`#${pages[id].page.attr('id')} a`);

    // Add a click event listener to each required link
    links.each(function() {
      if (this.href.startsWith(document.URL)) {
        $(this).click(function(e) {
          e.preventDefault();

          _loadPageContent(pages[id].page, $(this).data('type'), $(this).data('id'));
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
    // Deregister page windows from being moved/resized when the mouse button is lifted
    mouseup: () => {
      isWindowBeingMoved = false;
      isWindowBeingResized = false;
    },
    // Track window movement to keep movement consistent when mouse is moved off element being moved
    mousemove: (e) => {
      if (isWindowBeingMoved) _moveWindow(e);
      else if (isWindowBeingResized) _resizeWindow(e);
    }
  });


  function _registerEventListeners(id) {
    // Window movement
    const titleBar = $(`#${pages[id].page.prop('id')} .title-bar`);
    titleBar.on('mousedown', {page: pages[id].page}, _moveWindow);

    // Window resizing
    $(`#corner-grabber-${id}`).on('mousedown', _resizeWindow);

    const minimiseBtn = $(`#${pages[id].page.prop('id')} button[aria-label="Minimize"]`);
    minimiseBtn.click(function() { _minimiseWindow(id) });
    
    const maximiseBtn = $(`#${pages[id].page.prop('id')} button[aria-label="Maximize"]`);
    maximiseBtn.click(function() { _maximiseWindow(id) });

    const closeBtn = $(`#${pages[id].page.prop('id')} button[aria-label="Close"]`);
    closeBtn.click((e) => {
      const idArr = $(e.target).closest('.wp98-page').prop('id').split('-');
      const id = idArr[idArr.length - 1];
      _removePage(id);
    });

    const navBackBtn = $(`#${pages[id].page.prop('id')} .navigation-back`);
    navBackBtn.on('mousedown', _backHistory);
  }

  function _minimiseWindow(id) {
    event.stopPropagation();
    pages[id].page.css('display', 'none');
    op.selectTab('');
  }

  function _maximiseWindow(id) {
    if (isWindowMaximised === true) {
      pages[id].page.css({
        width: minimisedSize.width,
        height: minimisedSize.height,
        top: minimisedSize.top,
        left: minimisedSize.left
      });
      
      isWindowMaximised = false;
    }
    else {
      minimisedSize = {
        width: pages[id].page.css('width'),
        height: pages[id].page.css('height'),
        top: pages[id].page.css('top'),
        left: pages[id].page.css('left')
      };

      pages[id].page.css({
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

      const id = $(e.target).data('id');
      focusPage(id);

      isWindowBeingMoved = true;
      mousePos = { x: e.originalEvent.clientX, y: e.originalEvent.clientY };
      windowOffset = { x: $(e.data.page).offset().left - mousePos.x, y: $(e.data.page).offset().top - mousePos.y };
      pageBeingManipulated = pages[id].page;
    }
    else if (e.type === 'mousemove' && isWindowBeingMoved === true) {
      $(pageBeingManipulated).css('left', `${e.originalEvent.clientX + windowOffset.x}px`);
      $(pageBeingManipulated).css('top', `${e.originalEvent.clientY + windowOffset.y}px`);
    }
  }

  function _resizeWindow(e) {
    if ($(e.target).hasClass('corner-grabber') === true && e.type === 'mousedown' && e.button === 0) {
      e.preventDefault();

      const id = $(e.target).data('id');
      focusPage(id);

      isWindowBeingResized = true;
      pageBeingManipulated = pages[id].page;
      mousePos = { x: e.originalEvent.clientX, y: e.originalEvent.clientY };
      windowSize = { width: pageBeingManipulated.width(), height: pageBeingManipulated.height()}
    }
    else if (e.type === 'mousemove' && isWindowBeingResized === true) {
      const mouseOffset = { x: e.originalEvent.clientX - mousePos.x, y: e.originalEvent.clientY - mousePos.y }
      pageBeingManipulated.width(windowSize.width + mouseOffset.x);
      pageBeingManipulated.height(windowSize.height + mouseOffset.y);
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