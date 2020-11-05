var app = {
    globalMethods: {},
    globalVariables: {},
};
app.globalMethods = {
    generateUUID: function generateUUID() {
        var d = new Date().getTime();//Timestamp
        var d2 = (performance && performance.now && (performance.now()*1000)) || 0;//Time in microseconds since page-load or 0 if unsupported
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16;//random number between 0 and 16
            if(d > 0){//Use timestamp until depleted
                r = (d + r)%16 | 0;
                d = Math.floor(d/16);
            } else {//Use microseconds since page-load if supported
                r = (d2 + r)%16 | 0;
                d2 = Math.floor(d2/16);
            }
            return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
        });
    },
    initWebStomp: function initWebStomp() {
        app.globalVariables.tabUID = app.globalMethods.generateUUID();
        $.ajax({
            url: '/prepare-queues.php',
            type: 'post',
            data: {
                tabUID: app.globalVariables.tabUID
            },
            success: function (data) {
                if (data.queueName) {
                    app.globalVariables.webStompClient = webstomp.client(app.globalVariables.webStompUrl);
                    app.globalVariables.webStompClient.debug = app.globalMethods.webStompOnDebug;
                    app.globalVariables.webStompClient.connect(app.globalVariables.webStompLogin, app.globalVariables.webStompPassword, function () {
                        app.globalVariables.webStompClient.subscribe('/amq/queue/' + data.queueName, app.globalMethods.webStompOnMessageArrived);
                    });
                }
            }
        });

    },
    webStompOnDebug: function webStompOnDebug(data) {
        return;
    },
    webStompOnMessageArrived: function webStompOnMessageArrived(webStompMessage) {
        var message = JSON.parse(webStompMessage.body);
        if (message) {
            MapGlobal.createMarker(message.msg.lat, message.msg.lon);
            app.globalMethods.updateMessageInformationTable(message);
        }
    },
    updateMessageInformationTable: function (msg) {
        var rabbitMsgBox = $('#info_section').find('.rabbit-msg-box');
        rabbitMsgBox.html('<h2>RabbitMQ Message</h2>' + '<pre>' + JSON.stringify(msg, undefined, 4) + '</pre>');
debugger;
        app.globalMethods.updateHumanReadingTable(msg.msg.tm, msg.msg.course, msg.msg.speed, msg.msg.sats, msg.imei, msg.msg.lat, msg.msg.lon);
    },
    initUnitData: function () {
        $.ajax({
            url: '/load-unit-data.php', //get last_msg_unit row
            type: 'get',
            success: function (lmsg) {
                MapGlobal.createMarker(lmsg.lat, lmsg.lon);
                app.globalMethods.updateHumanReadingTable(lmsg.time, lmsg.course, lmsg.speed, lmsg.sats, lmsg.unit_id, lmsg.lat, lmsg.lon);
            }
        });
    },
    updateHumanReadingTable: function (time, course, speed, sats, imei, lat, lon) {
        var $table = $('#additional_info');
        $table.find('.datetime').find('td')
            .html(new Date(parseInt(time.toString() + '000')).toLocaleString("ru-RU")
                + ' ' + Intl.DateTimeFormat().resolvedOptions().timeZone
            );
        $table.find('.course').find('td').html(course);
        $table.find('.speed').find('td').html(speed);
        $table.find('.sats').find('td').html(sats);
        $table.find('.imei').find('td').html(imei);
        $table.find('.coords').find('td').html(lat + ', ' + lon);
    }
};
app.globalVariables = {
    webStompClient: null,
    webStompUrl: 'ws://192.168.1.123:15674/ws',
    webStompLogin: 'user',
    webStompPassword: '32dg6fg2v1e65gr',
};
