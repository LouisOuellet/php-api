class phpAPI {

  #configurations = {}

  constructor(url){
    const self = this
    self.#configurations = {
      "url": "/api.php",
      "dataType": "json",
      "context": self,
      "expiration": 3600,
      "cache": false,
      "beforeSend": function(xhr){},
      "complete": function(xhr,status){},
      "error": function(xhr,status,error){},
      "success": function(result,status,xhr){},
    }
  }

  setDefaults(callbacks){
    const self = this
    for(const [name, callback] of Object.entries(callbacks)){
      switch(name){
        case"beforeSend":
        case"complete":
        case"error":
        case"success":
          if(typeof callback === 'function' && typeof self.#configurations[name] !== 'undefined'){
            self.#configurations[name] = callback
          }
          break
      }
    }
  }

  setAuth(type = null, username = null, password = null){
    const self = this
    if(type != null && typeof type === 'string'){
      switch(type.toUpperCase()) {
        case"BEARER":
          if(username != null && typeof username === 'string'){
            self.#configurations.headers = {
              'Content-Type': 'application/x-www-form-urlencoded',
              'Authorization': 'Bearer '+btoa(username)
            }
          }
          break;
        case"BASIC":
          if(username != null && password != null && typeof username === 'string' && typeof password === 'string'){
            self.#configurations.username = btoa(username)
            self.#configurations.password = btoa(password)
          }
          break;
      }
    }
    return self
  }

  get(url = null, data = null, config = null){
    const self = this
    let configurations = {}
    for(const [key, value] of Object.entries(self.#configurations)){
      configurations[key] = value;
    }

    // Check for cached data
    let cacheKey = this.generateCacheKey(url, data);
    if (configurations.cache) {
      const cachedData = this.getCache(cacheKey);
      if (cachedData) {
        // Call success callback with cached data
        if (config && config.success) config.success(cachedData);
        return self;
      }
    }

    // Prepare request
    if(url != null && typeof url === 'string'){ configurations.url = configurations.url+'/'+url; }
    if(data != null && typeof data === 'object'){
      if(typeof data.beforeSend === 'undefined' && typeof data.complete === 'undefined' && typeof data.error === 'undefined' && typeof data.success === 'undefined'){
        configurations.data = JSON.stringify(data);
      } else { config = data; }
    }
    configurations.type = "GET"
    if(config != null && typeof config === 'object'){
      for(const [key, value] of Object.entries(config)){
        if(typeof configurations[key] !== 'undefined'){
          switch(key){
            case"beforeSend":
            case"complete":
            case"error":
            case"success":
              configurations[key] = value;
              break;
          }
        }
      }
    }

    // Set cache after successful ajax call
    const originalSuccess = configurations.success;
    configurations.success = function(result, status, xhr) {
      if (configurations.cache) {
        self.setCache(cacheKey, result);
      }
      if (originalSuccess) originalSuccess(result, status, xhr);
    }

    // Make request
    $.ajax(configurations)

    // Return
    return self
  }

  post(url = null, data = null, config = null){
    const self = this
    let configurations = {}
    for(const [key, value] of Object.entries(self.#configurations)){
      configurations[key] = value;
    }

    // Check for cached data
    let cacheKey = this.generateCacheKey(url, data);
    if (configurations.cache) {
      const cachedData = this.getCache(cacheKey);
      if (cachedData) {
        // Call success callback with cached data
        if (config && config.success) config.success(cachedData);
        return self;
      }
    }

    // Prepare request
    if(url != null && typeof url === 'string'){ configurations.url = configurations.url+'/'+url; }
    if(data != null && typeof data === 'object'){
      if(typeof data.beforeSend === 'undefined' && typeof data.complete === 'undefined' && typeof data.error === 'undefined' && typeof data.success === 'undefined'){
        configurations.data = {};
        for(const [key, value] of Object.entries(data)){
          configurations.data[key] = encodeURI(btoa(value))
        }
      } else { config = data; }
    }
    configurations.type = "POST"
    if(config != null && typeof config === 'object'){
      for(const [key, value] of Object.entries(config)){
        switch(key){
          case"beforeSend":
          case"complete":
          case"error":
          case"success":
            configurations[key] = value;
            break;
        }
      }
    }
    
    // Set cache after successful ajax call
    const originalSuccess = configurations.success;
    configurations.success = function(result, status, xhr) {
      if (configurations.cache) {
        self.setCache(cacheKey, result);
      }
      if (originalSuccess) originalSuccess(result, status, xhr);
    }

    // Make request
    $.ajax(configurations)

    // Return
    return self
  }

  getCache(key) {
    const cached = JSON.parse(localStorage.getItem(key));
    if (!cached) return null;
    if ((new Date().getTime() - cached.timestamp) / 1000 > this.#configurations.expiration) {
      localStorage.removeItem(key);
      return null;
    }
    return cached.data;
  }

  setCache(key, data) {
    const cacheData = {
      data: data,
      timestamp: new Date().getTime()
    };
    localStorage.setItem(key, JSON.stringify(cacheData));
  }

  clearCache(key) {
    localStorage.removeItem(key);
  }
}
