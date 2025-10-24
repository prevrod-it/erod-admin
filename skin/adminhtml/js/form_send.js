window.addEventListener("DOMContentLoaded", function() {

  /*----------  SEND FORM  ----------*/
  var formSend = function(ev) {

    var curform = this.form;
    var procscript = curform.getAttribute("action");
    var loadinngmsg = "A processar os dados...";
    var formfields =  new FormData(curform);
    formfields.append("reqcode", "b86027f4f0b60cf0234557b55744a9bf6ecf26f71df497e8533c721e1c85ec6d");

    var formsend = new XMLHttpRequest();
    var url = "/app/code/processing/" + procscript;
    formsend.open("POST", url, true);
    formsend.send(formfields);
    formsend.onreadystatechange = function() {
      var fstatus = curform.querySelector("#form_status");       
      if(formsend.readyState < 4) {
        fstatus.innerHTML = loadinngmsg;
        if (hasClass(fstatus, ".d-none")) {
          fstatus.classList.remove("d-none");
          fstatus.classList.add("d-block");
        }
      } else if (formsend.readyState == 4) {
        var formsendstatus = JSON.parse(this.responseText);
        if (formsendstatus[0][0] == 1) {
          if (hasClass(fstatus, ".d-block")) {
            fstatus.classList.remove("d-block");
            fstatus.classList.add("d-none");
          }
          if (hasClass(fstatus, ".alert-secondary")) {
            fstatus.classList.remove("alert-secondary","text-secondary");
          }
          fstatus.classList.add("alert-success","text-success");
          fstatus.innerHTML = "<i class=\"fas fa-check mr-3\"></i> " + formsendstatus[0][1];
          if (hasClass(fstatus, ".d-none")) {
            fstatus.classList.remove("d-none");
            fstatus.classList.add("d-block");
          }
        } else {
          var allfleldstatus = formsendstatus.slice(1, formsendstatus.length);
          var fid = "";
          var fwid = "";
          allfleldstatus.forEach(function(fieldstatus) {
            fid = "#" + fieldstatus[0];
            fwid = fid + "_warn";
            var objfid = curform.querySelector(fid);
            var objwanr = curform.querySelector(fwid);
            objfid.classList.add("is-invalid");
            objwanr.innerHTML = fieldstatus[1];   
          });

          if (hasClass(fstatus, ".alert-secondary")) {
            fstatus.classList.remove("alert-success","text-success");
          }
          fstatus.classList.add("alert-danger","text-danger");
          fstatus.innerHTML = "<i class=\"fas fa-exclamation-triangle mr-3\"></i> " + formsendstatus[0][1];
          if (hasClass(fstatus, ".d-none")) {
            fstatus.classList.remove("d-none");
            fstatus.classList.add("d-block");
          }
        }

        var resetmode = "resetstatus"; //Default
        if (hasClass(curform, ".clear-after-submit")) {
          resetmode = "clearall";
        } else if (hasClass(curform, ".refresh-after-submit")) {
          resetmode = "refreshpage";
        } else if (hasClass(curform, ".refresh-on-success")) {
          resetmode = "refreshpageonsuccess";
        } else if (hasClass(curform, ".callurl-on-success")) {
          resetmode = "callurlonsuccess";
        }
        clearFormStatus(curform, resetmode);
      }
    }
  }

  /*----------  DELETE FORM  ----------*/
  var formDelete = function(ev) {

    var delconf = confirm("Pretende mesmo eliminar este item?!");

    if (delconf == true) {
      var curform = this.form;
      var procscript = curform.getAttribute("action");
      var loadinngmsg = "A processar os dados...";
      var formfields =  new FormData(curform);
      formfields.set("formaction", "delete");
      formfields.append("reqcode", "b86027f4f0b60cf0234557b55744a9bf6ecf26f71df497e8533c721e1c85ec6d");

      var formsend = new XMLHttpRequest();
      var url = "/app/code/processing/" + procscript;
      formsend.open("POST", url, true);
      formsend.send(formfields);
      formsend.onreadystatechange = function() {
        var fstatus = curform.querySelector("#form_status");       
        if(formsend.readyState < 4) {
          fstatus.innerHTML = loadinngmsg;
          if (hasClass(fstatus, ".d-none")) {
            fstatus.classList.remove("d-none");
            fstatus.classList.add("d-block");
          }
        } else if (formsend.readyState == 4) {
          var formsendstatus = JSON.parse(this.responseText);
          if (formsendstatus[0][0] == 1) {
            if (hasClass(fstatus, ".d-block")) {
              fstatus.classList.remove("d-block");
              fstatus.classList.add("d-none");
            }
            if (hasClass(fstatus, ".alert-secondary")) {
              fstatus.classList.remove("alert-secondary","text-secondary");
            }
            fstatus.classList.add("alert-success","text-success");
            fstatus.innerHTML = "<i class=\"fas fa-check mr-3\"></i> " + formsendstatus[0][1];
            if (hasClass(fstatus, ".d-none")) {
              fstatus.classList.remove("d-none");
              fstatus.classList.add("d-block");
            }
          } else {
            if (hasClass(fstatus, ".alert-secondary")) {
              fstatus.classList.remove("alert-success","text-success");
            }
            fstatus.classList.add("alert-danger","text-danger");
            fstatus.innerHTML = "<i class=\"fas fa-exclamation-triangle mr-3\"></i> " + formsendstatus[0][1];
            if (hasClass(fstatus, ".d-none")) {
              fstatus.classList.remove("d-none");
              fstatus.classList.add("d-block");
            }
          }

          var resetmode = "callurlonsuccess";
          clearFormStatus(curform, resetmode);
        }
      }
    }
  }

  function clearFormStatus(formobj, resetmode) { 
    setTimeout(function() {
      var formstatus = false;
      var fstatus = formobj.querySelector('#form_status');
      
      if (hasClass(fstatus, '.alert-success')) {
        formstatus = true;
        if (resetmode == "clearall") {
         formobj.reset();
        }   
        fstatus.classList.remove("alert-success","text-success");
      }

      if (hasClass(fstatus, '.alert-danger')) {
        var errorfields = Array.from(formobj.querySelectorAll('.is-invalid'));  
        errorfields.length > 0 && 
          errorfields.forEach(function(input) {
            input.classList.remove('is-invalid');
            input.parentElement.querySelector('.invalid-feedback').innerHTML = "";
          });
        fstatus.classList.remove("alert-danger","text-danger");
      }  

      fstatus.classList.add("alert-secondary","text-secondary");
      fstatus.innerHTML = "";
      if (hasClass(fstatus, ".d-block")) {
        fstatus.classList.remove("d-block");
        fstatus.classList.add("d-none");
      }

      if (resetmode == "refreshpage") {
        location.reload(true);
      } else if (resetmode == "refreshpageonsuccess") {
        if (formstatus) {
          location.reload(true);
        }
      } else if (resetmode == "callurlonsuccess") {
        if (formstatus) {
          location = formobj.getAttribute("callbackurl");
        }
      }       
    }, 3000);
  }

  var sendBtns = document.querySelectorAll('button[btnaction="form-send"]');

  var delBtns = document.querySelectorAll('button[btnaction="form-delete"]');

  sendBtns.length > 0 &&
    sendBtns.forEach(function(input) {
      input.addEventListener("click", formSend);
    });

  delBtns.length > 0 &&
    delBtns.forEach(function(input) {
      input.addEventListener("click", formDelete);
    });
});