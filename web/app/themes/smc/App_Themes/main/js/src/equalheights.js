//== Equal Height elements

function setHeight(el, val) {
  if (typeof val === 'function') val = val()
  if (typeof val === 'string') el.style.height = val
  else el.style.height = val + 'px'
}

var equalheight = function (container) {
  var currentTallest = 0,
    currentRowStart = 0,
    rowDivs = new Array(),
    // $el,
    topPosition = 0

  Array.from(document.querySelectorAll(container)).forEach((el, i) => {
    el.style.height = 'auto'
    topPosition = el.offsetTop
    if (currentRowStart != topPosition) {
      for (let currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
        setHeight(rowDivs[currentDiv], currentTallest)
      }
      rowDivs.length = 0
      currentRowStart = topPosition
      currentTallest = parseFloat(getComputedStyle(el, null).height.replace('px', ''))
      rowDivs.push(el)
    } else {
      rowDivs.push(el)
      currentTallest =
        currentTallest < parseFloat(getComputedStyle(el, null).height.replace('px', ''))
          ? parseFloat(getComputedStyle(el, null).height.replace('px', ''))
          : currentTallest
    }
    for (let currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
      setHeight(rowDivs[currentDiv], currentTallest)
    }
  })
}

window.addEventListener('load', function () {
  equalheight('.eq')
})

window.addEventListener('resize', function () {
  setTimeout(function () {
    equalheight('.eq')
  })
})

window.addEventListener('load', function () {
  equalheight('.work-card')
  equalheight('.work-card .inner')
})

window.addEventListener('resize', function () {
  setTimeout(function () {
    equalheight('.work-card')
    equalheight('.work-card .inner')
  })
})

window.addEventListener('load', function () {
  equalheight('.feature-card .content')
})

window.addEventListener('resize', function () {
  setTimeout(function () {
    equalheight('.feature-card .content')
  })
})
