class phpAPI {

  #configurations = {}

  constructor(url){
    const self = this
    self.#configurations = {
      "url": "api.php",
      "dataType": "json",
      "context": self,
      "beforeSend": function(xhr){},
      "complete": function(xhr,status){},
      "error": function(xhr,status,error){},
      "success": function(result,status,xhr){},
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
    $.ajax(configurations)
    return self
  }

  post(url = null, data = null, config = null){
    const self = this
    let configurations = {}
    for(const [key, value] of Object.entries(self.#configurations)){
      configurations[key] = value;
    }
    if(url != null && typeof url === 'string'){ configurations.url = configurations.url+'/'+url; }
    if(data != null && typeof data === 'object'){
      if(typeof data.beforeSend === 'undefined' && typeof data.complete === 'undefined' && typeof data.error === 'undefined' && typeof data.success === 'undefined'){
        configurations.data = JSON.stringify(data);
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
    $.ajax(configurations)
    return self
  }
}
