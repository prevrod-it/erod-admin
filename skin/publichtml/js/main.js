var hasClass = function hasClass(el, selector) {
  return (
    el.matches ||
    el.matchesSelector ||
    el.msMatchesSelector ||
    el.mozMatchesSelector ||
    el.webkitMatchesSelector ||
    el.oMatchesSelector
  ).call(el, selector);
};

function readCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
    var c = ca[i];
    while (c.charAt(0) === ' ') {
        c = c.substring(1,c.length);
    }
    if (c.indexOf(nameEQ) === 0) {
        return c.substring(nameEQ.length,c.length);
    }
  }
  return null;
}

function outputDate(dateobj) {
  //dateobj is a js date object
  var day = dateobj.getDate();
  var month = dateobj.getMonth() + 1;
  var year = dateobj.getFullYear();

  if (day < 10) {
    day = '0' + day.toString();
  } else {
    day = day.toString();
  }
  if (month < 10) {
    month = '0' + month.toString();
  } else {
    month = month.toString();
  }
  year = year.toString();
  outputdate = day + '/' + month + '/' + year;

  return outputdate;
}

function showNotification() {
   const notification = new Notification("New message incoming", {
      body: "Hi there. How are you doing?",
      icon: "/skin/adminhtml/img/notification.png",
      badge: "/skin/adminhtml/img/notificationbadge.png" 
   })
}

window.addEventListener("DOMContentLoaded", function() {
  
  var $body = document.body;

  /*----------  LIST FILTERS  ----------*/  
  var listfilters = Array.from(document.querySelectorAll(".list-filter"));
  var listfilterclear = document.querySelector(".list-filter-clear");

  var filterHandler = function() {
    var thisform = this.form;
    var action = thisform.getAttribute("action");
    var offset = thisform.querySelector("#offset");
    var limit = thisform.querySelector("#limit");

    thisform.setAttribute("method", "POST");
    thisform.action = "/public/" + action;
    offset.value = 0;
    thisform.submit();
  }

  var filterClear= function() {
    var thisform = this.form;
    var action = thisform.getAttribute("action");
    window.open("/public/" + action, "_self");
  }

  listfilters &&
    listfilters.forEach(function(listfilter) {
      listfilter.addEventListener("change", filterHandler);
    });

  listfilterclear &&
    listfilterclear.addEventListener("click", filterClear);

  /*----------  PAGINATION  ----------*/
  var pagelinks = Array.from(document.querySelectorAll("[data-offset]"));

  var pageHandler = function() {
    var thisoffset = this.getAttribute("data-offset");
    var targetformid = this.parentElement.getAttribute("form");
    var targetform = document.querySelector("#"+targetformid);
    var action = targetform.getAttribute("action");
    var offset = targetform.querySelector("#offset");
    var limit = targetform.querySelector("#limit");

    targetform.setAttribute("method", "POST");
    targetform.action = "/public/" + action;
    offset.value = thisoffset;
    targetform.submit();
  }

  pagelinks &&
    pagelinks.forEach(function(pagelink) {
      pagelink.addEventListener("click", pageHandler);
    });

  /*----------  SORTING  ----------*/
  var sortlinks = Array.from(document.querySelectorAll("[data-sortfld]"));

  var sortHandler = function() {
    var thissortfld = this.getAttribute("data-sortfld");
    var targetformid = this.getAttribute("form");
    var targetform = document.querySelector("#"+targetformid);
    var action = targetform.getAttribute("action");
    var sortfld = targetform.querySelector("#sortfld");

    targetform.setAttribute("method", "POST");
    targetform.action = "/public/" + action;
    sortfld.value = thissortfld;
    targetform.submit();
  }

  sortlinks &&
    sortlinks.forEach(function(sortlink) {
      sortlink.addEventListener("click", sortHandler);
    });

  /*---------- SCROLL TO TOP  ----------*/
  //Get the button:
  topbutton = document.getElementById("topButton");

  // When the user scrolls down 20px from the top of the document, show the button
  window.onscroll = function() { scrollFunction(); };

  function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
      topbutton.style.display = "block";
    } else {
      topbutton.style.display = "none";
    }
  }

  // When the user clicks on the button, scroll to the top of the document
  function topFunction() {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
  }

  /*---------- CONDITIONAL SELECT INPUT  ----------*/
  var csfilters = Array.from(document.querySelectorAll(".csf-input"));

  var csfilterHandler = function() {
    var csfsearchstr = this.value;
    var csflist = this.nextElementSibling;
    var procscript = this.dataset.source;
    var groupflt = this.dataset.groupflt;
    var limit = this.dataset.limit;
    var helper = this.dataset.helper;
    var formfields =  new FormData();
      formfields.append("reqcode", "b86027f4f0b60cf0234557b55744a9bf6ecf26f71df497e8533c721e1c85ec6d");
      formfields.append("csfinput", csfsearchstr);
      formfields.append("groupid", groupflt);
      formfields.append("limit", limit);
      formfields.append("helper", helper);

    var formsend = new XMLHttpRequest();
    var url = "/app/code/processing/" + procscript;
    formsend.open("POST", url, true);
    formsend.send(formfields);
    formsend.onreadystatechange = function() {
      if (formsend.readyState == 4) {
        var returnlist = JSON.parse(this.responseText);
        if (Array.isArray(returnlist)) {
          var newlistcount = returnlist.length;
          while (csflist.options.length > 0) {
            csflist.remove(0);
          }
          returnlist.forEach(function(listrow) {
            let option = document.createElement("option");
            option.value = listrow[0];
            option.text = listrow[1];
            csflist.add(option);
          });
          if (newlistcount >= limit) {
            let option = document.createElement("option");
            option.text = "A mostrar até ao máximo de " + limit + " items. Use a pesquisa para mais...";
            option.disabled = true;
            option.classList.add("fst-italic");
            csflist.add(option);
          }
        }        
      }    
    }  
  }

  csfilters &&
    csfilters.forEach(function(csfilter) {
      csfilter.addEventListener("input", csfilterHandler);
    });  

  /*---------- MODAL CHECKBOX SELECTOR  ----------*/
  var cbxforms = Array.from(document.querySelectorAll(".cbx-picker"));

  var cbxpickHandler = function() {
    var thisform = this.form;
    var thisformid = thisform.id;
    var mainformid = thisformid.substring(0, thisformid.length-4);
    var targetfld = thisform.getAttribute("data-target");
    var maintargetfld = document.getElementById(mainformid).querySelector("#main" + targetfld);
    
    var cbxinputschecked = Array.from(thisform.querySelectorAll("input:checked, .cbx-picker"));
    var thisord = parseInt(this.getAttribute("data-ord"));
    var thisval = this.value;
    var thislabel = thisform.querySelector("#ord-" + thisval);

    var maxord = 0;
    cbxinputschecked.forEach(function(checkeditem) {
      maxord = Math.max(maxord, parseInt(checkeditem.getAttribute("data-ord")));  
    });

    if (this.checked) {
      maxord++;
      this.setAttribute("data-ord", maxord);
      thislabel.innerText = "(" + maxord + "º)";
    } else {
      cbxinputschecked.forEach(function(checkeditem) {
        var curord = parseInt(checkeditem.getAttribute("data-ord"));
        var curval = checkeditem.value;
        var curlabel = thisform.querySelector("#ord-" + curval);
        if (curord > thisord) {
          curord--;
          checkeditem.setAttribute("data-ord", curord);
          curlabel.innerText = "(" + curord + "º)";
        } 
      });
      this.setAttribute("data-ord", 0);
      thislabel.innerText = "";
    }

    var cbxlist = new Array();
    cbxinputschecked.forEach(function(checkeditem) {
      var checkedord = parseInt(checkeditem.getAttribute("data-ord")) - 1;
      var checkedval = checkeditem.value;
      cbxlist[checkedord] = checkedval;
    });

    var mainfldval = "Não definido";
    if (cbxlist.length == 0) {
      document.getElementById(mainformid).querySelector("#" + targetfld).value = "[0]";
    } else {
      document.getElementById(mainformid).querySelector("#" + targetfld).value = "[" + cbxlist + "]";
      mainfldval = thisform.querySelector("#lbl-" + cbxlist[0]).innerText;
    }

    if (typeof(maintargetfld) != 'undefined' && maintargetfld != null) {
      maintargetfld.value = mainfldval;
    }
  }

  cbxforms &&
    cbxforms.forEach(function(cbxform) {
      cbxform.addEventListener("change", cbxpickHandler);
    });

  /*---------- CHILD FORM FILED FILTER  ----------*/
  var childfltfields = Array.from(document.querySelectorAll(".childflt"));

  var childfltHandler = function() {
    var thisval = this.value;
    var thisoptions = this.options;
    var thisoptindex = this.selectedIndex;
    var targetfirstchild = thisoptions[thisoptindex].dataset.firstchild;
    var targetchildid = this.getAttribute("data-child");
    var targetchild = this.form.querySelector("#" + targetchildid);
    var childarray = targetchild.options;
    var nopt = childarray.length;

    var i;
    for (i = 0; i < nopt; i++) {
      childarray[i].style.display = "block";
      var parentval = childarray[i].getAttribute("data-parent");
      if (parentval != "" && parentval != thisval) {
        childarray[i].style.display = "none";
      }
    }
    if (targetfirstchild) {
      targetchild.value = targetfirstchild;
    } else {
      targetchild.value = 1;
    }
  }

  childfltfields &&
    childfltfields.forEach(function(childfltfield) {
      childfltfield.addEventListener("change", childfltHandler);
    });

  /*---------- PARENT FORM FILED FILTER  ----------*/
  var parentfltfields = Array.from(document.querySelectorAll(".parentflt"));

  var parentfltHandler = function() {
    var targetparentid = this.getAttribute("data-parent");
    var targetparent = this.form.querySelector("#" + targetparentid);
    var childarray = this.options;
    var thisoptindex = childarray.selectedIndex;
    var thisoption = childarray[thisoptindex];
    var parentval = thisoption.getAttribute("data-parent"); 
    
    targetparent.value = parentval;
  }

  parentfltfields &&
    parentfltfields.forEach(function(parentfltfield) {
      parentfltfield.addEventListener("change", parentfltHandler);
    });

  /*---------- INDIVIDUAL USER FORM FILL  ----------*/
  var pubusersubsctypeselector = document.querySelector("#subsctype");

  var puserFormfill = function() {
    var thisform = this.form;
    var thisval = this.value;
    var companydata = document.querySelector("#js-companydata");
    var drvname = thisform.querySelector("#name");
    var cmpname = companydata.querySelector("#js-entityname").value;
    var drvtaxid = thisform.querySelector("#taxid");
    var cmptaxid = companydata.querySelector("#js-entitytaxid").value;
    var drvaddr = thisform.querySelector("#address");
    var cmpaddr = companydata.querySelector("#js-entityaddress").value;
    var drvzipcode = thisform.querySelector("#zipcode");
    var cmpzipcode = companydata.querySelector("#js-entityzipcode").value;
    var drvziploc = thisform.querySelector("#ziploc");
    var cmpziploc = companydata.querySelector("#js-entityziploc").value;
    var drvemail = thisform.querySelector("#email");
    var cmpemail = companydata.querySelector("#js-entityemail").value;
    var drvtel = thisform.querySelector("#tel");
    var cmptel = companydata.querySelector("#js-entitytel").value;
    
    if (thisval == 0) {
      drvname.value = cmpname;
      drvtaxid.value = cmptaxid;
      drvaddr.value = cmpaddr;
      drvzipcode.value = cmpzipcode;
      drvziploc.value = cmpziploc;
      drvemail.value = cmpemail;
      drvtel.value = cmptel;
    } else {
      drvname.value = "";
      drvtaxid.value = "";
      drvaddr.value = "";
      drvzipcode.value = "";
      drvziploc.value = "";
      drvemail.value = "";
      drvtel.value = "";
    }
  }

  pubusersubsctypeselector &&
    pubusersubsctypeselector.addEventListener("change", puserFormfill);

  /*---------- Show/hide password field  ----------*/
  var pwdfldselector = document.querySelector('a[data-bs-toggle="shpassword"]');

  var showhidePwd = function () {
    let pwdfldtraget = this.getAttribute("data-bs-target");
    let targetobj = document.querySelector(pwdfldtraget);
    let targetype = targetobj.getAttribute("type");
    if (targetype == "password") {
      if (targetobj.value == "KeepPassword") {
        targetobj.value = "";
      }
      targetobj.setAttribute("type", "text");
      this.innerHTML = "<i class=\"fas fa-eye-slash\"></i>";
    } else {
      targetobj.setAttribute("type", "password");
      if (targetobj.value == "") {
        targetobj.value = "KeepPassword";
      }
      this.innerHTML = "<i class=\"fas fa-eye\">";
    }
  }

  pwdfldselector &&
    pwdfldselector.addEventListener("click", showhidePwd);

  // Loop js if...
  var hasloop = document.getElementById("loopjsfn");
  if (hasloop) {
    var vlooptime = hasloop.dataset.looptime;
    var vloopfn = hasloop.dataset.loopfn;
    var vlooptimems = vlooptime * 1000;

    var loop = setInterval(doloop,vlooptimems,vloopfn);
  }

  // Loop functions
  // Main
  function doloop(fn) {
    //Fn1
    if (fn == "newevents") {
      newevents();
    } else{
      alert("ERRO");
    }
  }
  //
  //Fn1
  function newevents() {

    var procscript = "checknewevents.php";
    var url = "/app/code/processing/" + procscript;
    var formfields =  new FormData();
    formfields.append("reqcode", "b86027f4f0b60cf0234557b55744a9bf6ecf26f71df497e8533c721e1c85ec6d");

    var formsend = new XMLHttpRequest();
    formsend.open("POST", url, true);
    formsend.send(formfields);
    formsend.onreadystatechange = function() {
      if (formsend.readyState == 4) {
        var formsendstatus = this.responseText;
        var notarr = JSON.parse(formsendstatus);
        if (notarr.length > 0) {
          notifications(notarr);
        }
      }
    }
  }

  function notifications(notlist) {
    var neweventsmodal = document.getElementById("neweventsmodal");
    if (neweventsmodal) {
      var modalcontent = neweventsmodal.querySelector(".modal-body");
      var newnotelem = null;
      
      for (var i = 0; i < notlist.length; i++) {
        newnotelem = "<div id=\"not-" + i + "\" class=\"alert alert-" + notlist[i][4] + " alert-dismissible fade show\" role=\"alert\">";
        newnotelem += "<i class=\"" + notlist[i][5] + " me-2\"></i>";
        newnotelem += "<span><small>[" + notlist[i][2] + " às " + notlist[i][3] + "] . <a href=\"#\" class=\"alert-link\">Lead #" + notlist[i][0] + "</a></small></span>";
        newnotelem += "<span class=\"d-block\">" + notlist[i][6] + "</span>";
        newnotelem += "<button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Fechar\"></button>";
        newnotelem += "</div>";

        var firstelem = modalcontent.firstElementChild;
        if (!firstelem) {
          modalcontent.innerHTML = newnotelem;
        } else {
          firstelem.insertAdjacentHTML("beforebegin",newnotelem);  
        }

        //SO notification
        if (Notification.permission === "granted") {
          showNotification();
        } else if (Notification.permission !== "denied") {
          Notification.requestPermission().then(permission => {
            showNotification();
          });
        }
      }

      if (!hasClass(neweventsmodal, ".show")) {        
        var notifmodal = new bootstrap.Modal(neweventsmodal);
        notifmodal.toggle();
        let notifsound = document.getElementById("notificationaudio");
        notifsound.play();
      }
    }
  }
});