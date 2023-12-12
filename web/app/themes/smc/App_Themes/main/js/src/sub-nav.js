
const selectSubnav = element => {

  const active = document.querySelector('.active');
  const visible = document.querySelector('.visible');

  const subnavPanel = document.getElementById(element.href.split('#')[1]);

  if (active) {
    active.classList.remove('active');
  }

  element.classList.add('active');

  if (visible) {
    visible.classList.remove('visible');
  }

  subnavPanel.classList.add('visible');

}


document.addEventListener('click', event => {

  if (event.target.matches('.js-subnav-trigger')) {
    selectSubnav(event.target);
    event.preventDefault();
  }
  
}, false);
