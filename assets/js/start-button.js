const sb = (function($) {
  const startBtn = $('#wp98-start-button');
  const startMenu = $('#wp98-start-menu')
  const menuTitleBar = $('#wp98-start-menu .title-bar');
  const menuItems = $('#wp98-start-menu .page-item');
  const taskbar = $('#wp98-taskbar');

  let isStartMenuOpen = false;

  // Show or hide the start menu on start button click
  startBtn.click(showHideStartMenu);

  // Document event listener for checking if clicks occur outside the start menu when opened
  $(document).click(function(e) {
    if (isStartMenuOpen === true) {
      // Check if the click has occurred outside start menu and start button
      if ($(e.target).closest('#wp98-start-button').length === 0 && $(e.target).closest('#wp98-start-menu').length === 0)
        showHideStartMenu();
      op.selectTab('');
    }
  });

  // Mouse item hover on menu items
  $('#wp98-start-menu .nav-list').on({
    mouseover: function(e) {
      const el = e.target;
      if (el.tagName === 'LI') $(el).addClass('menu-hover');
      else if (el.tagName === 'IMG' || el.tagName === 'SPAN') $(el.parentElement).addClass('menu-hover');
    },
    mouseout: function(e) {
      const el = e.target;
      if (el.tagName === 'LI') $(el).removeClass('menu-hover');
      else if (el.tagName === 'IMG' || el.tagName === 'SPAN') $(el.parentElement).removeClass('menu-hover');
    }
  })

  // Menu item event listener and handling
  $.each(menuItems, function(key, item) {
    $(item).click( function() {
      const id = $(item).data('id');

      // Return if this menu item has already been clicked and created a new element on the page
      if ($(`#wp98-page-${id}`).length !== 0) {
        showHideStartMenu();
        pm.focusPage(id);
        op.selectTab(id);
        return;
      }
      
      // Create the page container
      const page = $('<div/>',{
        id: `wp98-page-${id}`,
        class: 'wp98-page window',
        'data-id': id
      }).appendTo('body');

      // Fetch the page content
      $.ajax({
        url: `/wp-content/themes/wp98/templates/window.php?id=${id}`,
        success: content => {
          page.html(content);
          pm.addPage(id, 'page');
          op.addTab(id);
        }
      });

      showHideStartMenu();
      page.focus();
    });
  });

  function showHideStartMenu() {
    if (isStartMenuOpen === true) {
      startMenu.css('display', 'none');
      startBtn.blur();
    }
    else startMenu.css('display', 'flex');

    // Set the width of titleBar to fix render issue in Firefox
    if (menuTitleBar !== null) menuTitleBar.css('width', `${menuTitleBar[0].children[0].clientWidth}px`); 
    startMenu.css('bottom', `${taskbar[0].clientHeight + 1}px`);

    isStartMenuOpen ? isStartMenuOpen = false : isStartMenuOpen = true;
  }
})(jQuery);