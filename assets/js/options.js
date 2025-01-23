jQuery(document).ready(function($) {
  const mediamanager = new Mediamanager({
    loadItemsUrl: window.location.origin + '/wp-content/themes/wp98/assets/mediamanager/loadfiles.json',
    uploadUrl: '/'
  });

  // Menu rows and items manager
  const menuRows = $('.menu-row');
  $.each(menuRows, function(keys, row) {
    // Hidden input elements
    const actionInputEl = $(row.children[0]);
    const orderInputEl = $(row.children[1]);
    const pageIdInputEl = $(row.children[2]);
    const iconInputEl = $(row.children[3]);
    const pageNameInputEl = $(row.children[4]);
    const headerInputEl = $(row.children[5]);
    const footerInputEl = $(row.children[6]);

    // Icon elements
    const dragEl = $(row.children[7]);
    const noImgEl = $(row.children[8].children[0]);
    const imgEl = $(row.children[8].children[1]);
    const originalImgUrl = iconInputEl.val() !== '' ?
      iconInputEl.val() :
      '';
    
    // Page drop down elements
    const pageSelEl = $(row.children[9].children[0]);
    const headerSelEl = $(row.children[10].children[0]);
    const footerSelEl = $(row.children[11].children[0]);
    const originalPageId = pageSelEl.val();
    const originalRow = Array.from(row.parentNode.children).indexOf(row);
  
    let btnsCon, changeBtn, removeBtn, chooseBtn;

    // Page selection dropdown event listener
    pageSelEl.change(updateInputValueState);

    // Add event listeners if the row already contains a selected icon
    if (imgEl[0].nextElementSibling.type === undefined) {
      btnsCon = $(row.children[8].children[2]);
      
      changeBtn = $(btnsCon[0].children[0]);
      removeBtn = $(btnsCon[0].children[1]);
      
      addChangeIconEventListener(changeBtn);

      addRemoveIconEventListener(removeBtn);
    }
    // Add event listeners if the row doesn't contain a selected icon
    else {
      chooseBtn = $(row.children[8].children[2]);

      addChooseIconEventListener(chooseBtn);
    }

    // Event listener for the "change" button
    function addChangeIconEventListener(element) {
      $(element).click(function(e) {
        e.stopPropagation();
        mediamanager.open();
        mediamanager.options.insertType = 'html';
        mediamanager.options.insert = chosenImg => {
          iconInputEl.val(chosenImg.src);
          imgEl.attr('src', chosenImg.src);
          imgEl.css('display', 'inline-block');
          noImgEl.css('display', 'none');

          updateInputValueState();
        }
      });
    }

    // Event listener for the "remove" button
    function addRemoveIconEventListener(element) {
      $(element).click(function(e) {
        e.stopPropagation();
        noImgEl.css('display', 'inline-flex');
        imgEl.attr('src', '');
        imgEl.css('display', 'none');

        // Remove the old buttons and add a "choose" button
        iconInputEl.val('');
        btnsCon.remove();
        chooseBtn = $('<button/>', {
          type: 'button',
          class: 'mediamanager-btn add-button button button-secondary'
        }).html('Choose<br>Icon').appendTo(row.children[8]);
        
        addChooseIconEventListener(chooseBtn);

        updateInputValueState();
      });
    }

    // Event listener for the "Choose" button
    function addChooseIconEventListener(element) {
      $(element).click(function(e) {
        e.stopPropagation();
        mediamanager.open();
        mediamanager.options.insertType = 'html';
        mediamanager.options.insert = chosenImg => {
          iconInputEl.attr('value', chosenImg.src);
          imgEl.attr('src', chosenImg.src);
          imgEl.css('display', 'inline-block');
          noImgEl.css('display', 'none');

          // Remove the old buttons and add a "choose" button
          chooseBtn.remove();

          btnsCon = $('<div/>', { class: 'menu-btn-flex-container'})
            .appendTo(row.children[8]);
          
          changeBtn = $('<button/>', {
            type: 'button',
            class: 'mediamanager-btn button button-secondary'
          }).text('Change').appendTo(btnsCon);
          addChangeIconEventListener(changeBtn);

          removeBtn = $('<button/>', {
            type: 'button',
            class: 'icon-remove-btn button button-secondary'
          }).text('Remove').appendTo(btnsCon);
          addRemoveIconEventListener(removeBtn);

          updateInputValueState();
        }
      });
    }

    // Update the values to be returned to the php page when the form is submitted
    function updateInputValueState() {
      if (pageSelEl.val() === 'none') {
        actionInputEl.val('delete');
        pageIdInputEl.val(originalPageId);
        pageNameInputEl.val('');
        headerInputEl.val('');
        footerInputEl.val('');
      }
      else if (pageSelEl.val() === originalPageId && iconInputEl.val() === originalImgUrl) {
        actionInputEl.val('no-change');
        pageIdInputEl.val(originalPageId);
        pageNameInputEl.val(pageSelEl[0][pageSelEl[0].selectedIndex].text);
        headerInputEl.val(headerSelEl.val());
        footerInputEl.val(footerSelEl.val());
      }
      else {
        actionInputEl.val('update');
        pageIdInputEl.val(pageSelEl.val());
        pageNameInputEl.val(pageSelEl[0][pageSelEl[0].selectedIndex].text);
        headerInputEl.val(headerSelEl.val());
        footerInputEl.val(footerSelEl.val());
      }

      // Row number in the table
      const rowNo = Array.from(row.parentNode.children).indexOf(row);
      orderInputEl.val(rowNo - 1);
    }
  });

  const checkboxes = $('input[type="checkbox"]');
  $.each(checkboxes, function(keys, checkbox) {
    const stateInputEl = $(checkbox).prev();
    
    $(checkbox).change(() => {
      if (checkbox.checked) stateInputEl.val(1);
      else stateInputEl.val(0);
    })
  });
});