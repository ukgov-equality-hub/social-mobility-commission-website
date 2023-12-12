//== tooltip
const tooltipBtns = document.querySelectorAll('.tooltip-item .tooltip-button')

tooltipBtns.forEach((btn) => {

  const tooltip = btn.nextElementSibling
  const closeButton = tooltip.querySelector('.close-button')

  btn.addEventListener('click', () => {
    tooltip.classList.add('open')
    tooltip.setAttribute('aria-expanded', 'true')
  })

  closeButton.addEventListener('click', () => {
    tooltip.classList.remove('open');
    tooltip.setAttribute('aria-expanded', 'false')
  })

})
