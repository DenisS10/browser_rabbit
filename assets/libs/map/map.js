//Embed Yandex-map
ymaps.ready(init);
var dprtsMap;

//Кординаты Минск
location4 = [53.905583, 27.502692];


function init(){     
    dprtsMap = new ymaps.Map("map", {
        center: [53.905583, 27.502692],
        zoom: 4,
        controls: ['zoomControl']
    });

    icon = {
        // Опции.
        // Необходимо указать данный тип макета.
        iconLayout: 'default#image',
        // Своё изображение иконки метки.
        iconImageHref: 'assets/img/ic_map_pin_default.svg',
        // Размеры метки.
        iconImageSize: [56, 56],
        // Смещение левого верхнего угла иконки относительно
        // её "ножки" (точки привязки).
        iconImageOffset: [-28, -48]
    };

    dprtsMap.behaviors.disable('scrollZoom');
    dprtPlaceMark4 = new ymaps.Placemark(location4, {
        hintContent   : 'Минский офис',
    }, icon);


    dprtsMap.geoObjects.add(dprtPlaceMark4);
}

$(function() {
    $('.minsk').click(function() {
        dprtsMap.setCenter(location4, 14);
        return '#map';
    });
});
