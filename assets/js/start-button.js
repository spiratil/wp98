const taskbar = document.querySelector('#wp98-taskbar');
const startBtn = document.querySelector('#wp98-start-button');
const startMenu = document.querySelector('#wp98-start-menu');
const menuTitleBar = document.querySelector('#wp98-start-menu .title-bar');
const menuItems = document.querySelector('#wp98-start-menu nav ul');

startBtn.addEventListener('click', showHideStartMenu);

/*
Array.from(menuItems.children).forEach(item => {
  const img = document.createElement('img');
  img.src = templateUrl + '/assets/images/icons/camera3-4.png';
  item.children[0].innerHTML = img.outerHTML + item.children[0].innerText;
});
*/

const mouseEnterHoverEvent = document.querySelector('#wp98-start-menu nav').addEventListener('mouseover', e => {
  if (e.target.tagName === 'UL') return;
  else if (e.target.tagName === 'LI') e.target.classList.add('menu-hover');
  else if (e.target.tagName === 'A') e.target.parentElement.classList.add('menu-hover');
  else if (e.target.tagName === 'IMG') e.target.parentElement.parentElement.classList.add('menu-hover');
}, true);

const mouseExitHoverEvent = document.querySelector('#wp98-start-menu nav').addEventListener('mouseout', e => {
  if (e.target.tagName === 'UL') return;
  else if (e.target.tagName === 'LI') e.target.classList.remove('menu-hover');
  else if (e.target.tagName === 'A') e.target.parentElement.classList.remove('menu-hover');
  else if (e.target.tagName === 'IMG') e.target.parentElement.parentElement.classList.remove('menu-hover');
}, true);

function showHideStartMenu() {
  startMenu.style.display === 'flex' ? startMenu.style.display = 'none' : startMenu.style.display = 'flex';



  if (menuTitleBar !== null) menuTitleBar.style.width = menuTitleBar.children[0].clientWidth + 'px'; // Set the width of titleBar to fix render issue in Firefox

  startMenu.style.bottom = `${taskbar.clientHeight + 1}px`;
  
}