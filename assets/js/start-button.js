jQuery(document).ready(function($) {
  const startBtn = $('#wp98-start-button');
  const startMenu = $('#wp98-start-menu')
  const menuTitleBar = $('#wp98-start-menu .title-bar');
  const menuItems = $('#wp98-start-menu .page-item');
  const taskbar = $('#wp98-taskbar');

  // Show or hide the start menu on start button click
  startBtn.click(showHideStartMenu);

  // Mouse item hover enter
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
      const id = item.getAttribute('data-id');

      // Return if this menu item has already been clicked and created a new element on the page
      if ($(`#wp98-page-${id}`).length !== 0) return;
      
      // Create the page container
      const page = $('<div/>',{
        id: `wp98-page-${id}`,
        class: 'wp98-page window'
      }).appendTo('body');

      // Fetch the page content
      $.ajax({
        url: `/wp-content/themes/wp98/templates/page.php?id=${id}&buildpage=1`,
        success: content => {
          page.html(content);
          pm.addPage(id);
        }
      });
    });
  });

  function showHideStartMenu() {
    startMenu.css('display') === 'flex' ? startMenu.css('display', 'none') : startMenu.css('display', 'flex')
    // Set the width of titleBar to fix render issue in Firefox
    if (menuTitleBar !== null) menuTitleBar.css('width', `${menuTitleBar[0].children[0].clientWidth}px`); 
    startMenu.css('bottom', `${taskbar[0].clientHeight + 1}px`);
  }
});