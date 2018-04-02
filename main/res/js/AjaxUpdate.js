// Javascript Web Socket Connection Class - Javascript
var AjaxUpdate = (function(){

    // This Class' Parameter and statement when instance is created.
    function AjaxUpdate(){

    }

    // The following are the functions of Connection Class
    AjaxUpdate.prototype = {

        getYearVisitor: function (year, callback) {
            $.post('getYearVisitor', {
                "year": year
            }).done(function(data){
                // Invoke the callback function here
                if(callback !== null)  {
                    callback(data);
                }
            }).fail(function(xhr, status, error) {
                alert("Error: A connection distortion occur while trying to get data from /getYearVisitor");
            });
        },

        getAllYears: function (callback) {
            $.post('getAllYears', {}).done(function(data){
                // Invoke the callback function here
                if(callback !== null)  {
                    callback(data);
                }
            }).fail(function(xhr, status, error) {
                alert("Error: A connection distortion occur while trying to get data from /getAllYears");
            });
        },
        getIntervalVisitor: function (start, end, callback) {
            $.post('getIntervalVisitor', {
                "start": start,
                "end": end
            }).done(function (data) {
                // Invoke the callback function here
                if (callback !== null) {
                    callback(data);
                }
            }).fail(function (xhr, status, error) {
                alert("Error: A connection distortion occur while trying to get data from /getIntervalVisitor");
            });
        },

        getWeeklyVisitor: function (interval, month, year, callback) {
            $.post('getWeeklyVisitor', {
                "interval": interval,
                "month": month,
                "year": year
            }).done(function (data) {
                // Invoke the callback function here
                if (callback !== null) {
                    callback(data);
                }
            }).fail(function (xhr, status, error) {
                alert("Error: A connection distortion occur while trying to get data from /getWeeklyVisitor");
            });
        },
        
        getTodayVisitor: function (callback) {
            $.post('getTodayVisitor', {}).done(function (data) {
                // Invoke the callback function here
                if (callback !== null) {
                    callback(data);
                }
            }).fail(function (xhr, status, error) {
                alert("Error: A connection distortion occur while trying to get data from /getTodayVisitor");
            });
        },

        updateVisitor: function (amount, datetime, callback) {
            $.post('updateVisitor', {
                "datetime": datetime,
                "amount": amount

            }).done(function (data) {
                // Invoke the callback function here
                if (callback !== null) {
                    callback(data);
                }
            }).fail(function (xhr, status, error) {
                alert("Error: A connection distortion occur while trying to get data from /updateVisitor");
            });
        },
        getPrintInterface: function (year, callback) {
            $.get('printInterface/'+year).done(function (data) {
                // Invoke the callback function here
                if (callback !== null) {
                    callback(data);
                }
            }).fail(function (xhr, status, error) {
                alert("Error: A connection distortion occur while trying to get data from /printInterface");
            });

        },
        getByURL: function (url, callback) {
            $.get(url).done(function (data) {
                // Invoke the callback function here
                if (callback !== null) {
                    callback(data);
                }
            }).fail(function (xhr, status, error) {
                alert("Error: A connection distortion occur while trying to get data from "+url);
            });
        },
        addEmployee: function (username, password, callback) {
            $.post('addEmployee', {
                "username": username,
                "password": password

            }).done(function (data) {
                // Invoke the callback function here
                if (callback !== null) {
                    callback(data);
                }
            }).fail(function (xhr, status, error) {
                alert("Error: A connection distortion occur while trying to get data from /addEmployee");
            });
        },
        editEmployee: function (oldUsername, username, password, callback) {
            $.post('editEmployee', {
                "oldUsername": oldUsername,
                "username": username,
                "password": password

            }).done(function (data) {
                // Invoke the callback function here
                if (callback !== null) {
                    callback(data);
                }
            }).fail(function (xhr, status, error) {
                alert("Error: A connection distortion occur while trying to get data from /addEmployee");
            });
        },
        deleteEmployee: function (username, callback) {
            $.post('deleteEmployee', {
                "username": username
            }).done(function (data) {
                // Invoke the callback function here
                if (callback !== null) {
                    callback(data);
                }
            }).fail(function (xhr, status, error) {
                alert("Error: A connection distortion occur while trying to get data from /addEmployee");
            })
        },
        changePassword: function (username, password, newPassword, callback) {
            $.post('changePassword', {
                "username": username,
                "password": password,
                "newPassword": newPassword
            }).done(function (data) {
                // Invoke the callback function here
                if (callback !== null) {
                    callback(data);
                }
            }).fail(function (xhr, status, error) {
                alert("Error: A connection distortion occur while trying to get data from /addEmployee");
            })
        }

    };
    return AjaxUpdate;
})();
