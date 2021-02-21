var ac = {
  // (A) ATTACH AUTOCOMPLETE TO INPUT FIELD
  instances : [], // autocomplete instances
  attach : function (options) {
  // options
  //  target : ID of target field.
  //  data : suggestion data (ARRAY), or URL (STRING).
  //  delay : optional, delay before suggestion, default 500ms.
  //  post : optional, extra data to send to server
  //  min : optional, minimum characters to fire suggestion, default 2.

    // (A1) NEW AUTOCOMPLETE INSTANCE
    ac.instances.push({
      id : ac.instances.length, // Element position in ac.instances
      parent: document.getElementById(options.target).parentElement, // Input field parent
      target: document.getElementById(options.target), // Input field
      wrapper: document.createElement("div"), // Suggestion wrapper
      suggest: document.createElement("div"), // Suggestion box
      timer: null, // Autosuggest timer
      post: options.post ? options.post : null, // Extra parameters to POST to server
      delay: options.delay ? options.delay : 500, // Suggestion delay
      min: options.min ? options.min : 2, // Min characters
      data: options.data // Autocomplete data
    });
    let instance = ac.instances[ac.instances.length-1];
    
    // (A2) ATTACH AUTOCOMPLETE HTML
    instance.parent.insertBefore(instance.wrapper, instance.target);
    instance.wrapper.classList.add("acWrap");
    instance.wrapper.appendChild(instance.target);
    instance.wrapper.appendChild(instance.suggest);
    instance.suggest.classList.add("acSuggest");

    // (A3) KEY PRESS LISTENER
    instance.target.addEventListener("keyup", function(evt){
      // CLEAR OLD TIMER
      if (instance.timer != null) { window.clearTimeout(instance.timer); }
      
      // HIDE AND CLEAR OLD SUGGESTION BOX
      instance.suggest.innerHTML = "";
      instance.suggest.style.display = "none";

      // CREATE NEW SUGGESTION TIMER
      if (this.value.length >= instance.min) {
        // FETCH SUGGESTION DATA FROM SERVER
        if (typeof instance.data == "string") {
          instance.timer = setTimeout(
            function(){ ac.fetch(instance.id); }, instance.delay
          );
        }
        // SUGGESTION DATA FROM GIVEN ARRAY
        else {
          instance.timer = setTimeout(
            function(){ ac.filter(instance.id); }, instance.delay
          );
        }
      }
    });
  },
  
  // (B) DRAW SUGGESTIONS FROM ARRAY
  filter : function (id) {
    // (B1) GET INSTANCE + DATA
    let instance = ac.instances[id],
        search = instance.target.value.toLowerCase(),
        multi = typeof instance.data[0]=="object",
        results = [];

    // (B2) FILTER APPLICABLE SUGGESTIONS
    for (let i of instance.data) {
      let entry = multi ? i.D : i ;
      if (entry.toLowerCase().indexOf(search) != -1) {
        results.push(i);
      }
    }

    // (B3) DRAW SUGGESTIONS
    ac.draw(id, results.length==0 ? null : results);
  },
  
  // (C) AJAX FETCH SUGGESTIONS FROM SERVER
  fetch : function (id) {
    let instance = ac.instances[id],
        xhr = new XMLHttpRequest(),
        data = new FormData();
    data.append('search', instance.target.value);
    if (instance.post !== null) {
      for (let i in instance.post) {
        data.append(i, instance.post[i]);
      }
    }
    xhr.open('POST', instance.data);
    xhr.onload = function () {
      var results = JSON.parse(this.response);
      ac.draw(id, results.data);
    };
    xhr.send(data);
  },
  
  // (D) DRAW AUTOSUGGESTION
  open : null, // Currently open autocomplete
  draw : function (id, results) {
    // (D1) GET INSTANCE
    let instance = ac.instances[id];
    ac.open = id;

    // (D2) NO RESULTS
    if (results == null) { ac.close(); }

    // (D3) DRAW RESULTS 
    else {
      instance.suggest.innerHTML = "";
      let multi = typeof results[0]=="object",
          list = document.createElement("ul"), row, entry;
      for (let i of results) { 
        row = document.createElement("li");
        row.innerHTML = multi ? i.D : i;
        if (multi) {
          entry = {...i};
          delete entry.D;
          row.dataset.multi = JSON.stringify(entry);
        }
        row.addEventListener("click", function(){
          ac.select(id, this);
        });
        list.appendChild(row);
      }
      instance.suggest.appendChild(list);
      instance.suggest.style.display = "block";
    }
  },
  
  // (E) ON SELECTING A SUGGESTION
  select : function (id, el) {
    ac.instances[id].target.value = el.innerHTML;
    if (el.dataset.multi !== undefined) {
      let multi = JSON.parse(el.dataset.multi);
      for (let i in multi) {
        document.getElementById(i).value = multi[i];
      }
    }
    ac.close();
  },

  // (F) CLOSE AUTOCOMPLETE
  close : function () { if (ac.open != null) {
    let instance = ac.instances[ac.open];
    instance.suggest.innerHTML = "";
    instance.suggest.style.display = "none";
    ac.open = null;
  }},
  
  // (G) CLOSE AUTOCOMPLETE IF USER CLICKS ANYWHERE OUTSIDE
  checkclose : function (evt) {if (ac.open != null) {
    let instance = ac.instances[ac.open];
    if (instance.target.contains(evt.target)==false &&
        instance.suggest.contains(evt.target)==false) {
      ac.close();
    }
  }}
};
document.addEventListener("click", ac.checkclose);