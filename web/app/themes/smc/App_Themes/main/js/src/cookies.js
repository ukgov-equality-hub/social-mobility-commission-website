//== set & get Cookie

function setCookie(cName, cValue, expDays) {
  let date = new Date()
  date.setTime(date.getTime() + expDays * 24 * 60 * 60 * 1000)
  const expires = 'expires=' + date.toUTCString()
  document.cookie = cName + '=' + cValue + '; ' + expires + '; path=/'
}

function getCookie(cName) {
  const name = cName + '='
  const cDecoded = decodeURIComponent(document.cookie)
  const cArr = cDecoded.split('; ')
  let res
  cArr.forEach((val) => {
    if (val.indexOf(name) === 0) res = val.substring(name.length)
  })
  return res
}

// setCookie('cookie-name', sValue, 30);
// getCookie('cookie-name');

//== close alert bar and set/get cookie
// Get the alert bar element
const alertBar = document.getElementById('alertBar')

// Get the close button
const alertBarClose = document.getElementById('closeAlert')

// If the alert bar and close button exist...
if (alertBar && alertBarClose) {
  // Add an event listener for the click event on the close button
  alertBarClose.addEventListener('click', function () {
    // Hide the alert bar
    alertBar.style.display = 'none'

    // Set a cookie so that the alert bar is hidden for the next 14 days
    setCookie('sz-alert-bar', true, 14)
  })

  // If the cookie is set...
  if (getCookie('sz-alert-bar')) {
    // Hide the alert bar
    alertBar.style.display = 'none'
  }
}
