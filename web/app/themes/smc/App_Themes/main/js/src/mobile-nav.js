//== multi-level mobile menu
const pageHeader = document.querySelector(".wrapper-header");
const toggleMenu = pageHeader.querySelector(".toggle-menu");
const menuWrapper = pageHeader.querySelector(".wrapper-mobile-menu");
const level1Links = pageHeader.querySelectorAll(".level-1 > li > a");
const listWrapper2 = pageHeader.querySelector(".menu-container:nth-child(2)");
const listWrapper3 = pageHeader.querySelector(".menu-container:nth-child(3)");
const subMenuWrapper2 = listWrapper2.querySelector(".sub-menu-container");
const subMenuWrapper3 = listWrapper3.querySelector(".sub-menu-container");
const backOneLevelBtns = pageHeader.querySelectorAll(".back-one-level");
const isVisibleClass = "is-visible";
const isActiveClass = "is-active";

toggleMenu.addEventListener("click", function () {
  menuWrapper.classList.toggle(isVisibleClass);
  toggleMenu.classList.toggle('is-active');
  document.body.classList.toggle('oflow');

  if (!this.classList.contains(isVisibleClass)) {
    listWrapper2.classList.remove(isVisibleClass);
    listWrapper3.classList.remove(isVisibleClass);
    const menuLinks = menuWrapper.querySelectorAll("a");
    for (const menuLink of menuLinks) {
      menuLink.classList.remove(isActiveClass);
    }
  }

  // if (menuWrapper.classList.contains(isVisibleClass)) {
  //   document.getElementById('toggleSearchMobile').classList.remove('is-active')
  //   document.getElementById('mobileSearchPanel').classList.remove('open')
  //   document.body.classList.add('oflow')
  // } else {
  //   document.body.classList.remove('oflow')
  // }
});

for (const level1Link of level1Links) {
  level1Link.addEventListener("click", function (e) {
    const siblingList = level1Link.nextElementSibling;
    if (siblingList) {
      e.preventDefault();
      this.classList.add(isActiveClass);
      const cloneSiblingList = siblingList.cloneNode(true);
      subMenuWrapper2.innerHTML = "";
      subMenuWrapper2.append(cloneSiblingList);
      listWrapper2.classList.add(isVisibleClass);
    }
  });
}

listWrapper2.addEventListener("click", function (e) {
  const target = e.target;
  if (target.tagName.toLowerCase() === "a" && target.nextElementSibling) {
    const siblingList = target.nextElementSibling;
    e.preventDefault();
    target.classList.add(isActiveClass);
    const cloneSiblingList = siblingList.cloneNode(true);
    subMenuWrapper3.innerHTML = "";
    subMenuWrapper3.append(cloneSiblingList);
    listWrapper3.classList.add(isVisibleClass);
  }
});

for (const backOneLevelBtn of backOneLevelBtns) {
  backOneLevelBtn.addEventListener("click", function () {
    const parent = this.closest(".menu-container");
    parent.classList.remove(isVisibleClass);
    parent.previousElementSibling
      .querySelector(".is-active")
      .classList.remove(isActiveClass);
  });
}
