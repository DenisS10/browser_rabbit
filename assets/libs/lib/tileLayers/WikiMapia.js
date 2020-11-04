L.WikiMapia = L.TileLayer.extend({
    getTileUrl: function (tilePoint) {
           var n = tilePoint.x % 4 + (tilePoint.y % 4) * 4;
        return this._url.replace('{num}', n)
            .replace('{x}', tilePoint.x)
            .replace('{y}', tilePoint.y)
            .replace('{z}', tilePoint.z);
    },
});

L.wikiMapia = function (key, options) {
    return new L.WikiMapia(key, options);
};