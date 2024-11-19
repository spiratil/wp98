document.addEventListener('DOMContentLoaded', () => {
  const taskbar = document.querySelector('#wp98-taskbar');
  const startBtn = document.querySelector('#wp98-start-button');
  const startMenu = document.querySelector('#wp98-start-menu');
  const menuTitleBar = document.querySelector('#wp98-start-menu .title-bar');
  const menuItems = document.querySelectorAll('#wp98-start-menu .page-item');

  console.log(menuItems)

  startBtn.addEventListener('click', showHideStartMenu);

  // Menu item hover enter
  const mouseEnterHoverEvent = document.querySelector('#wp98-start-menu nav').addEventListener('mouseover', e => {
    if (e.target.tagName === 'UL') return;
    else if (e.target.tagName === 'LI') e.target.classList.add('menu-hover');
    else if (e.target.tagName === 'A') e.target.parentElement.classList.add('menu-hover');
    else if (e.target.tagName === 'IMG') e.target.parentElement.parentElement.classList.add('menu-hover');
  }, true);

  // Menu item hover exit
  const mouseExitHoverEvent = document.querySelector('#wp98-start-menu nav').addEventListener('mouseout', e => {
    if (e.target.tagName === 'UL') return;
    else if (e.target.tagName === 'LI') e.target.classList.remove('menu-hover');
    else if (e.target.tagName === 'A') e.target.parentElement.classList.remove('menu-hover');
    else if (e.target.tagName === 'IMG') e.target.parentElement.parentElement.classList.remove('menu-hover');
  }, true);

  function showHideStartMenu() {
    startMenu.style.display === 'flex' ? startMenu.style.display = 'none' : startMenu.style.display = 'flex';
    // Set the width of titleBar to fix render issue in Firefox
    if (menuTitleBar !== null) menuTitleBar.style.width = menuTitleBar.children[0].clientWidth + 'px'; 
    startMenu.style.bottom = `${taskbar.clientHeight + 1}px`;
  }
  
  // Menu item event listener and handling
  menuItems.forEach(
    item => item.addEventListener('click', e => {
      const id = item.children[0].getAttribute('data-id');
      const url = item.children[0].value;
      if (document.querySelector(`#wp98-page-${id}`) !== null) return;
      
      const page = document.createElement('div');
      page.id = `wp98-page-${id}`;
      page.classList.add('wp98-ie-page');
      page.innerHTML = 'Content loading...'

      document.querySelector('body').appendChild(page);

    

      const xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          console.log(this.responseText)
          page.innerHTML = this.responseText;
        }
      }

      xhttp.open("GET", url, true);
      //xhttp.setRequestHeader('Accept', 'text/html');
      xhttp.send();

      document.querySelector('body').appendChild(page);


    })
  );

});