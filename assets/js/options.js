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
    const imgInputEl = $(row.children[3]);
    const nameInputEl = $(row.children[4]);
    const headInputEl = $(row.children[5]);
    const footInputEl = $(row.children[6]);
    const styleInputEl = $(row.children[7]);

    // Icon elements
    const dragEl = $(row.children[8]);
    const menuCon = $(row.children[9]);
    const noImgEl = $(menuCon[0].children[0]);
    const imgEl = $(menuCon[0].children[1]);
    const originalImgUrl = imgInputEl.val() !== '' ?
      imgInputEl.val() :
      '';
    
    // Page drop down elements
    const pageSelEl = $(row.children[10].children[0]);
    const headSelEl = $(row.children[11].children[0]);
    const footSelEl = $(row.children[12].children[0]);
    const originalPageId = pageSelEl.val();
    const originalHeader = headSelEl.val();
    const originalFooter = footSelEl.val();
    const originalRow = Array.from(row.parentNode.children).indexOf(row);

    // Checkboxes
    const styleCheckEl = $(row.children[13].children[0]);
    const originalStyleState = styleCheckEl.prop('checked');
    // Buttons
    const delBtn = $(row.children[14].children[0]);
    let btnsCon, changeBtn, removeBtn, chooseBtn;

    // Page selection dropdown event listener
    pageSelEl.change(updateInputValueState);
    headSelEl.change(updateInputValueState);
    footSelEl.change(updateInputValueState);
    styleCheckEl.change(updateInputValueState);

    // Add event listeners if the row already contains a selected icon
    if (imgEl[0].nextElementSibling.type === undefined) {
      btnsCon = $(menuCon[0].children[2]);
      
      changeBtn = $(btnsCon[0].children[0]);
      removeBtn = $(btnsCon[0].children[1]);
      
      addChangeIconEventListener(changeBtn);

      addRemoveIconEventListener(removeBtn);
    }
    // Add event listeners if the row doesn't contain a selected icon
    else {
      chooseBtn = $(menuCon[0].children[2]);

      console.log(chooseBtn)

      addChooseIconEventListener(chooseBtn);
    }

    // Event listener for the "change" button
    function addChangeIconEventListener(element) {
      $(element).click(function(e) {
        e.stopPropagation();
        mediamanager.open();
        mediamanager.options.insertType = 'html';
        mediamanager.options.insert = chosenImg => {
          imgInputEl.val(chosenImg.src);
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
        imgInputEl.val('');
        btnsCon.remove();
        chooseBtn = $('<button/>', {
          type: 'button',
          class: 'mediamanager-btn add-button button button-secondary'
        }).html('Choose<br>Icon').appendTo(menuCon[0]);
        
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
          imgInputEl.attr('value', chosenImg.src);
          imgEl.attr('src', chosenImg.src);
          imgEl.css('display', 'inline-block');
          noImgEl.css('display', 'none');

          // Remove the old buttons and add a "choose" button
          chooseBtn.remove();

          btnsCon = $('<div/>', { class: 'menu-btn-flex-container'})
            .appendTo(menuCon[0]);
          
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
        nameInputEl.val('');
        headInputEl.val('');
        headSelEl.prop('selectedIndex', 0);
        footInputEl.val('');
        footSelEl.prop('selectedIndex', 0);
        styleInputEl.val(0);
        styleCheckEl.prop('checked', false); 
      }
      else if (
        pageSelEl.val() === originalPageId
        && imgInputEl.val() === originalImgUrl
        && headSelEl.val() === originalHeader
        && footSelEl.val() === originalFooter
        && styleCheckEl.prop('checked') === originalStyleState
      ) {
        actionInputEl.val('no-change');
        pageIdInputEl.val(originalPageId);
        nameInputEl.val(pageSelEl[0][pageSelEl[0].selectedIndex].text);
        headInputEl.val(headSelEl.val());
        footInputEl.val(footSelEl.val());
        styleInputEl.val(styleCheckEl.prop('checked'));
      }
      else {
        actionInputEl.val('update');
        pageIdInputEl.val(pageSelEl.val());
        nameInputEl.val(pageSelEl[0][pageSelEl[0].selectedIndex].text);
        headInputEl.val(headSelEl.val());
        footInputEl.val(footSelEl.val());
        styleInputEl.val(styleCheckEl.prop('checked'));
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