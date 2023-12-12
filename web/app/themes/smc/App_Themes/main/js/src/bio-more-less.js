const readMoreButtons = document.querySelectorAll('.cta-more-less');

readMoreButtons.forEach((btn) => {
  btn.addEventListener('click', function() {
    const moreText = btn.previousElementSibling
    const isExpanded = btn.classList.contains('active')
    const bioCard = btn.parentElement.parentElement

    if (!isExpanded) {
      btn.classList.add('active')
      btn.setAttribute('aria-expanded', 'true')
      moreText.classList.add('expanded')
      btn.textContent = 'Read less'
      bioCard.classList.add('active')
    } else {
      btn.classList.remove('active')
      btn.setAttribute('aria-expanded', 'false')
      moreText.classList.remove('expanded')
      btn.textContent = 'Read more'
      bioCard.classList.remove('active')
    }
  });
})
