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
    },
    ajaxError: function(e, xhr, options, exc) { 
      _loadPageError(xhr.status);
      // Remove the added history for the page that couldn't be loaded
      const id = $(e.target.activeElement).closest('.wp98-page').data('id');
      pages[id].history.toSpliced(0, 1);
    }
  });

  function addPage(id, type) {
    const page = $(`#wp98-page-${id}`);
    pages[id] = {
      page: page,
      content: page.find('.wp98-content'),
      home: type,
      history: [],
      histStep: 0,
      events: null
    };
    _repurposeLinks(id);
    pages[id].events = _registerEventListeners(id);
    page.on('click', {id: id}, focusPage);
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

  function _addHistory(winId, type, pageId) {
    // Clear the forward history
    if (pages[winId].histStep !== 0 && pages[winId].histStep === pages[winId].history.length) {
      pages[winId].history = [{ type: type, id: Number(pageId) }];
    }
    else {
      pages[winId].history = pages[winId].history.toSpliced(0, pages[winId].histStep);
      pages[winId].history.unshift({ type: type, id: Number(pageId) });
    }
    pages[winId].histStep = 0;  

    _updateNavButtons(winId);

    console.log(pages[winId].histStep, pages[winId].history)
  }

  // Prevent <a> links with hrefs to the site domain from activating and instead load
  // the content of the requested page within the container on the homepage
  function _repurposeLinks(id) {
    // Fetch and assign an event listener to every <a> link on the page
    const links = pages[id].page.find('a');

    // Add a click event listener to each required link
    links.each(function() {
      if (this.href.startsWith(document.URL)) {
        $(this).click(function(e) {
          e.preventDefault();

          _loadPageContent(id, $(this).data('type'), $(this).data('id'));
          $(`#url-display-${id} img`).attr('src', '');
          $(`#url-display-${id} span`).text('');
        });
      }

      // Display the link URL in the status bar
      $(this).mouseenter(function() {
        $(`#url-display-${id} img`).attr('src', 'https://wordpress.ddev.site/wp-content/themes/wp98/assets/images/icons/html2-1.png');
        $(`#url-display-${id} span`).text($(this).attr('href'));
      });

      // Hide the link URL in the status bar when the mouse moves away
      $(this).mouseleave(function() {
        $(`#url-display-${id} img`).attr('src', '');
        $(`#url-display-${id} span`).text('');
      });
    });
  }

  // Loads the requested page content inside the page container
  function _loadPageContent(winId, type, pageId, isHistory = false) {
    $.ajax({
      url: `/wp-content/themes/wp98/templates/${type}.php?id=${pageId}`,
      success: content => {
        pages[winId].content.html(content);
        _repurposeLinks(winId);
      },
    });

    if (isHistory === false) _addHistory(winId, type, pageId);
  }

  function _loadPageError(status) {
    $.ajax({
      url: `/wp-content/themes/wp98/templates/404.php?status=${status}`,
      success: content => {
        $('body').append(content);

        // Prevent the user from interacting with the page until the error message is closed
        const pageWrapper = $('.wp98-error-window');
        pageWrapper.click((e) => {
          e.preventDefault();
        })
        
        const closeBtn = $('.wp98-error-window .close-button');
        closeBtn.click(() => { $('.wp98-error-window').remove(); });
      }
    });
  }


  function _registerEventListeners(id) {
    // Window movement
    const titleBar = pages[id].page.find('.title-bar');
    titleBar.on('mousedown', {page: pages[id].page}, _moveWindow);

    // Window resizing
    $(`#corner-grabber-${id}`).on('mousedown', _resizeWindow);

    const minimiseBtn = pages[id].page.find('button[aria-label="Minimize"]');
    minimiseBtn.click(function() { _minimiseWindow(id); });
    
    const maximiseBtn = pages[id].page.find('button[aria-label="Maximize"]');
    maximiseBtn.click(function() { _maximiseWindow(id); });

    const closeBtn = pages[id].page.find('button[aria-label="Close"]');
    closeBtn.click(function() { _removePage(id); });
    
    const navBackBtn = pages[id].page.find('.navigation-back');
    navBackBtn.click(function() { _backHistory(id); });

    const navForwardBtn = pages[id].page.find('.navigation-forward');
    navForwardBtn.click(function() { _forwardHistory(id); });

    const navRefreshBtn = pages[id].page.find('.navigation-refresh');
    navRefreshBtn.click(function() { 
      if (pages[id].history.length === 0) _loadHomePage(id);
      else _refreshPage(id);
    });

    const navHomeBtn = pages[id].page.find('.navigation-home');
    navHomeBtn.click(function() { _loadHomePage(id, pages[id].home.type, id); });

    return {
      titleBar: titleBar,
      minimiseBtn: minimiseBtn,
      maximiseBtn: maximiseBtn,
      closeBtn: closeBtn,
      navBackBtn: navBackBtn,
      navForwardBtn: navForwardBtn,
    };
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

  function _backHistory(id) {
    if (pages[id].histStep == pages[id].history.length) return;
    const step = ++pages[id].histStep;
    console.log(step)
    if (pages[id].histStep == pages[id].history.length)
      _loadPageContent(id, pages[id].home, id, true);  
    else
      _loadPageContent(id, pages[id].history[step].type, pages[id].history[step].id, true);

    _updateNavButtons(id);
  }

  function _forwardHistory(id) {
    if (pages[id].histStep == 0) return;
    const step = --pages[id].histStep;
    console.log(step)
    _loadPageContent(id, pages[id].history[step].type, pages[id].history[step].id, true);

    _updateNavButtons(id);
  }

  function _updateNavButtons(id) {
    if (pages[id].histStep == pages[id].history.length) {
      pages[id].events.navBackBtn.addClass('navigation-disabled');
      pages[id].events.navBackBtn.find('img').attr('src', 'https://wordpress.ddev.site/wp-content/themes/wp98/assets/images/navigation/back-greyscale.png');
      pages[id].events.navForwardBtn.removeClass('navigation-disabled');
      pages[id].events.navForwardBtn.find('img').attr('src', 'https://wordpress.ddev.site/wp-content/themes/wp98/assets/images/navigation/forward.png');
    }
    else if (pages[id].histStep > 0 && pages[id].histStep < pages[id].history.length) {
      pages[id].events.navBackBtn.removeClass('navigation-disabled');
      pages[id].events.navBackBtn.find('img').attr('src', 'https://wordpress.ddev.site/wp-content/themes/wp98/assets/images/navigation/back.png');
      pages[id].events.navForwardBtn.removeClass('navigation-disabled');
      pages[id].events.navForwardBtn.find('img').attr('src', 'https://wordpress.ddev.site/wp-content/themes/wp98/assets/images/navigation/forward.png');
    }
    else if (pages[id].histStep == 0 && pages[id].history.length > 0) {
      pages[id].events.navBackBtn.removeClass('navigation-disabled');
      pages[id].events.navBackBtn.find('img').attr('src', 'https://wordpress.ddev.site/wp-content/themes/wp98/assets/images/navigation/back.png');
      pages[id].events.navForwardBtn.addClass('navigation-disabled');
      pages[id].events.navForwardBtn.find('img').attr('src', 'https://wordpress.ddev.site/wp-content/themes/wp98/assets/images/navigation/forward-greyscale.png');
    }
  }

  // A delay of 200 ms is applied as pages load too quickly for the user to see
  function _loadHomePage(id) {
    pages[id].content.html('');
    // Save to history if loading the homepage from a different page
    if (pages[id].history.length > 0 && pages[id].history[pages[id].histStep].id !== Number(id)) 
      setTimeout(_loadPageContent, 200, id, pages[id].home, id, false);
    // Refresh the homepage if already on the homepage and don't add to history
    else 
      setTimeout(_loadPageContent, 200, id, pages[id].home, id, true);
  }

  // A delay of 200 ms is applied as pages load too quickly for the user to see
  function _refreshPage(id) {
    pages[id].content.html('');
    setTimeout(_loadPageContent, 200, id, pages[id].history[pages[id].histStep].type, pages[id].history[pages[id].histStep].id, true);
  }
  
  return {
    addPage: (id, type) => addPage(id, type),
    focusPage: (id) => focusPage(id)
  };
})(jQuery);