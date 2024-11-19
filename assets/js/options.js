document.addEventListener('DOMContentLoaded', () => {
  const mediamanager = new Mediamanager({
    loadItemsUrl: templateUrl + '/assets/mediamanager/loadfiles.json',
    uploadUrl: '/'
  });

  const menuRows = document.querySelectorAll('.start-menu-row')
  for (const [i, row] of menuRows.entries()) {
    const input = row.children[1].children[0];
    const noImg = row.children[1].children[1];
    const img = row.children[1].children[2];
    let btnsCon, changeBtn, removeBtn, chooseBtn;

    // Add event listeners if the row already contains a selected icon
    if (img.nextElementSibling.type === undefined) {
      btnsCon = row.children[1].children[3];
      
      changeBtn = btnsCon.children[0];
      removeBtn = btnsCon.children[1];
      
      addChangeIconEventListener(changeBtn);

      addRemoveIconEventListener(removeBtn);
    }
    // Add event listeners if the row doesn't contain a selected icon
    else {
      chooseBtn = row.children[1].children[3];

      addChooseIconEventListener(chooseBtn);
    }

    // Event listener for the "change" button
    function addChangeIconEventListener(element) {
      element.addEventListener('click', () => {
        mediamanager.open();
        mediamanager.options.insertType = 'html';
        mediamanager.options.insert = chosenImg => {
          input.value = chosenImg.src;
          img.src = chosenImg.src;
          img.style.display = 'inline-block';
          noImg.style.display = 'none';
        }
      });
    }

    // Event listener for the "remove" button
    function addRemoveIconEventListener(element) {
      element.addEventListener('click', () => {
        noImg.style.display = 'inline-flex';
        img.src = '';
        img.style.display = 'none';

        // Remove the old buttons and add a "choose" button
        input.value = '';
        btnsCon.remove();
        chooseBtn = document.createElement('button');
        chooseBtn.type = 'button';
        chooseBtn.classList.add('mediamanager-btn', 'add-button', 'button', 'button-secondary');
        chooseBtn.innerHTML = 'Choose<br>Icon';
        row.children[1].appendChild(chooseBtn);
        addChooseIconEventListener(chooseBtn);
      });
    }

    // Event listener for the "Choose" button
    function addChooseIconEventListener(element) {
      element.addEventListener('click', () => {
        mediamanager.open();
        mediamanager.options.insertType = 'html';
        mediamanager.options.insert = chosenImg => {
          input.value = chosenImg.src;
          img.src = chosenImg.src;
          img.style.display = 'inline-block';
          noImg.style.display = 'none';

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
          row.children[1].appendChild(btnsCon);
        }
      });
    }

  }

  
});

