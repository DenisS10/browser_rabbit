'use strict';

$(function () {
    initLeafletMaps();
    app.globalMethods.initUnitData();
    app.globalMethods.initWebStomp();
});

var MapGlobal = {};
MapGlobal.centerMap = [53.902527, 27.554153];
// MapGlobal.map = L.map('map_div', {zoomControl: false}).setView(MapGlobal.centerMap, 13);
var initLeafletMaps = function initializeLeafletMaps() {
    MapGlobal.map = L.map('map_gps', {zoomControl: false}).setView(MapGlobal.centerMap, 13);
    MapGlobal.markerGroup = L.layerGroup().addTo(MapGlobal.map);
    L.control.zoom({
        position: 'topleft'
    }).addTo(MapGlobal.map);
    var options = {
        position: 'topleft',            // Position to show the control. Values: 'topright', 'topleft', 'bottomright', 'bottomleft'
        unit: 'metres',                 // Show imperial or metric distances. Values: 'metres', 'landmiles', 'nauticalmiles'
        clearMeasurementsOnStop: true,  // Clear all the measurements when the control is unselected
        showBearings: false,            // Whether bearings are displayed within the tooltips
        bearingTextIn: 'In',            // language dependend label for inbound bearings
        bearingTextOut: 'Out',          // language dependend label for outbound bearings
        tooltipTextFinish: 'Click to <b>finish line</b><br>',
        tooltipTextDelete: 'Press SHIFT-key and click to <b>delete point</b>',
        tooltipTextMove: 'Click and drag to <b>move point</b><br>',
        tooltipTextResume: '<br>Press CTRL-key and click to <b>resume line</b>',
        tooltipTextAdd: 'Press CTRL-key and click to <b>add point</b>',
        // language dependend labels for point's tooltips
        measureControlTitleOn: 'Включить линейку',   // Title for the control going to be switched on
        measureControlTitleOff: 'Выключить линейку', // Title for the control going to be switched off
        measureControlLabel: '&#8614;', // Label of the Measure control (maybe a unicode symbol)
        measureControlClasses: [],      // Classes to apply to the Measure control
        showClearControl: false,        // Show a control to clear all the measurements
        clearControlTitle: 'Clear Measurements', // Title text to show on the clear measurements control button
        clearControlLabel: '&times',    // Label of the Clear control (maybe a unicode symbol)
        clearControlClasses: [],        // Classes to apply to clear control button
        showUnitControl: false,         // Show a control to change the units of measurements
        distanceShowSameUnit: false,    // Keep same unit in tooltips in case of distance less then 1 km/mi/nm
        unitControlTitle: {             // Title texts to show on the Unit Control button
            text: 'Change Units',
            metres: 'metres',
            landmiles: 'land miles',
            nauticalmiles: 'nautical miles'
        },
        unitControlLabel: {             // Unit symbols to show in the Unit Control button and measurement labels
            metres: 'm',
            kilometres: 'km',
            feet: 'ft',
            landmiles: 'mi',
            nauticalmiles: 'nm'
        },
        tempLine: {                     // Styling settings for the temporary dashed line
            color: '#00f',              // Dashed line color
            weight: 2                   // Dashed line weight
        },
        fixedLine: {                    // Styling for the solid line
            color: '#006',              // Solid line color
            weight: 2                   // Solid line weight
        },
        startCircle: {                  // Style settings for circle marker indicating the starting point of the polyline
            color: '#000',              // Color of the border of the circle
            weight: 1,                  // Weight of the circle
            fillColor: '#0f0',          // Fill color of the circle
            fillOpacity: 1,             // Fill opacity of the circle
            radius: 3                   // Radius of the circle
        },
        intermedCircle: {               // Style settings for all circle markers between startCircle and endCircle
            color: '#000',              // Color of the border of the circle
            weight: 1,                  // Weight of the circle
            fillColor: '#ff0',          // Fill color of the circle
            fillOpacity: 1,             // Fill opacity of the circle
            radius: 3                   // Radius of the circle
        },
        currentCircle: {                // Style settings for circle marker indicating the latest point of the polyline during drawing a line
            color: '#000',              // Color of the border of the circle
            weight: 1,                  // Weight of the circle
            fillColor: '#f0f',          // Fill color of the circle
            fillOpacity: 1,             // Fill opacity of the circle
            radius: 3                   // Radius of the circle
        },
        endCircle: {                    // Style settings for circle marker indicating the last point of the polyline
            color: '#000',              // Color of the border of the circle
            weight: 1,                  // Weight of the circle
            fillColor: '#f00',          // Fill color of the circle
            fillOpacity: 1,             // Fill opacity of the circle
            radius: 3                   // Radius of the circle
        },
    };
    // L.control.polylineMeasure(options).addTo(MapGlobal.map);
    // new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 18}).addTo(MapGlobal.map);
    // L.webGisLayer('https://tile.geliospro.net/gmaps/{x}_{y}_{z}.png', {maxZoom: 18});

// MapGlobal.map = L.map('map', {zoomControl: false}).setView(MapGlobal.centerMap, 13);

// L.control.zoom({
//     position:'bottomleft',
//     zoomInTitle: yii.t('Zoom in'),
//     zoomOutTitle: yii.t('Zoom out')
// }).addTo(MapGlobal.map);

// MapGlobal.map.attributionControl.setPrefix('');
// var osm = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(MapGlobal.map);
// // create a tile layer sourced from mapbox
// //var osm = L.tileLayer('https://api.tiles.mapbox.com/v4/kirnet.cf0e9369/{z}/{x}/{y}.png?access_token=pk.eyJ1Ijoia2lybmV0IiwiYSI6IjQ0MDJhY2U5YzM2ZWMyYTFkNzNhMGFlZDRkOGM2M2UyIn0.UwYnDZYOwoBVDu6Ojj1ofA').addTo(MapGlobal.map);
// var googleApiKey = (MapGlobal.customMaps && MapGlobal.customMaps.apiKeys && MapGlobal.customMaps.apiKeys.google && MapGlobal.customMaps.apiKeys.google.length) ? '&key=' + MapGlobal.customMaps.apiKeys.google : '';
// var yaLang = language.substr(0,2);
    var yaLang = 'ru',
        yandexApiKey = 'AOTbsl0BAAAAEDT7WwMAms7mgXP6g7KwZzbBLPBQm-eLdkYAAAAAAAAAAADqGMuL7yZ0ZfYExiJRk5LP_hmcfA==',
        yandexTileLayerUrl = 'js/lib/leaflet-plugins-3.0.3/layer/tile/Yandex.js';
// MapGlobal.mapsHere = here;
// MapGlobal.mapsBox = mapBox;
// MapGlobal.googleTraffic = googleTraffic;
    var layerdefs = {
        osm: {
            name: "OSM", js: [],
            init: function () {
                return new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 18});
            }
        },//
        googleTraffic: {
            name: "Google Traffic", js: [],
            init: function () {
                return new L.TileLayer('https://{s}.google.com/vt/lyrs=m,traffic&x={x}&y={y}&z={z}&hl=', {
                    maxZoom: 18,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
                });
            }
        },
        openTopoMap: {
            name: "OpenTopoMap", js: [],
            init: function () {
                return new L.TileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {maxZoom: 17});
            }
        },
        // mapnik: { name: "Mapnik", js: [],
        //     init: function() {return new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');}},
        // kosmo: { name: "Космоснимки", js: [],
        //     init: function() {return new L.TileLayer('https://{s}.tile.kosmosnimki.ru/kosmo/{z}/{x}/{y}.png', {attribution:'Tiles Courtesy of <a href="http://kosmosnimki.ru/" target="_blank">kosmosnimki.ru</a>'});}},
        // ghybrid: { name: "G-Спутник", js: [],
        //     init: function() {return new L.GridLayer.GoogleMutant({type: 'hybrid', maxZoom:20}); }},
        // groad: { name: "Google", js: [],
        //     init: function() {return new L.GridLayer.GoogleMutant({type: 'roadmap', maxZoom:20}); }},
        ymap: {
            name: "Yandex", js: [yandexTileLayerUrl, yandexApiKey ?
                // "https://enterprise.api-maps.yandex.ru/2.0/?lang=" + yaLang + "&apikey=" + yandexApiKey
                "https://api-maps.yandex.ru/2.0/?lang=" + yaLang + "&apikey=" + yandexApiKey
                : "https://api-maps.yandex.ru/2.0/?load=package.map&lang=" + yaLang],
            init: function () {
                return new L.Yandex("map");
            }
        },
        nyak: {
            name: "Яндекс народная",
            js: [yandexTileLayerUrl, "https://api-maps.yandex.ru/2.0/?load=package.map&lang=" + yaLang],
            init: function () {
                return new L.Yandex("publicMap");
            }
        },
        yhybrid: {
            name: "Яндекс народная",
            js: [yandexTileLayerUrl, "https://api-maps.yandex.ru/2.0/?load=package.map&lang=" + yaLang],
            init: function () {
                return new L.Yandex("hybrid");
            }
        },
        // defsbing : { name: "Bing", js: [bingTileLayerUrl],
        //     init: function() {return new L.BingLayer(''+bingApiKey+'',{type:'ROAD'})}
        // },
        traffic: {
            name: "Яндекс пробки",
            js: [yandexTileLayerUrl, "https://api-maps.yandex.ru/2.0/?load=package.map&lang=" + yaLang],
            init: function () {
                return new L.Yandex("map", {traffic: true});
            },
            overlay: true
        },
        // geliosSoft: {
        //     name: 'GeliosSoft',
        //     js: [],
        //     init: function () {
        //         return new L.TileLayer('https://tile.geliospro.net/osm/planet/{z}/{x}/{y}.png');
        //     }
        // },
        webGis: {
            name: "webGis", js: [],
            init: function () {
                return new L.WebGisLayer('https://tile.geliospro.net/gmaps/{x}_{y}_{z}.png', {maxZoom: 18});
            }
        },
        virtualEarthOblique: {
            name: "virtualEarthOblique", js: [],
            init: function () {
                return new L.VirtualEarth('http://ecn.t{subdomain}.tiles.virtualearth.net/tiles/r{quadkey}.jpeg?g=658&mkt=en-us&shading=hill&stl=H', {maxZoom: 18});
            }
        },
        virtualEarthHybrid: {
            name: "virtualEarthHybrid", js: [],
            init: function () {
                return new L.VirtualEarth('http://ecn.t{subdomain}.tiles.virtualearth.net/tiles/h{quadkey}.jpeg?g=658&mkt=en-us&shading=hill&stl=H', {maxZoom: 18});
            }
        },
        virtualEarthAerial: {
            name: "virtualEarthAerial", js: [],
            init: function () {
                return new L.VirtualEarth('http://ecn.t{subdomain}.tiles.virtualearth.net/tiles/a{quadkey}.jpeg?g=658&mkt=en-us&shading=hill&stl=H', {maxZoom: 18});
            }
        },
        // wikimapia: { name: "wikimapia", js: [],
        //     init: function() {return L.wikiMapia('http://i{num}.wikimapia.org/?x={x}&y={y}&zoom={z}&r=9826022&type=map&lng=0', {maxZoom: 18});}},
    };
// MapGlobal.layerdefs = layerdefs;
    var
        osm = new L.DeferredLayer(layerdefs.osm),
        googleTraffic = new L.DeferredLayer(layerdefs.googleTraffic),
        openTopoMap = new L.DeferredLayer(layerdefs.openTopoMap),
        // yndx = new L.DeferredLayer(layerdefs.ymap),
        // kosmo = new L.DeferredLayer(layerdefs.kosmo),
        // google = new L.DeferredLayer(layerdefs.groad),
        ytraffic = new L.DeferredLayer(layerdefs.traffic),
        yaHybrid = new L.DeferredLayer(layerdefs.yhybrid),
        // mso = new L.DeferredLayer(layerdefs.mso),
        // googleHybrid = new L.DeferredLayer(layerdefs.ghybrid),
        nk = new L.DeferredLayer(layerdefs.nyak),
        // bing = new L.DeferredLayer(layerdefs.defsbing),
        // sm = new L.TileLayer('https://tiles.openseamap.org/seamark/{z}/{x}/{y}.png'),
        // tollRoads = L.tileLayer('https://tile.geliospro.net/osm/toll-roads/{z}/{x}/{y}.png'),
        // tollRoadsPlaton = L.tileLayer('https://tile.geliospro.net/osm/platon/{z}/{x}/{y}.png'),
        // tollRoadsBeltoll = L.tileLayer('https://tile.geliospro.net/osm/beltoll/{z}/{x}/{y}.png'),

// wikimapia = new L.DeferredLayer(layerdefs.wikimapia),
        webGis = new L.DeferredLayer(layerdefs.webGis),
        virtualEarthOblique = new L.DeferredLayer(layerdefs.virtualEarthOblique),
        virtualEarthHybrid = new L.DeferredLayer(layerdefs.virtualEarthHybrid),
        virtualEarthAerial = new L.DeferredLayer(layerdefs.virtualEarthAerial);


// sm.setZIndex(20);
    var baseLayers = {
        'OSM': osm,
        'Google Traffic': googleTraffic,
        'OpenTopoMap': openTopoMap,
        // //"Kosmo":kosmo,
        // "Google":google,
        // "G-Hybrid": googleHybrid,
        // 'G-traffic':googleTraffic,
        // "Yandex": yndx,
        // 'Яндекс народная': nk,
        // "Яндекс пробки": ytraffic,
        // 'Яндекс гибрид': yaHybrid,
        // 'Bing':bing,
        // 'Here':here,
        // 'MapBox':mapBox,
        'WebGis': webGis,
        'VirtualEarth': virtualEarthOblique,
        'VirtualEarthHybrid': virtualEarthHybrid,
        'VirtualEarthAerial': virtualEarthAerial,
        // 'WikiMapia': wikimapia,
        // 'GeliosSoft': new L.DeferredLayer(layerdefs.geliosSoft)
    };

    var customLayerIndex = 1,
        customTileURIs = [];
    for (var customLayerName in customTileURIs) {
        baseLayers['custom_layer_' + customLayerIndex] = L.tileLayer(customTileURIs[customLayerName]);
        customLayerIndex++;
    }
    osm.addTo(MapGlobal.map); // default map
    L.control.layers(baseLayers).addTo(MapGlobal.map);
};
/*

{
  "imei": "867857034733324",
  "msg": {
    "tm_recv": 1604566402,
    "height": 140,
    "course": 20,
    "lat": 53.31398773,
    "speed": 18,
    "sats": 15,
    "lon": 34.28489685,
    "unit_id": "867857034733324",
    "unit_type": "arnavi_v4",
    "tm": 1604566398,
    "params": "OUT0:0;OUT1:0;OUT2:0;OUT3:0;IN4:0;IN5:0;IN6:0;move:1;IN0:0;IN1:0;IN2:1;IN3:0;BU:13823;sim_st:1;GSM:3;st0:0;st1:0;st2:1;rbU:2;sim:0;IN7:0;hdop:0.71;RBU:4057;GPS:3;bU:13800;"
  },
  "type": "messages",
  "sendTm": 1604566402,
  "unitId": 124617
}
*/
MapGlobal.createMarker = function (lat, lon) {
    if (this.marker) {
        this.marker.removeFrom(MapGlobal.map);
    }
    if(!lat || !lon){
        return false;
    }
    var icon_path = '/assets/img/ufo.png';
    var icon = L.icon({iconUrl: icon_path, iconSize: [32, 32], iconAnchor: [12, 12]});
    this.marker = L.marker([lat, lon], {
        icon: icon,
        title: 'Car'
    });
    this.marker.addTo(MapGlobal.markerGroup);
    MapGlobal.map.setView([lat, lon]);
};




