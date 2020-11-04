L.VirtualEarth = L.TileLayer.extend({
	options: {
		subdomains: [0, 1, 2, 3],
		type: 'Aerial',
		attribution: 'VirtualEarth',
		culture: 'en-us'
	},

		getTileUrl: function (tilePoint) {
		//var zoom = this._getZoomForUrl();
		var subdomains = this.options.subdomains,
			s = this.options.subdomains[Math.abs((tilePoint.x + tilePoint.y) % subdomains.length)];
		return this._url.replace('{subdomain}', s)
				.replace('{quadkey}', this.tile2quad(tilePoint.x, tilePoint.y, tilePoint.z))
				.replace('{culture}', this.options.culture);
	},
	tile2quad: function (x, y, z) {
		var quad = '';
		for (var i = z; i > 0; i--) {
			var digit = 0;
			var mask = 1 << (i - 1);
			if ((x & mask) !== 0) digit += 1;
			if ((y & mask) !== 0) digit += 2;
			quad = quad + digit;
		}
		return quad;
	},
});

L.virtualEarth = function (key, options) {
    return new L.VirtualEarth(key, options);
};
