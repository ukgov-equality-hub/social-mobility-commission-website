var mapObject
var markersCollArray = []
var infoWindow = new google.maps.InfoWindow({ content: '' })

function initMappingCollection(_ID, _lat, _lng, _markersCollection, _zoom) {
  markersCollArray = _markersCollection

  var loca = { lat: _lat, lng: _lng }

  var arrStyle = [
    {
      featureType: 'all',
      stylers: [
        {
          saturation: 0,
        },
        {
          hue: '#e7ecf0',
        },
      ],
    },
    {
      featureType: 'road',
      stylers: [
        {
          saturation: -70,
        },
      ],
    },
    {
      featureType: 'transit',
      stylers: [
        {
          visibility: 'off',
        },
      ],
    },
    {
      featureType: 'poi',
      stylers: [
        {
          visibility: 'off',
        },
      ],
    },
    {
      featureType: 'water',
      stylers: [
        {
          visibility: 'simplified',
        },
        {
          saturation: -60,
        },
      ],
    },
  ]

  //== set-up the map
  mapObject = new google.maps.Map(document.getElementById(_ID), {
    zoom: _zoom,
    center: loca,
    styles: arrStyle,
    disableDefaultUI: true,
    mapTypeControl: false,
    streetViewControl: false,
  })

  //== loop through collection and add the pins to the map
  for (i = 0; i < markersCollArray.length; i++) {
    addMarker(markersCollArray[i])
  }
}

//== function to add marker to map
function addMarker(marker) {
  const nID = marker[0]
  const sTitle = marker[1]
  const sCategory = marker[4] //== if needed to show a different pin type
  const sDate = marker[5]
  const sMainText = marker[6]
  const oPos = new google.maps.LatLng(marker[2], marker[3])

  const sInfoContent = `
    <div class='pin-content'>
      <p class='title'>${marker[1]}</p>
      <p class='date'>${sDate}</p>
      <p class=''>${sMainText}</p>
    </div>`

  markerPin = new google.maps.Marker({
    title: sTitle,
    position: oPos,
    category: sCategory,
    icon: 'assets/main/img/layout/map-pin.png',
    map: mapObject,
  })

  google.maps.event.addListener(
    markerPin,
    'click',
    (function (markerPin, sInfoContent) {
      return function () {
        infoWindow.setContent(sInfoContent)
        infoWindow.open(mapObject, markerPin)
      }
    })(markerPin, sInfoContent)
  )
}

function gmInit() {
  console.log('Google Maps Callback')
}

//== USAGE ON HTML PAGE // markersColl can be a delimited collection
//<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB6VCrMw7Y4dts2YkwiCkPG20egZzWHTYwY&callback=gmInit"></script> //== get a legit GM key for each client
//<script src="assets/main/js/mapping.min.js"></script>
//<!--map example-- >
//	<script>
//
//		var markersColl = [
//			['0', 'Item Title', 54.6052647, -5.922147, 'Event', '23 June 2018', 'Level 7, City Quays 2, Clarendon Road, Belfast BT1 3FD']
//		];
//
//		initMappingCollection("divMap", 54.5953611, -5.9372377, markersColl, 15);
//
//</script>
