// Javascript Web Socket Connection Class - Javascript
var Connection = (function(){
    // This Class' Parameter and statement when instance is created.
    function Connection(){

        this.status   = $("#deviceStatus");
        this.open     = false;
        this.socket   = new WebSocket("ws://" +location.host+ ":2000");
        this.setupConnectionEvents();
    }

    // The following are the functions of Connection Class
    Connection.prototype = {
        setUsername: function(){
            var data =
                JSON.stringify({
                    action: 'setname',
                    username: "adminPanel"
                });

            this.socket.send(data);
        },

        addSystemMessage: function (msg) {
            this.status.text(msg)
        },

        setupConnectionEvents: function(){
            var self = this;
            self.socket.onopen      = function(evt){ self.connectionOpen(evt) };
            self.socket.onmessage   = function(evt){ self.connectionMessage(evt) };
            self.socket.onclose     = function(evt){ self.connectionClose(evt); };
        },

        connectionOpen: function(evt){
            this.open = true;

            // Get Socket ID from the Socket Server
            this.setUsername();
        },

        connectionMessage: function(evt) {
            var data = JSON.parse(evt.data);
            if (!this.open){ return; }

            switch (data.action){
                case 'setname':
                    if (data.success){
                        //Do something in here
                    } else{
                        alert("AI: Sorry the server is already been used.");
                    }
                    break;
                case 'deviceConnected':
                    this.addSystemMessage("Device Connected.");
                    break;
                case 'add':
                    // TODO: updateThe Server and then bring the result back

                    ajaxUpdate.updateVisitor(data.amount, data.datetime, function (d) {
                        if (d !==  "ERROR"){
                            updateTodayGraph();
                        }else{
                            alert("A problem occur while gathering data from the server...")
                        }
                    });
                    break;
                case 'clientList':
                    // This is the array of connected users
                    clientList = JSON.parse(data.clients);
                    console.log(clientList);

                    // Check  if the hardwareDevice is conencted to the server
                    if (clientList.indexOf("hardwareDevice") !== -1){
                        this.addSystemMessage("Device Connected.");
                    }else{
                        this.addSystemMessage("Not Connected.");
                    }
                    break;
            }
        },

        // When the connection was closed
        connectionClose: function(evt){
            this.open = false;
            this.addSystemMessage("SockError");
        },

        // Request Data
        requestData: function(){
            if (this.open){
                var data = JSON.stringify ({
                    action: 'requestData'
                });

                this.socket.send(data);
            }
            else{
                // Error Message here...
            }
        }
    };
    return Connection;
})();
