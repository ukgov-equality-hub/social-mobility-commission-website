//== accordions
const accordionBtns = document.querySelectorAll('.accordion-button')

accordionBtns.forEach((btn) => {
  btn.addEventListener('click', () => {
    const panel = btn.nextElementSibling
    const isPanelOpen = btn.classList.contains('active')

    //== close all panels
    accordionBtns.forEach((otherBtn) => {
      otherBtn.classList.remove('active')
      otherBtn.setAttribute('aria-expanded', 'false')
      otherBtn.nextElementSibling.style.maxHeight = null
      otherBtn.nextElementSibling.classList.remove('open')
    })

    if (!isPanelOpen) {
      //== open the clicked panel
      panel.style.maxHeight = panel.scrollHeight + 'px'
      btn.classList.add('active')
      btn.setAttribute('aria-expanded', 'true')
      panel.classList.add('open')
    }
  })
})
