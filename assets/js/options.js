document.addEventListener('DOMContentLoaded', () => {
  const mediamanager = new Mediamanager({
    loadItemsUrl: templateUrl + '/assets/mediamanager/loadfiles.json',
    uploadUrl: '/'
  });

  let btns = document.querySelectorAll('.mediamanager-btn');
  console.log(btns)
  for (const [i, btn] of btns.entries()) {
    console.log('wooters')
    btn.addEventListener('click', () => {
      mediamanager.open();
      mediamanager.options.insertType = 'html';
      mediamanager.options.insert = img => {
        btn.previousElementSibling.src = img.src;
      }
    });
  }
});