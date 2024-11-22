document.addEventListener('DOMContentLoaded', () => {
  const mediamanager = new Mediamanager({
    loadItemsUrl: templateUrl + '/assets/mediamanager/loadfiles.json',
    uploadUrl: '/'
  });

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

