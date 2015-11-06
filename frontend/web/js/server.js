var Server = function(options){
    if(typeof options === 'undefined'){
        options = {};
    }
    var defaultOptions  = {
        _connect : null,
        address : 'ws://localhost',
        port : 3084,
        onopen : function(){},
        onmessage : function(){},
        onerror : function(){},
        onclose : function(){}
    };
    options = $.extend(defaultOptions, options);
    $.extend(this, options);
};

Server.prototype = {
    connect : function(){
        this._connect = new WebSocket(this.address+':'+this.port);
        this._connect.onopen = this.onopen;
        this._connect.onmessage = this.onmessage;
        this._connect.onerror = this.onerror;
        this._connect.onclose = this.onclose;

        /*var self = this;
        window.onbeforeunload = function() {
            self._connect.onclose = function () {};
            self._connect.close();
        };*/
    },
    send : function(data){
        this._connect.send(JSON.stringify(data));
    }
};