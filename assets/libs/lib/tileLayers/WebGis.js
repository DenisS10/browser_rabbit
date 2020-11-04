L.WebGisLayer = L.TileLayer.extend({
	getTileUrl: function (tilePoint) {
	    var correction = {
			18: -1,
			17:  0,
			16:  1,
			15:  2,
			14:  3,
			13:  4,
			12:  5,
			11:  6,
			10:  7,
			9:  8,
			8:  9,
			7:  10,
			6:  11,
			5:  12,
			4:  13,
			3: 14
		};
		return this._url
				.replace('{x}', tilePoint.x)
				.replace('{y}', tilePoint.y)
				.replace('{z}',correction[tilePoint.z]);
		},
});

L.webGisLayer = function (key, options) {
    return new L.WebGisLayer(key, options).addTo(MapGlobal.map);
};
