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
        console.log(webStompMessage);
        var message = JSON.parse(webStompMessage.body);
        if (message) {
            MapGlobal.createMarker(message);
            app.globalMethods.updateMessageInformationTable(message);
            console.log(message);

        }
    },
    updateMessageInformationTable: function (msg) {
        var bodyBox = $('#info_section').find('.body_box');
        bodyBox.html('<pre>' + JSON.stringify(msg) + '</pre>');
    }
};
app.globalVariables = {
    webStompClient: null,
    webStompUrl: 'ws://192.168.1.123:15674/ws',
    webStompLogin: 'user',
    webStompPassword: '32dg6fg2v1e65gr',
};
