kernelservice = function ($service, $routeService, $cache, $thread) {
    
    function service($routeID, url, value, method) {
        var url = $routeService.Url($routeID, url, method);
        this.url = url;
        this.mehtod = mehtod;
        this.value = value;
        this.data =  $cache.get(url, value);
    }
    
    function request(routeID, method, data, callback) {
        var s;
        if (!data.url || !data.value) {
            return false;
        }
        switch (method) {
            case "GET":
                s = new service(routeID, data.url, data.value, method);
                break;
            case "POST":
                s = new service(routeID, data.url, data.value, method);
                break;
            case "DELETE":
                s = new service(routeID, data.url, data.value, method);
                break;
            default:
                return false;
                break;
        }
        if (s.data) {
            callback.success(s.data);
        } else {
            $thread.register(s, callback);
        }
        return true;
    }

    return  {
        get: (routeID, data, callback) => {
            new request(routeID, "GET", data, callback);
        },
        post: (routeID, data, callback) => {
            new request(routeID, "POST", data, callback);
        },
        delete: (routeID, data, callback) => {
            new request(routeID, "DELETE", data, callback);
        }
    };
};
module.exports = kernelservice;