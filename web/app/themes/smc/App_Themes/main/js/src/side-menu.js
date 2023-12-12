const submenuSection = document.getElementsByClassName('submenu-sections')

if (submenuSection.length > 0) {

  let jumpSections = document.querySelectorAll(".submenu-sections .jump-section");
  let submenu = document.querySelector('.nav-submenu');

  jumpSections.forEach(function(section) {

    let sectionID = section.id;
    let sectionTitleText = section.querySelector('.section-title').innerText;
    let li = document.createElement('li');
    let a = document.createElement('a');

    a.setAttribute('href', '#' + sectionID);
    a.textContent = sectionTitleText;

    li.appendChild(a);
    submenu.appendChild(li);

  })

  // Add an event listener listening for scroll
  window.addEventListener("scroll", submenuHighlighter);

  function submenuHighlighter() {

    // Get current scroll position
    let scrollY = window.pageYOffset;

    // Now we loop through sections to get height, top and ID values for each
    jumpSections.forEach(current => {
      const sectionHeight = current.offsetHeight;
      const sectionTop = current.offsetTop - 50;
      sectionId = current.getAttribute("id");

      /*
      - If our current scroll position enters the space where current section on screen is, add .is-active class to corresponding navigation link, else remove it
      - To know which link needs an active class, we use sectionId variable we are getting while looping through sections as an selector
      */
      if (
        scrollY > sectionTop &&
        scrollY <= sectionTop + sectionHeight
      ){
        document.querySelector(".nav-submenu a[href*=" + sectionId + "]").classList.add("is-active");
      } else {
        document.querySelector(".nav-submenu a[href*=" + sectionId + "]").classList.remove("is-active");
      }
    });
  }

}

//== mobile - side menu
const btnMobNavSide = document.getElementById('btnSideMenu')
const pnlMobNavSide = document.getElementById('pnlSideMenu')

if (btnMobNavSide && pnlMobNavSide) {
  btnMobNavSide.addEventListener('click', function (e) {
    e.preventDefault()
    btnMobNavSide.classList.toggle('is-active')
    pnlMobNavSide.classList.toggle('is-open')
    btnMobNavSide.setAttribute('aria-expanded', btnMobNavSide.classList.contains('is-open'))
  })
}
