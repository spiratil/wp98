const op = (function($) {
  let tabs = [];

  const container = $('#wp98-open-programs');

  function addTab(id) {
    const tab = $('<button/>',{
      id: `wp98-tab-${id}`,
      class: "wp98-tabs"
    }).appendTo(container);

    // Get the image and title from the start menu
    $('<img/>', { src: $(`#wp98-menu-${id} img`).attr('src') }).appendTo(tab);
    $('<span/>').text($(`#wp98-menu-${id} span`).text()).appendTo(tab);

    tabs.push(tab);

    tab.on('click',{id: id}, selectTab);

    selectTab(id);
  }

  function removeTab(id) {
    const tab = $(`#wp98-tab-${id}`);

    tabs.forEach((tab, index, tabs) => {
      if (tab.attr('id') === `#wp98-tab-${id}`) tabs.splice(index, 1);
    });

    tab.remove();
  }

  function selectTab(id) {
    if (typeof id === 'object') {
      id = id.data.id;
      pm.focusPage(id);
    }
    tabs.forEach(tab => {
      if (tab.attr('id') === `wp98-tab-${id}`) tab.addClass('wp98-tab-selected');
      else tab.removeClass('wp98-tab-selected');
    });
  }

  return {
    addTab: (id) => addTab(id),
    removeTab: (id) => removeTab(id),
    selectTab: (id) => selectTab(id)
  }
})(jQuery);