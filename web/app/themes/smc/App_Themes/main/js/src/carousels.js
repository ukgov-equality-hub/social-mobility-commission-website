import tns from './tiny-slider.js'

//== Init Tiny Slider // http://ganlanyuan.github.io/tiny-slider/

const slideExist = document.getElementsByClassName('tiny-slider')

if (slideExist.length > 0) {
  var slider = tns({
    container: '.tiny-slider',
    items: 3,
    slideBy: 1,
    // slideBy: 'page',
    gutter: 30,
    autoplay: false,
    autoplayButtonOutput: false,
    mouseDrag: true,
    controls: true,
    controlsPosition: 'bottom',
    nav: true,
    navPosition: 'bottom',
    controlsText: ['prev', 'next'],
    responsive: {
      0: {
        items: 1,
      },
      600: {
        items: 2,
      },
      1000: {
        items: 3,
      },
    },
  })
}
