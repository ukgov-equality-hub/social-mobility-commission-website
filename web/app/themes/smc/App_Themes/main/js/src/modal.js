//== ********************************
//== Feature Modal (work in progress)
//== ********************************

const btnModalOpen = document.querySelectorAll('.btn-modal')

// Loop through all elements with the class name "btn-modal"
for (let i = 0; i < btnModalOpen.length; i++) {
  // When a user clicks on any element with the class name "btn-modal"
  btnModalOpen[i].addEventListener('click', function () {
    // Get the value of the data-modalid attribute
    const modalID = this.dataset.modalid
    // Get the value of the data-modaliframe attribute
    const modalURL = this.dataset.modaliframe

    // If the data-modalid attribute has a value
    if (modalID) {
      // Get the element with an ID that matches the value of the data-modalid attribute
      const pnlModal = document.getElementById(`${modalID}`)
      // Run the handleOpenOnPageModal function
      handleOpenOnPageModal(pnlModal, btnModalOpen[i])
    }

    // If the data-modaliframe attribute has a value
    if (modalURL) {
      // Run the handleOpenVideoModal function
      handleOpenVideoModal(modalURL)
    }
  })
}

// Select all elements with the class 'btn-modal-close'
const btnModalClose = document.querySelectorAll('.btn-modal-close')

// Loop through each element
for (let i = 0; i < btnModalClose.length; i++) {
  // Add a click event listener
  btnModalClose[i].addEventListener('click', function () {
    // Call the closeFeatureModal function
    closeFeatureModal()
  })
}

const closeFeatureModal = (e) => {
  const pnlModalclose = document.querySelectorAll('.feature-modal.is-open')
  if (pnlModalclose.length > 0) {
    for (let i = 0; i < pnlModalclose.length; i++) {
      pnlModalclose[i].classList.remove('is-open')
      //pnlModalclose[i].setAttribute('aria-hidden', 'true')
      pnlModalclose[i].classList.add('is-closing')
      setTimeout(() => {
        pnlModalclose[i].classList.remove('is-closing')
        document.body.classList.remove('oflow')
      }, 300)
    }
    for (let j = 0; j < btnModalOpen.length; j++) {
      btnModalOpen[j].setAttribute('aria-expanded', 'false')
    }
  }
}

const closeVideoModal = (_modal) => {
  _modal.classList.add('is-closing')
  setTimeout(() => {
    _modal.remove()
    document.body.classList.remove('oflow')
  }, 300)
}

const createElementClass = (_elName, _classList) => {
  let newElement = document.createElement(_elName)
  newElement.classList = _classList
  return newElement
}

//== prep the URL for embedding
function fixVideoURL(_urlText) {
  let returnVal = _urlText
  //== youtube
  if (_urlText.includes('youtube.com')) {
    let regYT = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i
    returnVal = _urlText.replace(regYT, 'youtube.com/embed/$1?autoplay=1')
  }
  //== vimeo
  if (_urlText.includes('vimeo.com')) {
    let regVim = /vimeo.*(?:\/|clip_id=)([0-9a-z]*)/i
    returnVal = '//player.vimeo.com/video/' + _urlText.match(regVim)[1]
  }
  return returnVal
}

const handleOpenOnPageModal = (_pnlModal, _btnModal) => {
  //== open the modal
  _pnlModal.classList.add('is-open')
  // _pnlModal.setAttribute('aria-hidden', 'false')
  _btnModal.setAttribute('aria-expanded', 'true')

  _pnlModal.addEventListener('click', function (e) {
    if (e.target.classList.contains('is-open')) {
      closeFeatureModal()
    }
  })

  document.body.classList.add('oflow')
}

const handleOpenVideoModal = (_modalURL) => {
  //== create modal block and content
  let mModal = createElementClass('div', 'feature-modal is-video is-open')
  let mContent = createElementClass('div', 'modal-content')

  //== create iframe to embed the video for content
  let mFrame = createElementClass('iframe', 'modal-video')
  mFrame.src = fixVideoURL(_modalURL.replace('https://', '//'))
  mFrame.frameBorder = 0
  mFrame.setAttribute('allowfullscreen', '')

  //== create close button for content
  let mClose = createElementClass('button', 'btn-modal-close')
  mClose.innerHTML = '×'
  mClose.setAttribute('type', 'button')
  mClose.addEventListener('click', function () {
    closeVideoModal(mModal)
  })

  //== add close and iframe to content
  mContent.appendChild(mClose)
  mContent.appendChild(mFrame)

  //== add content to modal
  mModal.appendChild(mContent)
  //mModal.setAttribute('aria-hidden', false)
  mModal.addEventListener('click', function (e) {
    if (e.target.classList.contains('is-open')) {
      closeVideoModal(mModal)
    }
  })

  //== add the modal block to the body
  document.body.appendChild(mModal)
  document.body.classList.add('oflow')
}
