jQuery(document).ready(function($) {
  const mediamanager = new Mediamanager({
    loadItemsUrl: window.location.origin + '/wp-content/themes/wp98/assets/mediamanager/loadfiles.json',
    uploadUrl: '/'
  });

  const menuRows = $('.start-menu-row');
  $.each(menuRows, function(keys, row) {
    // Hidden input elements
    const actionInputEl = $(row.children[0]);
    const orderInputEl = $(row.children[1]);
    const pageInputEl = $(row.children[2]);
    const iconInputEl = $(row.children[3]);

    // Icon elements
    const noImgEl = $(row.children[5].children[0]);
    const imgEl = $(row.children[5].children[1]);
    const originalImgUrl = iconInputEl.val() !== '' ?
      iconInputEl.val() :
      '';
    
    // Page drop down elements
    const pageSelEl = $(row.children[6].children[0]);
    const originalPageId = pageSelEl.val();
    const originalRow = Array.from(row.parentNode.children).indexOf(row);
  
    let btnsCon, changeBtn, removeBtn, chooseBtn;

    // Page selection dropdown event listener
    pageSelEl.change(updateInputValueState);

    // Add event listeners if the row already contains a selected icon
    if (imgEl[0].nextElementSibling.type === undefined) {
      btnsCon = $(row.children[5].children[2]);
      
      changeBtn = $(btnsCon[0].children[0]);
      removeBtn = $(btnsCon[0].children[1]);
      
      addChangeIconEventListener(changeBtn);

      addRemoveIconEventListener(removeBtn);
    }
    // Add event listeners if the row doesn't contain a selected icon
    else {
      chooseBtn = $(row.children[5].children[2]);

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
        }).html('Choose<br>Icon').appendTo(row.children[5]);
        
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

          btnsCon = $('<div/>', { class: 'start-menu-btn-flex-container'})
            .appendTo(row.children[5]);
          
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
      if (pageSelEl.val() === 'choose') {
        actionInputEl.val('delete');
        pageInputEl.val(originalPageId);
      }
      else if (pageSelEl.val() === originalPageId && iconInputEl.val() === originalImgUrl) {
        actionInputEl.val('no-change');
        pageInputEl.val(originalPageId);
      }
      else {
        actionInputEl.val('update');
        pageInputEl.val(pageSelEl.val());
      }

      // Row number in the table
      const rowNo = Array.from(row.parentNode.children).indexOf(row);
      orderInputEl.val(rowNo);
    }
  });
});

/*
document.addEventListener('DOMContentLoaded', () => {
  

  const menuRows = document.querySelectorAll('.start-menu-row')
  for (const [i, row] of menuRows.entries()) {
    // Page drop down elements
    const pageSelEl = row.children[4].children[1];
    const pageInputEl = row.children[4].children[0];
    const actionInputEl = row.children[0];
    const orderInputEl = row.children[1];
    const originalPageId = pageSelEl.value;
    const originalRow = Array.from(row.parentNode.children).indexOf(row);

    // Icon elements
    const iconInputEl = row.children[3].children[0];
    const noImgEl = row.children[3].children[1];
    const imgEl = row.children[3].children[2];
    const originalImgUrl = iconInputEl.value !== '' ?
      iconInputEl.value :
      '';
    let btnsCon, changeBtn, removeBtn, chooseBtn;

    // Page selection dropdown event listener
    pageSelEl.addEventListener('change', () => {
      updateInputValueState();
    });

    // Add event listeners if the row already contains a selected icon
    if (imgEl.nextElementSibling.type === undefined) {
      btnsCon = row.children[3].children[3];
      
      changeBtn = btnsCon.children[0];
      removeBtn = btnsCon.children[1];
      
      addChangeIconEventListener(changeBtn);

      addRemoveIconEventListener(removeBtn);
    }
    // Add event listeners if the row doesn't contain a selected icon
    else {
      chooseBtn = row.children[3].children[3];

      addChooseIconEventListener(chooseBtn);
    }

    // Event listener for the "change" button
    function addChangeIconEventListener(element) {
      element.addEventListener('click', () => {
        mediamanager.open();
        mediamanager.options.insertType = 'html';
        mediamanager.options.insert = chosenImg => {
          iconInputEl.value = chosenImg.src;
          imgEl.src = chosenImg.src;
          imgEl.style.display = 'inline-block';
          noImgEl.style.display = 'none';
        }
      });
    }

    // Event listener for the "remove" button
    function addRemoveIconEventListener(element) {
      element.addEventListener('click', () => {
        noImgEl.style.display = 'inline-flex';
        imgEl.src = '';
        imgEl.style.display = 'none';

        // Remove the old buttons and add a "choose" button
        iconInputEl.value = '';
        btnsCon.remove();
        chooseBtn = document.createElement('button');
        chooseBtn.type = 'button';
        chooseBtn.classList.add('mediamanager-btn', 'add-button', 'button', 'button-secondary');
        chooseBtn.innerHTML = 'Choose<br>Icon';
        row.children[3].appendChild(chooseBtn);
        addChooseIconEventListener(chooseBtn);

        updateInputValueState();
      });
    }

    // Event listener for the "Choose" button
    function addChooseIconEventListener(element) {
      element.addEventListener('click', () => {
        mediamanager.open();
        mediamanager.options.insertType = 'html';
        mediamanager.options.insert = chosenImg => {
          iconInputEl.value = chosenImg.src;
          imgEl.src = chosenImg.src;
          imgEl.style.display = 'inline-block';
          noImgEl.style.display = 'none';

          // Remove the old buttons and add a "choose" button
          chooseBtn.remove();

          changeBtn = document.createElement('button');
          changeBtn.type = 'button';
          changeBtn.classList.add('mediamanager-btn', 'button', 'button-secondary');
          changeBtn.innerText = 'Change';
          addChangeIconEventListener(changeBtn);

          removeBtn = document.createElement('button');
          removeBtn.type = 'button';
          removeBtn.classList.add('icon-remove-btn', 'button', 'button-secondary');
          removeBtn.innerText = 'Remove';
          addRemoveIconEventListener(removeBtn);

          btnsCon = document.createElement('div');
          btnsCon.classList.add('start-menu-btn-flex-container');
          btnsCon.append(changeBtn, removeBtn);
          row.children[3].appendChild(btnsCon);

          updateInputValueState();
        }
      });
    }

    // Update the values to be returned to the php page when the form is submitted
    function updateInputValueState() {
      if (pageSelEl.value === 'choose') {
        actionInputEl.value = 'delete';
        pageInputEl.value = originalPageId;
      }
      else if (pageSelEl.value === originalPageId && iconInputEl.value === originalImgUrl) {
        actionInputEl.value = 'no-change';
        pageInputEl.value = originalPageId;
      }
      else {
        actionInputEl.value = 'update';
        pageInputEl.value = pageSelEl.value;
      }

      // Row number in the table
      const row = Array.from(row.parentNode.children).indexOf(row);
      orderInputEl.value = row;
    }
  }
});

*/