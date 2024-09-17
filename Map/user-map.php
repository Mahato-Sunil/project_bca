<?php
require '../Configuration/config.php';

//getting the data from the url 
$lat = isset($_GET['lat']) ? $_GET['lat'] : null;
$lon = isset($_GET['lon']) ? $_GET['lon'] : null;
$time = isset($_GET['time']) ? $_GET['time'] : null;

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Location View</title>
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no">
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet">
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
    <link rel="stylesheet" href="../Style/mapbox-style.css">
</head>

<body>
    <div id="loader" class="spinner"></div>
    <div class="data-container">
        <div id="lat-lon-data" class="data-summary">
            <h1> Location Details : </h1>
            <p> Latitude :
                <span>
                    <?php echo $lat ?>
                </span>
            </p>
            <p> Longitude :
                <span>
                    <?php echo $lon ?>
                </span>
            </p>
            <p> Requested Time :
                <span>
                    <?php echo $time ?>
                </span>
            </p>
        </div>

        <div class="data-summary">
            <h1> Reversed Geocoded Data :</h1>
            <p> Place Name : <span id="exactLocation"></span> </p>
            <p> Location Category : <span id="locationCategory"></span></p>
        </div>
        <button id="getDirection" class="w3-button w3-white w3-border w3-border-blue"> Get Direction </button>
        <button id="changeTheme" class="w3-button w3-white w3-border w3-border-blue"> Satellite View </button>
    </div>

    <div id="map"></div>
    <script type="text/javascript">
        const lat = <?php echo $lat ?>;
        const lon = <?php echo $lon ?>;

        //get the current location of the user and update it 
        const getRtLocation = () => {
            //get the exact location 
            if (navigator.geolocation) {
                return new Promise((resolve, reject) => {
                    navigator.geolocation.watchPosition((target) => {
                        let current_lat = target.coords.latitude;
                        let current_lon = target.coords.longitude;
                        resolve([current_lon, current_lat]); // Resolve with location array
                    }, reject); // Pass error handling to Promise
                });
            } else {
                console.log(Promise.reject(new Error("Geolocation is not supported")));
                //get the ip based location 
                console.log("using ip based location");

                fetch('https://ipapi.co/json/')
                    .then(response => response.json())
                    .then(data => {
                        let current_lat = data.latitude;
                        let current_lon = data.longitude;
                        return ([current_lon, current_lat]);
                    });
            }
        };

        // Check if coordinates are available
        if (lat !== null && lon !== null) {
            const coords = [lon, lat];
            console.log(typeof(coords));
            // Initialize map after fetching coordinates
            mapboxgl.accessToken = '<?php echo $mapbox_access_token ?>';
            const map = new mapboxgl.Map({
                container: 'map',
                // style: 'mapbox://styles/mapbox/streets-v12',
                style: "mapbox://styles/mapbox/streets-v12",
                center: coords,
                zoom: 16
            });

            // create the popup
            const popup = new mapboxgl.Popup({
                offset: 45
            }).setText(
                `Latitude : ${lat} ,  Longitude : ${lon}`
            );

            // adding circles over the marker 
            map.on('load', function() {
                // Add a circle layer
                map.addLayer({
                    'id': 'circle-layer',
                    'type': 'circle',
                    'source': {
                        'type': 'geojson',
                        'data': {
                            'type': 'Feature',
                            'geometry': {
                                'type': 'Point',
                                'coordinates': coords
                            }
                        }
                    },
                    'paint': {
                        // The circle radius is measured in pixels
                        'circle-radius': 100,
                        'circle-color': '#FD4545',
                        'circle-opacity': 0.3
                    }
                });
            });

            //adding the map controls 
            map.addControl(new mapboxgl.NavigationControl()); //zoom and rotation controls 

            // for the geolocate 
            let geolocate = new mapboxgl.GeolocateControl({
                positionOptions: {
                    enableHighAccuracy: true
                },
                trackUserLocation: true,
            });
            map.addControl(geolocate);
            map.addControl(new mapboxgl.FullscreenControl());
            // Add marker for the entered location
            new mapboxgl.Marker({
                    color: "#E20D0D"
                })
                .setLngLat(coords)
                .setPopup(popup)
                .addTo(map);


            // fetching the reverse address using the  fetch api and the reverse geocoding api of mapbox 
            const endpoint = "mapbox.places";
            const url = `https://api.mapbox.com/geocoding/v5/${endpoint}/${lon},${lat}.json?access_token=${mapboxgl.accessToken}`;

            fetch(url)
                .then(response => {
                    if (!response.ok)
                        throw new Error("Error in Network : " + response.statusText);
                    return response.json();
                })

                .then(data => {
                    console.log(data);
                    let exactLocation = data.features[0].place_name;
                    let locationCategory = data.features[0].properties.category;
                    dispatchLocation(exactLocation, locationCategory);
                })
                .catch(error => console.error('Error :', error));

            //fetching the data for  showing the direction 
            const getUserDirection = () => {
                const victimCoords = coords;

                // retrieving the ral time location 
                getRtLocation()
                    .then(currentCoords => {
                        const directionUrl = `https://api.mapbox.com/directions/v5/mapbox/driving-traffic/${currentCoords}%3B${victimCoords}?alternatives=true&annotations=congestion&geometries=geojson&language=en&overview=full&steps=true&access_token=${mapboxgl.accessToken}`;
                        fetch(directionUrl)
                            .then(response => {
                                if (!response.ok)
                                    throw new Error("Error in Network : " + response.statusText);
                                return response.json();
                            })

                            .then(data => {
                                // Add a source and layer displaying the route.
                                map.addSource('route', {
                                    'type': 'geojson',
                                    'data': data.routes[0].geometry
                                });

                                map.addLayer({
                                    'id': 'route',
                                    'type': 'line',
                                    'source': 'route',
                                    'layout': {
                                        'line-join': 'round',
                                        'line-cap': 'round'
                                    },
                                    'paint': {
                                        'line-color': '#888',
                                        'line-width': 8
                                    }
                                });

                                // Add a circle layer
                                map.addLayer({
                                    'id': 'circle-layer-user',
                                    'type': 'circle',
                                    'source': {
                                        'type': 'geojson',
                                        'data': {
                                            'type': 'Feature',
                                            'geometry': {
                                                'type': 'Point',
                                                'coordinates': currentCoords
                                            }
                                        }
                                    },
                                    'paint': {
                                        // The circle radius is measured in pixels
                                        'circle-radius': 50,
                                        'circle-color': '#3C42FA',
                                        'circle-opacity': 0.3
                                    }
                                });


                                //add the markers 
                                const myLocation = new mapboxgl.Popup({
                                    offset: 40
                                }).setText('My Current Location');

                                new mapboxgl.Marker({
                                        color: "#1AFF00"
                                    })
                                    .setLngLat(currentCoords)
                                    .setPopup(myLocation)
                                    .addTo(map);

                            })
                            .catch(error => console.error('Error :', error));
                    })
                    .catch(error => {
                        console.error("Error getting location:", error);
                    });
            }
            // function to show the exact location 
            const dispatchLocation = (exactLocation, locationCategory) => {
                let exactLoc = document.querySelector('#exactLocation');
                let locCategory = document.querySelector('#locationCategory');

                exactLoc.innerHTML = exactLocation;
                locCategory.innerHTML = locationCategory;
            }

            //script for refresshing the map content 
            const getDirectionBtn = document.getElementById('getDirection');
            getDirectionBtn.addEventListener('click', () => {
                getUserDirection();
            });

            // script for changing the style of the button 
            let changeTheme = document.getElementById('changeTheme');
            changeTheme.addEventListener('click', () => {
                if (changeTheme.textContent == "Satellite View") {
                    map.setStyle('mapbox://styles/mapbox/satellite-v9');
                    changeTheme.innerHTML = "Street View";
                } else {
                    map.setStyle('mapbox://styles/mapbox/streets-v12');
                    changeTheme.innerHTML = "Satellite View";
                }
            });

        } else {
            console.error('Latitude and longitude not provided.');
        }
    </script>
</body>

</html>