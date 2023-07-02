var map;

window.onload = function() {
    //initMap();
}


function initMap() {
    getClinicLocation(function(setLocation) {
        //alert("return: " + setLocation.rda);
        var map = L.map('map', {
        scrollWheelZoom: false,
        }).setView([setLocation.lat, setLocation.lng], 17);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 21,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
      //var marker = L.marker([5.262483336754534, 103.16571156556414]).addTo(map);
        var passLatLng = new L.LatLng(setLocation.lat, setLocation.lng);
        initCrosshair(map, passLatLng);
        initRadius(map, setLocation);
    });
}

var crosshairIcon = L.icon({
iconUrl: 'media/crosshair.png',
iconSize: [40, 40], // size of the icon
iconAnchor: [10, 10], // point of the icon which will correspond to marker's location
});

var crosshair;

function initCrosshair(map, passLatLng) {
    crosshair = new L.marker(passLatLng, { icon: crosshairIcon, clickable: false, interactive: false });
    crosshair.addTo(map);

    // Move the crosshair to the center of the map when the user pans
    map.on('move', function(e) {
        crosshair.setLatLng(map.getCenter());
    });
}

function getClinicLocation(callback) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
        var response = xmlhttp.responseText;
        try {
            var temp = JSON.parse(response);
            callback(temp);
        } catch (error) {
            alert("Error: " + error);
        }
    }
    };

    var method = "getClinicLocation";
    var url = "manage clinic.php";
    url += "?method=" + encodeURIComponent(method);

    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

function setClinicLocation(){
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
        var response = xmlhttp.responseText;
        try {
            var temp = JSON.parse(response);
            alert(temp.response);
        } catch (error) {
            alert("Error: " + error);
        }
    }
    };

    var method = "setClinicLocation";
    var currentLatLng = "currentLatLng=" + encodeURIComponent(crosshair.getLatLng());
    var newRadius = "newRadius=" + encodeURIComponent(document.getElementById("myRange").value);
    var url = "manage clinic.php";
    url += "?method=" + encodeURIComponent(method) + "&" + currentLatLng + "&" + newRadius;

    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

var circle;
function initRadius(map, setLocation){
    circle = L.circle(map.getCenter(), {
        color: '#9370db',
        weight: 0.1,
        fillColor: '#9370db',
        fillOpacity: 0.4,
        radius: setLocation.rad
    }).addTo(map);

    map.on('move', function(e) {
        circle.setLatLng(map.getCenter());
    });

    var slider = document.getElementById("myRange");
    slider.value = setLocation.rad;
    var output = document.getElementById("demo");
    output.innerHTML = slider.value;

    slider.oninput = function() {
    output.innerHTML = this.value;
    circle.setRadius(this.value); // Sets the radius of the circle to be the value of the slider
    }

    function clickCircle(e) {
    var clickedCircle = e.target;
    }
}
