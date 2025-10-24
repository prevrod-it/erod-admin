var beat = new Audio('/media/file/alarm.mp3');
var dataarray = new Array();
var datajson = Array(null,null,null,null,null,null,null);
var syncbusy = false;
var acttimecount = null; var rsttimecount = null; var drvtimecount = null; var dawtimecount = null;

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

function setCookie(cname, cvalue, exhours) {
  var d = new Date();
  d.setTime(d.getTime() + (exhours*60*60*1000));
  var expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
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

function outputDateTime(dateobj,precision) {
  //s->seconds; m-minuts
  //dateobj is a js date object
  var day = dateobj.getDate();
  var month = dateobj.getMonth() + 1;
  var year = dateobj.getFullYear();
  var hour = dateobj.getHours();
  var min = dateobj.getMinutes();
  var sec = dateobj.getSeconds();

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
  if (hour < 10) {
    hour = '0' + hour.toString();
  } else {
    hour = hour.toString();
  }
  if (min < 10) {
    min = '0' + min.toString();
  } else {
    min = min.toString();
  }
  if (sec < 10) {
    sec = '0' + sec.toString();
  } else {
    sec = sec.toString();
  }
  
  if (precision == "m") {
    outputdate = day + '/' + month + '/' + year + ' ' + hour + ':' + min;
  } else if (precision == "s") {
    outputdate = day + '/' + month + '/' + year + ' ' + hour + ':' + min + ':' + sec;
  } else {
    outputdate = day + '/' + month + '/' + year;
  }

  return outputdate;
}

function outputCurDateTime() {
  let dateobj = new Date();
  var day = dateobj.getDate();
  var month = dateobj.getMonth() + 1;
  var year = dateobj.getFullYear();
  var hour = dateobj.getHours();
  var min = dateobj.getMinutes();
  var sec = dateobj.getSeconds();

  year = year.toString();
  if (month < 10) {
    month = '0' + month.toString();
  } else {
    month = month.toString();
  }
  if (day < 10) {
    day = '0' + day.toString();
  } else {
    day = day.toString();
  }
  if (hour < 10) {
    hour = '0' + hour.toString();
  } else {
    hour = hour.toString();
  }
  if (min < 10) {
    min = '0' + min.toString();
  } else {
    min = min.toString();
  }
  if (sec < 10) {
    sec = '0' + sec.toString();
  } else {
    sec = sec.toString();
  }
  
  outputdate = year + '-' + month + '-' + day + ' ' + hour + ':' + min + ':' + sec;

  return outputdate;
}

function gettimefromsecs($secstr) {
  let hrs = $secstr / 3600;
  let inthrs = Math.trunc(hrs);
  let mins = (hrs - inthrs) * 60;
  let intmins = Math.trunc(mins);
  let secs = (mins - intmins) * 60;
  let intsecs = Math.trunc(secs);
  return Array(inthrs,intmins,intsecs);
}

function isInViewport(el) {
  const rect = el.getBoundingClientRect();
  return (
    rect.top >= 0 &&
    rect.left >= 0 &&
    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
    rect.right <= (window.innerWidth || document.documentElement.clientWidth)
  );
}

function sysRTClock() {
  let date = new Date(); 
  let hh = date.getHours();
  let mm = date.getMinutes();
  let ss = date.getSeconds();
  let session = "AM";

  if(hh > 12){
      session = "PM";
  }

  hh = (hh < 10) ? "0" + hh : hh;
  mm = (mm < 10) ? "0" + mm : mm;
  ss = (ss < 10) ? "0" + ss : ss;
    
  let time = hh + ":" + mm + ":" + ss; //+ " " + session;
  document.getElementById("rtclock").innerText = time;
  let t = setTimeout(function() { sysRTClock() }, 1000); 
}

function sysACTClock(timestr) {
  let curactstart = new Date(timestr);
  let curdatetime = new Date();
  let curacttmcount = (curdatetime - curactstart) / 1000; if (curacttmcount > 345600) { curacttmcount = 0; }
  var timearr = gettimefromsecs(curacttmcount);
  let hi = timearr[0]; let hh = (hi < 10) ? "0" + hi : hi; 
  let mi = timearr[1]; let mm = (mi < 10) ? "0" + mi : mi; 
  let si = timearr[2]; let ss = (si < 10) ? "0" + si : si;
  let time = hh + ":" + mm + ":" + ss;
  actclock.innerText = time;
  if (hi < 96) {
    acttimecount = setTimeout(function() { sysACTClock(timestr) }, 1000);
  }
}

function sysRSTClock(timestr,count) {
  let curactstart = new Date(timestr);
  let curdatetime = new Date();
  let curacttmcount = (curdatetime - curactstart) / 1000; if (curacttmcount > 345600) { curacttmcount = 0; }
  var timearr = gettimefromsecs(curacttmcount);
  let hi = timearr[0]; let hh = (hi < 10) ? "0" + hi : hi; 
  let mi = timearr[1]; let mm = (mi < 10) ? "0" + mi : mi; 
  let si = timearr[2]; let ss = (si < 10) ? "0" + si : si;
  let time = hh + ":" + mm + ":" + ss;
  rstclock.innerText = time;
  if (count == true && hi < 96) {
    rsttimecount = setTimeout(function() { sysRSTClock(timestr,count) }, 1000);
  }
}

function sysDRVClock(timestr,count) {
  let curactstart = new Date(timestr);
  let curdatetime = new Date();
  let curacttmcount = (curdatetime - curactstart) / 1000; if (curacttmcount > 345600) { curacttmcount = 0; }
  var timearr = gettimefromsecs(curacttmcount);
  let hi = timearr[0]; let hh = (hi < 10) ? "0" + hi : hi; 
  let mi = timearr[1]; let mm = (mi < 10) ? "0" + mi : mi; 
  let si = timearr[2]; let ss = (si < 10) ? "0" + si : si;
  let time = hh + ":" + mm + ":" + ss;
  drvclock.innerText = time;
  if (count == true && hi < 96) {
    drvtimecount = setTimeout(function() { sysDRVClock(timestr,count) }, 1000);
  }
}

function sysDAWClock(timestr,count) {
  let curactstart = new Date(timestr);
  let curdatetime = new Date();
  let curacttmcount = (curdatetime - curactstart) / 1000; if (curacttmcount > 345600) { curacttmcount = 0; }
  var timearr = gettimefromsecs(curacttmcount);
  let hi = timearr[0]; let hh = (hi < 10) ? "0" + hi : hi; 
  let mi = timearr[1]; let mm = (mi < 10) ? "0" + mi : mi; 
  let si = timearr[2]; let ss = (si < 10) ? "0" + si : si;
  let time = hh + ":" + mm + ":" + ss;
  dawclock.innerText = time;
  if (count == true && hi < 96) {
    dawtimecount = setTimeout(function() { sysDAWClock(timestr,count) }, 1000);
  }
}

function conStatus() {
  let status = true;
  if (navigator.onLine) {
    status = true;
  } else {
    status = false;
  }
  return status;
}

function showConStatus() {
  if (navigator.onLine) {
    constatus.classList.replace("fa-ban","fa-wifi");
    constatus.classList.replace("text-danger","text-success");
  } else {
    constatus.classList.replace("fa-wifi","fa-ban");
    constatus.classList.replace("text-success","text-danger");
  }
  let t = setTimeout(function() { showConStatus() }, 1000);
}

function actcounters(journeyid,journeystart,journeyend,activitieslist) {
  let timenow = new Date();
  let journeystartobj = new Date(journeystart);
  let journeyendobj = (journeyend !== null && journeyend != "") ? new Date(journeyend) : timenow;
  let firstacttimeobj = null;
  let tottmcounters = Array(0,0,0,0,0);
  let rstdif = 0;
  let idx = 0;
  activitieslist.forEach(function(ev) {
    if (ev[3] == journeyid) {
      var thisacttp = ev[1];  
      var timestart = (ev[6] !== null) ? new Date(ev[6]) : timenow;
      var timeend = (ev[7] !== null) ? new Date(ev[7]) : journeyendobj;
      let acttmdif = timeend - timestart;
      tottmcounters[thisacttp] += acttmdif;
      if (idx == 0 && thisacttp != 0) {
        firstacttimeobj = new Date(timestart);
      }
      idx++;  
    }
  });
  if (firstacttimeobj === null) { firstacttimeobj = timenow; }
  rstdif = firstacttimeobj - journeystartobj;
  tottmcounters[0] += rstdif;
  tottmcounters[4] = tottmcounters[1] + tottmcounters[2];
  tottmcounters.forEach(function(ev,idx,arr) {
    let utsnow = Date.now();
    let newuts = utsnow - ev;
    let newsymbdate = new Date(newuts);
    arr[idx] = newsymbdate.toISOString();
  });

  return tottmcounters;
}

function appendjrnend(arr) {
  let rptel = document.querySelector("#jrnreport");
  let rowelm = document.createElement("span");
  let symelm = document.createElement("i");
  let textelm = document.createTextNode("");
  rowelm.classList.add("d-block","w-100","py-2","bg-light","fw-bold");
  symelm.classList.add("fa-solid","fa-circle-stop","me-2");
  if (arr[7] !== null && arr[7] != "") {
    textelm.data = "Jornada terminada em " + arr[7] + ", em " + arr[9];
  } else {
    textelm.data = "Jornada a decorrer";
  }
  rowelm.appendChild(symelm);
  rowelm.appendChild(textelm);
  rptel.append(rowelm);
}

function appendjrnstart(arr) {
  let rptel = document.querySelector("#jrnreport");
  let rowelm = document.createElement("span");
  let symelm = document.createElement("i");
  let textelm = document.createTextNode("");
  rowelm.classList.add("d-block","w-100","py-2","bg-light","fw-bold");
  symelm.classList.add("fa-solid","fa-circle-play","me-2");
  textelm.data = "Jornada iniciada em " + arr[4] + ", em " + arr[6];

  rowelm.appendChild(symelm);
  rowelm.appendChild(textelm);
  rptel.append(rowelm);
}

function appendactivity(arr) {
  let rptel = document.querySelector("#jrnreport");
  let rowelm = document.createElement("span");
  let symelm = document.createElement("img");
  let textelm = document.createTextNode("");
  let spanelm = document.createElement("span");
  let imgarr = Array("ico-rest.svg","ico-driving.svg","ico-altwork.svg","ico-availability.svg");
  let timedifstr = "";
  rowelm.classList.add("d-block","w-100");
  symelm.classList.add("svg-ico");
  symelm.src = "/media/img/" + imgarr[arr[1]];
  if (arr[7] !== null && arr[7] != "") {
    let inidate = new Date(arr[6]); let enddate = new Date(arr[7]);
    let datedif = (enddate - inidate) / 1000;
    let timearr = gettimefromsecs(datedif); if (timearr[0] > 96) { timearr[0] = 96; }
    let hd = (timearr[0] < 10) ? "0" + timearr[0] : timearr[0];
    let md = (timearr[1] < 10) ? "0" + timearr[1] : timearr[1];
    timedifstr = hd + "h" + md + "m";
    textelm.data = arr[6] + " > " + arr[7] + " ";
  } else {
    let inidate = new Date(arr[6]); let enddate = new Date();
    let datedif = (enddate - inidate) / 1000;
    let timearr = gettimefromsecs(datedif);
    let hd = (timearr[0] < 10) ? "0" + timearr[0] : timearr[0];
    let md = (timearr[1] < 10) ? "0" + timearr[1] : timearr[1];
    timedifstr = hd + "h" + md + "m";
    textelm.data = arr[6] + " > a decorrer ";
  }
  spanelm.classList.add("fw-bold");
  spanelm.innerText = "(" + timedifstr + ")";

  rowelm.appendChild(symelm);
  rowelm.appendChild(textelm);
  rowelm.appendChild(spanelm);
  rptel.append(rowelm);
}

function appendremark(arr) {
  let rptel = document.querySelector("#rmkreport");
  let rowelm = document.createElement("span");
  let textelm = document.createTextNode("");
  rowelm.classList.add("d-block","w-100","p-2","bg-light");
  textelm.data = arr[2] + ": " + arr[3];

  rowelm.appendChild(textelm);
  rptel.append(rowelm);
}

/*----------  SEND API REQUEST  ----------*/
/*----------  START  ----------*/
function returnasyncresp(dataid,jsonstr) {
  datajson[dataid] = jsonstr;
  dataarray[dataid] = JSON.parse(datajson[dataid]);
  localStorage.setItem("lsdata" + dataid,datajson[dataid]);
}

function queryapi(apiscript,userid,qaction,qid,pref) {
  var respstr = "";
  var reqfields =  new FormData();
  reqfields.append("userid", userid);
  reqfields.append("qaction", qaction);
  reqfields.append("qid", qid);
  reqfields.append("pref", pref);
  reqfields.append("reqcode", "b86027f4f0b60cf0234557b55744a9bf6ecf26f71df497e8533c721e1c85ec6d");

  var reqsend = new XMLHttpRequest();
  var url = "/app/code/processing/" + apiscript;
  reqsend.open("POST", url, true);
  reqsend.send(reqfields); 
  reqsend.onreadystatechange = function() {
    if (reqsend.readyState == 4) {
      var reqsendresp = JSON.parse(this.responseText);
      respstr = JSON.stringify(reqsendresp);
      returnasyncresp(qid,respstr);
    }
  }
  syncbusy = false;
}
/*----------  END  ----------*/

/*----------  AT FULL CONTENT LOADED  ----------*/
/*----------  START  ----------*/
window.addEventListener("DOMContentLoaded", function() {
  
  var body = document.body;
  var wheight = window.innerHeight;
  var userid = document.getElementById("loginuserid").value;
  var fullsw = document.querySelector('#fullsw');
  var logout = document.querySelector('#logout');
  var fsstatus = false;
  var rtclock = document.querySelector('#rtclock');
  var actclockwrp = document.getElementById("actclockwrp"); var actclock = document.querySelector('#actclock');
  var rstclock = document.querySelector('#rstclock');
  var drvclock = document.querySelector('#drvclock');
  var dawclock = document.querySelector('#dawclock');
  var constatus = document.querySelector('#constatus');
  var vehinfo = document.querySelector("#vehcurrent");
  var allowoff = true;
  var emptyuser = JSON.stringify(Array(0,0,0,0,"Desconhecido","","","","","","999999999","","",0));
  var emptycompany  = JSON.stringify(Array(0,"Desconhecida","999999999","","","","",""));
  var emptyopunit = JSON.stringify(Array(0,"Desconhecida","","",""));
  var emmptyveh = JSON.stringify(Array());
  var emptyjourney = JSON.stringify(Array(Array(0,0,0,0,null,0,"",null,0,"",1)));
  var emptyvehalloc = JSON.stringify(Array(Array(0,0,"",0,null,0,0,"",null,0,0,"",1)));
  var emptyactions = JSON.stringify(Array(Array(0,0,0,0,0,"",null,null)));
  var emptyremarks = JSON.stringify(Array(Array(0,0,null,"")));
  var actColors = Array("bg-primary","bg-danger","bg-warning","bg-success","bg-white","bg-white");
  var clockColors = Array("text-primary","text-danger","text-warning","text-success","text-white","text-white");
  var jrnBtn = document.querySelector('button[btnaction="jrn-mark"]');
  var actBtns = Array.from(document.querySelectorAll('button[btnaction="act-mark"]'));
  var rmkbtn = document.querySelector('button[btnaction="rmk-add"]');

  /*----------  INIT DATA SECTION  ----------*/
  if (typeof(Storage) !== "undefined") {
    allowoff = true;
  } else {
    allowoff = false;
  }

  /*
  var loadingModalObj = new bootstrap.Modal(document.getElementById("loadingInterface"));
  loadingModalObj.show();
  */

  logout.addEventListener("click", function(ev) {
    if (conStatus()) {
      location.replace("/index.php?role=public&pg=logout");
    } else {
      alert("Não é possível terminar sessão sem ligação à internet!");
    }
  });

  datajson[0] = localStorage.getItem("lsdata0"); // User data
  datajson[1] = localStorage.getItem("lsdata1"); // Company data
  datajson[2] = localStorage.getItem("lsdata2"); // Opertional unit data
  datajson[3] = localStorage.getItem("lsdata3"); // Vehicles data
  datajson[4] = localStorage.getItem("lsdata4"); // Jorney data
  datajson[5] = localStorage.getItem("lsdata5"); // Allocation vehicle data
  datajson[6] = localStorage.getItem("lsdata6"); // Activities data
  datajson[7] = localStorage.getItem("lsdata7"); // Remarks data
  
  // User data
  if (conStatus()) {
     queryapi("usersapi.php",userid,"query",0,null);
  } else {
    if (datajson[0] === null) {
      datajson[0] = emptyuser;
      dataarray[0] = JSON.parse(datajson[0]);
    }
  }
  // Company data
  if (conStatus()) {
    queryapi("usersapi.php",userid,"query",1,null);
  } else {
    if (datajson[1] === null) {
      datajson[1] = emptycompany;
      dataarray[1] = JSON.parse(datajson[1]);
    }
  }
  // Operational unit data
  if (conStatus()) {
    queryapi("usersapi.php",userid,"query",2,null);
  } else {
    if (datajson[2] === null) {
      datajson[2] = emptyopunit;
      dataarray[2] = JSON.parse(datajson[2]);
    }
  }
  // Vehicles data
  if (datajson[3] === null || datajson[3] == "[]") {
    if (conStatus()) {
      queryapi("usersapi.php",userid,"query",3,null);
    } else {
      datajson[3] = emmptyveh;
      dataarray[3] = JSON.parse(datajson[3]); 
    }
  } else {
    if (conStatus()) {
      // Compare vehicles list
      queryapi("usersapi.php",userid,"compare",3,datajson[3]);
    }
  }
  // Joouney data
  if (datajson[4] === null || datajson[4] == "[]") {
    if (conStatus()) {
      queryapi("usersapi.php",userid,"query",4,null);
    } else {
      datajson[4] = emptyjourney;
      dataarray[4] = JSON.parse(datajson[4]);
    }
  } else {
    if (conStatus()) {
      // Compare journeys
      queryapi("usersapi.php",userid,"compare",4,datajson[4]);
    }
  }
  // Current allocated vehicle data
  if (datajson[5] === null || datajson[5] == "[]") {
    if (conStatus()) {
      queryapi("usersapi.php",userid,"query",5,null);
    } else {
      datajson[5] = emptyvehalloc;
      dataarray[5] = JSON.parse(datajson[5]);
    }
  } else {
    if (conStatus()) {
      // Compare vehicle allocations
      queryapi("usersapi.php",userid,"compare",5,datajson[5]);
    }
  }
  // Activities data
  if (datajson[6] === null || datajson[6] == "[]") {
    if (conStatus()) {
      queryapi("usersapi.php",userid,"query",6,null);
    } else {
      datajson[6] = emptyactions;
      dataarray[6] = JSON.parse(datajson[6]);
    }
  } else {
    if (conStatus()) {
      // Compare activities
      queryapi("usersapi.php",userid,"compare",6,datajson[6]);
    }
  }

  // Remarks data
  if (datajson[7] === null || datajson[7] == "[]") {
    if (conStatus()) {
      queryapi("usersapi.php",userid,"query",7,null);
    } else {
      datajson[7] = emptyremarks;
      dataarray[7] = JSON.parse(datajson[7]);
    }
  } else {
    if (conStatus()) {
      // Compare activities
      queryapi("usersapi.php",userid,"compare",7,datajson[7]);
    }
  }
 
  // OSD initial states
  setTimeout(function() {
    var driverdata = dataarray[0];
    var companydata = dataarray[1]; var opudata = dataarray[2];
    var curVehList = dataarray[3];
    var curJrnList = dataarray[4];
    var curJrnid = curJrnList[curJrnList.length-1][0];
    var curJrnstart = curJrnList[curJrnList.length-1][4];
    var curJrnend = (curJrnList[curJrnList.length-1][7] === null || curJrnList[curJrnList.length-1][7] == "") ? null : curJrnList[curJrnList.length-1][7];
    var curJrnst = curJrnList[curJrnList.length-1][10];
    var curValList = dataarray[5];
    var curValvid = curValList[curValList.length-1][1];
    var curValvrg = curValList[curValList.length-1][2];
    var curValvst = curValList[curValList.length-1][12];   
    var curActList = dataarray[6];
    var curActca = curActList[curActList.length-1];
    var curActtp = curActList[curActList.length-1][1];
    var driverinfo = document.getElementById("driverinfo");
    var companyinfo = document.getElementById("companyinfo");
    var vehDropList = document.getElementById("valVehId"); 
    var curbtn = document.getElementById("act" + curActtp);
    driverinfo.innerHTML += "Nome: " + driverdata[4] + "<br>";
    driverinfo.innerHTML += "Morada: " + driverdata[5] + ", " + driverdata[6] + " " + driverdata[7] + "<br>";  
    driverinfo.innerHTML += (driverdata[3] < 4) ? "Função: condutor<br>" : "Função: ajudante<br>";
    driverinfo.innerHTML += "e-mail: " + driverdata[8] + "<br>";
    driverinfo.innerHTML += "Tel: " + driverdata[9] + "<br>";
    driverinfo.innerHTML += "NIF: " + driverdata[10] + "<br>";
    driverinfo.innerHTML += "Carta de condução: " + driverdata[11] + "<br>";
    driverinfo.innerHTML += (driverdata[12] === null) ? "Início de contrato: n/a<br>" : "Início de contrato: " + driverdata[12] + "<br>";
    driverinfo.innerHTML += (driverdata[2] == 0) ? "Unidade operacional: sede<br>" : "Unidade operacional: " + opudata[1] + " - " + opudata[2] + ", " + opudata[3] + " " + opudata[4];
    companyinfo.innerHTML += companydata[1] + "<br>";
    companyinfo.innerHTML += "Morada: " + companydata[3] + ", " + companydata[4] + " " + companydata[5] + "<br>";  
    companyinfo.innerHTML += "e-mail: " + companydata[6] + "<br>";
    companyinfo.innerHTML += "Tel: " + companydata[7] + "<br>";
    companyinfo.innerHTML += "NIF: " + companydata[2] + "<br>";
    let vehoptcount = 0;
    curVehList.forEach(function(ev) {
      vehoptcount++;
      let option = document.createElement("option");
      let optionText = document.createTextNode(ev[1]);
      option.setAttribute("value",ev[0]);
      option.appendChild(optionText);
      vehDropList.appendChild(option);
      if (vehoptcount == 1) { vehDropList.value = ev[0]; }
    });
    if (curJrnst == 1) { // Jornadas fechadas
      jrnBtn.classList.replace("btn-secondary","btn-success");
      jrnBtn.innerHTML = "<i class=\"fa-solid fa-play me-2\"></i>Iniciar jornada";
      curbtn.classList.add("btnselected","bg-info");
    } else { // Jprnada aberta
      jrnBtn.classList.replace("btn-secondary","btn-danger");
      jrnBtn.innerHTML = "<i class=\"fa-solid fa-stop me-2\"></i>Terminar jornada";
      curbtn.classList.add("btnselected",actColors[curActtp]);
    }
    var inivehstr = "__-__-__";
    if (curValvid > 0) {  
      curVehList.forEach(function(ev) {
        if (ev[0] == curValvid && curValvst == 0) {
          inivehstr = ev[1];
        } 
      });
    } else {
      if (curValvst == 0) {
        inivehstr = curValvrg;
        var srchsep = inivehstr.search("-");
        if (srchsep >= 0) {
          inivehstr = inivehstr.replace(/[./_]/g,"-");
        } else {
          inivehstr = inivehstr.replace(/[ ./_]/g,"-");
        }
        inivehstr = inivehstr.toUpperCase();
      }
    }
    vehinfo.innerText = inivehstr;
    if (actclock !== null) {
      let thisactstart = (curActca[6] !== null) ? curActca[6] : outputCurDateTime();
      actclockwrp.classList.remove("text-primary","text-danger","text-success","text-warning");
      actclockwrp.classList.add(clockColors[curActtp]);
      sysACTClock(thisactstart);
    }
    let initmcounters = actcounters(curJrnid,curJrnstart,curJrnend,curActList);
    if (rstclock !== null) {
      if (curActtp == 0 && curJrnst == 0) {
        sysRSTClock(initmcounters[0],true);
      } else {
        sysRSTClock(initmcounters[0],false); 
      }
    }
    if (drvclock !== null) {
      if (curActtp == 1 && curJrnst == 0) {
        sysDRVClock(initmcounters[1],true);
      } else {
        sysDRVClock(initmcounters[1],false); 
      }
    }
    if (dawclock !== null) {
      if ((curActtp == 1 || curActtp == 2) && curJrnst == 0) {
        sysDAWClock(initmcounters[4],true);
      } else {
        sysDAWClock(initmcounters[4],false); 
      } 
    }
    loadingModalObj.hide();
  },2500);
  /*----------  END SECTION  ----------*/
  
  /*----------  FULLSCREEN SECTION  ----------*/
  if (fullsw !== null) {
    fullsw.addEventListener("click", () => {
      if (!document.fullscreenElement) {
        body?.requestFullscreen();
      } else {
        document.exitFullscreen();
      }
    });
  }
  /*----------  END SECTION  ----------*/

  /*----------  RTC SECTION  ----------*/
  if (rtclock !== null) {
    sysRTClock();
  }
  /*----------  END SECTION  ----------*/

  /*----------  CONNECTION STATUS SECTION  ----------*/
  if (constatus !== null) {
    showConStatus();
  }
  /*----------  END SECTION  ----------*/
 
  /*----------  SWIPE SECTION  ----------*/
  var slideleft = function(ev) {
    if (curpid != 3 && curpid != 4) {
      carouselObj.next();
    } else {
      carouselObj.prev();
    }
  }
  var slideright = function(ev) {
    if (curpid != 3 && curpid != 4) {
      carouselObj.prev();
    } else {
      carouselObj.next();
    }
  }

  var topelm = document.querySelector("#topelm");
  var driverelm = document.querySelector("#driverelm");
  var carouselSwipe = document.querySelector("#swipeboard");

  if (carouselSwipe !== null) {
    var curpid = null;
    var carouselObj = new bootstrap.Carousel(carouselSwipe);
    carouselSwipe.addEventListener("swiped-left", slideright);
    carouselSwipe.addEventListener("swiped-right", slideleft);
    carouselSwipe.addEventListener("slid.bs.carousel", function(ev) {
      curpid = ev.to;
      if (curpid == 3) {
        generate_report(15);
      } else if (curpid == 4) {
        generate_remarklist(15);
      }
    });

    topelm.addEventListener("swiped-down", function(ev) {
      if (conStatus()) {
        location.reload(true);
      } else {
        alert("Não existe ligação à internet!");
      }
    });
    driverelm.addEventListener("swiped-down", function(ev) {
      if (conStatus()) {
        location.reload(true);
      } else {
        alert("Não existe ligação à internet!");
      }
    });
  }
  /*----------  END SECTION  ----------*/

  /*----------  JOURNEY SECTION  ----------*/
  var jrnMark = function(ev) {
    var thisbutton = this;
    var thisModal = document.querySelector("#journeyInterface");
    var jrnModTit = thisModal.querySelector("#jrnModTit");
    var jrnTypeLbl = thisModal.querySelector("#jrnTypeLbl");
    var jrnType = thisModal.querySelector("#jrnType");
    var jrnStart = thisModal.querySelector("#jrnStart");
    var jrnLocTypeLbl = thisModal.querySelector("#jrnLocTypeLbl");
    var jrnLocType = thisModal.querySelector("#jrnLocType");
    var jrnLoc = thisModal.querySelector("#jrnLoc");
    var doactBtn = thisModal.querySelector('button[btnaction="jrn-action"]');
    // Elementos externos ao Modal
    var restBtn = document.querySelector("#act0");
    // Verificar o estado da última jornada
    var jrnList = dataarray[4];
    var jrnLastItem = jrnList[jrnList.length-1];
    var lastJrnid = jrnLastItem[0];
    var lastJrnstart = jrnLastItem[4];
    var lastJrnst = jrnLastItem[10];
    // Verificar a última atividade, e veículo
    var curValList = dataarray[5];
    var curValst = curValList[curValList.length-1][12];
    var curActList = dataarray[6];
    var curActtp = curActList[curActList.length-1][1];
    // Localizações
    var addrheadq = dataarray[1][3] + ", " + dataarray[1][4] + " " + dataarray[1][5];
    var addropu = dataarray[2][2] + ", " + dataarray[2][3] + " " + dataarray[2][4];
    var addrusr = dataarray[0][5] + ", " + dataarray[0][6] + " " + dataarray[0][7]; 
    // Iniciar Modal
    var thisModalObj = new bootstrap.Modal(thisModal);

    // Modal - valores iniciais
    if (lastJrnst == 1) {
      jrnModTit.innerText = "Iniciar jornada";
      jrnTypeLbl.classList.replace("d-none","d-block");
      jrnType.classList.replace("d-none","d-block");
      jrnStart.innerText = ""; jrnStart.classList.replace("d-block","d-none"); 
      jrnLocTypeLbl.innerText = "Localização de início";
      doactBtn.innerText = "Iniciar"; doactBtn.classList.replace("btn-danger","btn-success"); doactBtn.classList.replace("d-none","d-block");
    } else {
      jrnModTit.innerText = "Terminar jornada";
      jrnTypeLbl.classList.replace("d-block","d-none");
      jrnType.classList.replace("d-block","d-none");
      jrnStart.innerText = "Iniciada em " + outputDateTime(new Date(lastJrnstart),"m"); jrnStart.classList.replace("d-none","d-block"); 
      jrnLocTypeLbl.innerText = "Localização de encerramento";
      doactBtn.innerText = "Terminar"; doactBtn.classList.replace("btn-success","btn-danger"); doactBtn.classList.replace("d-none","d-block");
    }
    if (dataarray[0][2] > 0) {
      jrnLocType.value = 1;
      jrnLoc.value = addropu;  
    } else {
      jrnLocType.value = 0;
      jrnLoc.value = addrheadq;
    }

    var changeloc = function(ev) {
      if (this.value == 0) {
        jrnLoc.value = addrheadq; jrnLoc.setAttribute("readonly",true);
      } else if (this.value == 1) {
        if (dataarray[0][2] > 0) {
          jrnLoc.value = addropu;  
        } else {
          jrnLoc.value = addrheadq;
        }
        jrnLoc.setAttribute("readonly",true);
      } else if (this.value == 2) {
        jrnLoc.value = addrusr;
        jrnLoc.setAttribute("readonly",true);
      } else {
        jrnLoc.value = "";
        jrnLoc.removeAttribute("readonly");
      }
    }

    var dojrnact = function(ev) {
      if (lastJrnst == 1) {
        var newjrnarritem = Array(0,parseInt(jrnType.value),parseInt(userid),0,outputCurDateTime(),parseInt(jrnLocType.value),jrnLoc.value,null,0,"",0);       
        dataarray[4].push(newjrnarritem);
        if(dataarray[4].length > 15) {
          dataarray[4].shift();
        }
        var newjrnarrjson = JSON.stringify(dataarray[4]);
        if (conStatus()) {
          syncbusy = true;
          // Compare journeys
          queryapi("usersapi.php",userid,"compare",4,newjrnarrjson);
        } else {
          datajson[4] = newjrnarrjson;
          localStorage.setItem("lsdata4",datajson[4]);
        }
        thisbutton.innerHTML = "<i class=\"fa-solid fa-stop me-2\"></i>Terminar jornada"; thisbutton.classList.replace("btn-success","btn-danger");
        restBtn.classList.replace("bg-info",actColors[0]);
        // Iniciar contadores
        let startjrncounters = outputCurDateTime();
        if (rstclock !== null) {
          clearTimeout(rsttimecount);
          sysRSTClock(startjrncounters,true);
        }
        if (drvclock !== null) {
          clearTimeout(drvtimecount);
          sysDRVClock(startjrncounters,false); 
        }
        if (dawclock !== null) {
          clearTimeout(dawtimecount);
          sysDAWClock(startjrncounters,false); 
        }
      } else {
        var closejrnarritem = Array(
          parseInt(lastJrnid),
          parseInt(jrnLastItem[1]),
          parseInt(userid),
          0,
          lastJrnstart,
          parseInt(jrnLastItem[5]),
          jrnLastItem[6],
          outputCurDateTime(),
          parseInt(jrnLocType.value),
          jrnLoc.value,
          1
        );
        dataarray[4].pop();
        dataarray[4].push(closejrnarritem);
        var updatejrnarrjson = JSON.stringify(dataarray[4]);
        if (conStatus()) {
          syncbusy = true;
          // Compare journeys
          queryapi("usersapi.php",userid,"compare",4,updatejrnarrjson);
        } else {
          datajson[4] = updatejrnarrjson;
          localStorage.setItem("lsdata4",datajson[4]);
        }
        restBtn.classList.replace(actColors[0],"bg-info");
        thisbutton.innerHTML = "<i class=\"fa-solid fa-play me-2\"></i>Iniciar jornada"; thisbutton.classList.replace("btn-danger","btn-success");
        // Parar conyadres de jornada
        clearTimeout(rsttimecount); clearTimeout(drvtimecount); clearTimeout(dawtimecount);
      }
      thisModalObj.hide();
    }

    thisModal.addEventListener("hide.bs.modal", function(ev) {
      doactBtn.removeEventListener("click", dojrnact);
      jrnLocType.removeEventListener("change", changeloc);
    });

    if (lastJrnst == 0 && curValst == 0) {
        alert("Deve desassociar o veículo atual antes de terminar a jornada!");
    } else if (lastJrnst == 0 && curActtp > 0) {
      alert("Deve comutar para descanso antes de terminar a jornada!");
    } else {
      thisModalObj.show();
      // Modal - operação
      jrnLocType.addEventListener("change", changeloc);
      // Jornada - ação
      doactBtn.addEventListener("click", dojrnact);
    }
  }  

  if (jrnBtn !== null) {
    jrnBtn.addEventListener("click", jrnMark);
  }
  /*----------  END SECTION  ----------*/

  /*----------  VEHICLE ALLOCATION SECTION  ----------*/
  var valMark = function(ev) {
    var thisbutton = this;
    var thisModal = document.querySelector("#vehallocInterface");
    var valModTit = thisModal.querySelector("#valModTit");
    var valCurVeh = thisModal.querySelector("#valCurVeh");
    var valVehid = thisModal.querySelector("#valVehId");
    var valVeh = thisModal.querySelector("#valVeh");
    var valendKmsCol = thisModal.querySelector("#valendKmsCol");
    var valendKms = thisModal.querySelector("#valendKms");
    var valKmsCol = thisModal.querySelector("#valKmsCol");
    var valKms = thisModal.querySelector("#valKms");
    var valLocType = thisModal.querySelector("#valLocType");
    var valLoc = thisModal.querySelector("#valLoc");
    var doactBtn = thisModal.querySelector('button[btnaction="val-action"]');
    var vehlist = dataarray[3];
    // Verificar o estado da última alocação de viatura
    var valList = dataarray[5];
    var valLastItem = valList[valList.length-1];
    var lastValid = valLastItem[0];
    var lastValvid = valLastItem[1];
    var lastValvrg = valLastItem[2];
    var lastValstart = valLastItem[4];
    var lastValst = valLastItem[12];
    // Verificar o estado da última jornada
    var jrnList = dataarray[4];
    var lastjrnid = jrnList[jrnList.length-1][0];
    var lastjrnst = jrnList[jrnList.length-1][10];
    // Vaerificar a comutação atual
    var actList = dataarray[6];
    var lastacttp = actList[actList.length-1][1];
    // Localizações
    var addrheadq = dataarray[1][3] + ", " + dataarray[1][4] + " " + dataarray[1][5];
    var addropu = dataarray[2][2] + ", " + dataarray[2][3] + " " + dataarray[2][4];
    var addrusr = dataarray[0][5] + ", " + dataarray[0][6] + " " + dataarray[0][7];
    // Viatura atualmente atribuida (texto)
    var curvehstr = "";
    var vehList = dataarray[3];
    vehlist.forEach(function(ev) {
      if (ev[0] == lastValvid) {
        curvehstr = ev[1];
      } 
    });
    if (curvehstr == "" && lastValvrg != "") {
      curvehstr = lastValvrg;
      var findsep = curvehstr.search("-");
      if (findsep >= 0) {
        curvehstr = curvehstr.replace(/[./_]/g,"-");
      } else {
        curvehstr = curvehstr.replace(/[ ./_]/g,"-");
      }
      curvehstr = curvehstr.toUpperCase();
    }  
    // Iniciar Modal
    var thisModalObj = new bootstrap.Modal(thisModal);

    // Modal - valores iniciais
    var firstopt = 0;
    Array.from(valVehid.options).forEach(function(el) {
      if (el['value'] > 0) {
        var thisopt = valVehid.querySelector("option[value='" + el['value'] + "']")
        thisopt.style.display = "block";
        if (el['value'] != lastValvid && firstopt == 0) {
          firstopt = el['value'];
        }
      }
    });
    var valVehoptnone = valVehid.querySelector("option[value='-1']");
    var valVehoptcur = valVehid.querySelector("option[value='" + lastValvid + "']");
    valVehid.value = firstopt;
    if (lastValst == 0) {
      valModTit.innerText = "Trocar viatura";
      valCurVeh.innerText = "Viatura atual: " + curvehstr; valCurVeh.classList.replace("d-none","d-block");
      valVehoptnone.style.display = "block";
      valVehoptcur.style.display = (lastValvid > 0) ? "none" : "block";
      valendKmsCol.classList.remove("d-none");
      valKmsCol.classList.remove("d-none");
      doactBtn.innerText = "Trocar"; doactBtn.classList.replace("d-none","d-block");
    } else {
      valModTit.innerText = "Atribuir viatura";
      valCurVeh.innerText = ""; valCurVeh.classList.replace("d-block","d-none");
      valVehoptnone.style.display = "none";
      valendKmsCol.classList.add("d-none");
      valKmsCol.classList.remove("d-none");
      doactBtn.innerText = "Atribuir"; doactBtn.classList.replace("d-none","d-block");
    }
    if (dataarray[0][2] > 0) {
      valLocType.value = 1;
      valLoc.value = addropu;  
    } else {
      valLocType.value = 0;
      valLoc.value = addrheadq;
    }

    var changeveh = function(ev) {
      if (this.value == -1) {
        valVeh.value = ""; valVeh.setAttribute("readonly",true);
        valModTit.innerText = "Desatribuir viatura";
        valendKmsCol.classList.remove("d-none");
        valKmsCol.classList.add("d-none");
        doactBtn.innerText = "Desatribuir";
        if (lastacttp == 1) {
          doactBtn.classList.replace("d-block","d-none");
          alert("Não é possível desatribuir viatura em modo de condução!");
        } else {
          doactBtn.classList.replace("d-none","d-block");
        }
      } else if (this.value == 0) {
        valVeh.value = ""; valVeh.removeAttribute("readonly");
        if (lastValst == 0) {
          valModTit.innerText = "Trocar viatura";
          valendKmsCol.classList.remove("d-none");
          doactBtn.innerText = "Trocar";
        } else {
          valModTit.innerText = "Atribuir viatura";
          valendKmsCol.classList.add("d-none");
          doactBtn.innerText = "Atribuir ";
        }
        valKmsCol.classList.remove("d-none");
        doactBtn.classList.replace("d-none","d-block");
      } else { 
        valVeh.value = ""; valVeh.setAttribute("readonly",true);
        if (lastValst == 0) {
          valModTit.innerText = "Trocar viatura";
          valendKmsCol.classList.remove("d-none");
          doactBtn.innerText = "Trocar";
        } else {
          valModTit.innerText = "Atribuir viatura";
          valendKmsCol.classList.add("d-none");
          doactBtn.innerText = "Atribuir ";
        }
        valKmsCol.classList.remove("d-none");
        doactBtn.classList.replace("d-none","d-block");
      }
    }
    var changeloc = function(ev) {
      if (this.value == 0) {
        valLoc.value = addrheadq; valLoc.setAttribute("readonly",true);
      } else if (this.value == 1) {
        if (dataarray[0][2] > 0) {
          valLoc.value = addropu;  
        } else {
          valLoc.value = addrheadq;
        }
        valLoc.setAttribute("readonly",true);
      } else if (this.value == 2) {
        valLoc.value = addrusr;
        valLoc.setAttribute("readonly",true);
      } else {
        valLoc.value = "";
        valLoc.removeAttribute("readonly");
      }
    }

    var dovalact = function(ev) {
      var selvehstr = "";
      if (parseInt(valVehid.value) == -1) {
        selvehstr = "__-__-__";
      } else {
        vehlist.forEach(function(ev) {
          if (ev[0] == parseInt(valVehid.value)) {
            selvehstr = ev[1];
          } 
        });
        if (selvehstr == "" && valVeh.value != "") {
          selvehstr = valVeh.value;
          var hassep = selvehstr.search("-");
          if (hassep >= 0) {
            selvehstr = selvehstr.replace(/[./_]/g,"-");
          } else {
            selvehstr = selvehstr.replace(/[ ./_]/g,"-");
          }
          selvehstr = selvehstr.toUpperCase();
        }
      }
      if (lastValst == 1) {
        var newvalarritem = Array(0,parseInt(valVehid.value),valVeh.value,parseInt(lastjrnid),outputCurDateTime(),parseInt(valKms.value),parseInt(valLocType.value),valLoc.value,null,0,0,"",0);
        dataarray[5].push(newvalarritem);
        if(dataarray[5].length > 224) {
          dataarray[5].shift();
        }
        var newvalarrjson = JSON.stringify(dataarray[5]);
        if (parseInt(valVehid.value) == 0) {
          var newveharritem = Array(0,valVeh.value);
          dataarray[3].push(newveharritem);
          var newveharrjson = JSON.stringify(dataarray[3]);
        }
        if (conStatus()) {
          syncbusy = true;
          // Compare allocations
          queryapi("usersapi.php",userid,"compare",5,newvalarrjson);
          // New vehicle
          if (parseInt(valVehid.value) == 0) {
            queryapi("usersapi.php",userid,"compare",3,newveharrjson);
          }
        } else {
          datajson[5] = newvalarrjson;
          localStorage.setItem("lsdata5",datajson[5]);
          datajson[3] = newveharrjson;
          localStorage.setItem("lsdata3",datajson[3]);
        }
      } else {
        // Fechar atual
        var closevalarritem = Array(
          parseInt(lastValid ),
          parseInt(valLastItem[1]),
          valLastItem[2],
          parseInt(lastjrnid),
          lastValstart,
          parseInt(valLastItem[5]),
          parseInt(valLastItem[6]),
          valLastItem[7],
          outputCurDateTime(),
          parseInt(valendKms.value),
          parseInt(valLocType.value),
          valLoc.value,
          1
        );
        dataarray[5].pop();
        dataarray[5].push(closevalarritem);
        // Inserir nova
        if (parseInt(valVehid.value) >= 0) {
          var newvalarritem = Array(0,parseInt(valVehid.value),valVeh.value,parseInt(lastjrnid),outputCurDateTime(),parseInt(valKms.value),parseInt(valLocType.value),valLoc.value,null,0,0,"",0);
          dataarray[5].push(newvalarritem);
          if(dataarray[5].length > 120) {
            dataarray[5].shift();
          }
          if (parseInt(valVehid.value) == 0) {
            var newveharritem = Array(0,valVeh.value);
            dataarray[3].push(newveharritem);
            var newveharrjson = JSON.stringify(dataarray[3]);
          }
        }
        var updatevalarrjson = JSON.stringify(dataarray[5]);
        if (conStatus()) {
          syncbusy = true;
          // Compare allocations
          queryapi("usersapi.php",userid,"compare",5,updatevalarrjson);
          // New vehicle
          if (parseInt(valVehid.value) == 0) {
            queryapi("usersapi.php",userid,"compare",3,newveharrjson);
          }
        } else {
          datajson[5] = updatevalarrjson;
          localStorage.setItem("lsdata5",datajson[5]);
          datajson[3] = newveharrjson;
          localStorage.setItem("lsdata3",datajson[3]);
        }
      }
      vehinfo.innerText = selvehstr;
      thisModalObj.hide();
      // Limpar campos
      valVehid.value = 0; valVeh.value = ""; valVeh.removeAttribute("readonly"); valKms.value = 0; valendKms.value = 0; valLocType.value = 0; valLoc.value = "";
    }

    thisModal.addEventListener("hide.bs.modal", function(ev) {
      doactBtn.removeEventListener("click", dovalact);
      valVehid.removeEventListener("change", changeveh);
      valLocType.removeEventListener("change", changeloc);
    });

    if (lastjrnst == 1) {
      alert("Só pode atribuir/trocar o veículo com jornada iniciada!");
    } else {
      thisModalObj.show();
      // Modal - operação
      valVehid.addEventListener("change", changeveh);
      valLocType.addEventListener("change", changeloc);
      // Allocation - ação
      doactBtn.addEventListener("click", dovalact);
    }
  }  

  var valBtn = document.querySelector('button[btnaction="val-mark"]');

  if (valBtn !== null) {
    valBtn.addEventListener("click", valMark);
  }
  /*----------  END SECTION  ----------*/

  /*----------  ACTIVITIES SECTION  ----------*/
  var actMark = function(ev) {
    var thisbtn = this;
    var thisact = thisbtn.id;
    var actcode = thisact.substr(-1);
    var btnnode = document.querySelector(".act-btn-node");
    var activebtns = Array.from(btnnode.querySelectorAll(".btnselected"));
    var curbtnid = 0;
    var curbtnnum = 0;
    // Verificar o estado da última comutação
    var actList = dataarray[6];
    var lastActrow = actList[actList.length-1];
    var lastactcode = lastActrow[1];
    // Verificar o estado da última jornada
    var jrnList = dataarray[4];
    var lastjrnst = jrnList[jrnList.length-1][10];
    // Verificar o estado da última alocação de viatura
    var valList = dataarray[5];
    var valLastItem = valList[valList.length-1];
    var lastValst = valLastItem[12];
    
    if (lastjrnst == 0 && actcode != lastactcode) {
      if (actcode == 1 && lastValst == 1) {
        alert("Comutar para condução exige atribuição de viatura!");
      } else {
        var lastjrnid = jrnList[jrnList.length-1][0];
        var lastjrnstart = jrnList[jrnList.length-1][4];
        var lastjrnend = (jrnList[jrnList.length-1][7] === null || jrnList[jrnList.length-1][7] == "") ? null : jrnList[jrnList.length-1][7];
        var lastValvid = valLastItem[1];
        var lastValvrg = valLastItem[2];
        if (lastValst == 1) {
          lastValvid = 0;
          lastValvrg = "";
        }
        activebtns.length > 0 &&
          activebtns.forEach(function(ev) {
            curbtnid = ev.id;
            curbtnnum = parseInt(curbtnid.slice(-1));
            ev.classList.remove("btnselected",actColors[curbtnnum],"bg-info");
          });
        curbtnid = thisbtn.id;
        curbtnnum = parseInt(curbtnid.slice(-1));
        thisbtn.classList.add("btnselected",actColors[curbtnnum]);
        lastActrow.splice(7,1,outputCurDateTime());
        let updateactarr = lastActrow;
        var newactarr = Array(0,parseInt(actcode),parseInt(userid),parseInt(lastjrnid),parseInt(lastValvid),lastValvrg,outputCurDateTime(),null);
        dataarray[6].pop();
        dataarray[6].push(updateactarr);
        dataarray[6].push(newactarr);
        if(dataarray[6].length > 1440) {
          dataarray[6].shift();
        }
        var updateactarrjson = JSON.stringify(dataarray[6]);
        if (conStatus()) {
          syncbusy = true;
          // Compare activities
          queryapi("usersapi.php",userid,"compare",6,updateactarrjson);
        } else {
          datajson[6] = updateactarrjson;
          localStorage.setItem("lsdata6",datajson[6]);
        }
        // Contador de comutação atual
        if (actclock !== null) {
          actclockwrp.classList.remove("text-primary","text-danger","text-success","text-warning");
          actclockwrp.classList.add(clockColors[parseInt(actcode)]);
          clearTimeout(acttimecount);
          sysACTClock(outputCurDateTime());
        }
        // Contadores acumulados
        let acttmcounters = actcounters(lastjrnid,lastjrnstart,lastjrnend,actList);
        if (rstclock !== null) {
          clearTimeout(rsttimecount);
          if (actcode == 0) {
            sysRSTClock(acttmcounters[0],true);
          } else {
            sysRSTClock(acttmcounters[0],false); 
          }
        }
        if (drvclock !== null) {
          clearTimeout(drvtimecount);
          if (actcode == 1) {
            sysDRVClock(acttmcounters[1],true);
          } else {
            sysDRVClock(acttmcounters[1],false); 
          }
        }
        if (dawclock !== null) {
          clearTimeout(dawtimecount);
          if (actcode  == 1 || actcode  == 2) {
            sysDAWClock(acttmcounters[4],true);
          } else {
            sysDAWClock(acttmcounters[4],false); 
          } 
        }
      }
    }
  }  

  actBtns.length > 0 &&
    actBtns.forEach(function(input) {
      input.addEventListener("click", actMark);
    });
  /*----------  END SECTION  ----------*/

  /*----------  REMARKS SECTION  ----------*/
  var rmkAdd = function(ev) {
    var thisbutton = this;
    var thisModal = document.querySelector("#remarkInterface");
    var addrmkBtn = thisModal.querySelector('button[btnaction="rnk-action"]');  
    var thisModalObj = new bootstrap.Modal(thisModal);

    var dormkact = function(ev) {
      var rptctn = document.querySelector("#rmkreport");
      var rmkstr = thisModal.querySelector("#remark").value;
      var newrmkarritem = Array(0,parseInt(userid),outputCurDateTime(),rmkstr);       
      dataarray[7].push(newrmkarritem);
      if(dataarray[7].length > 120) {
        dataarray[7].shift();
      }
      var newrmkarrjson = JSON.stringify(dataarray[7]);
      if (conStatus()) {
        syncbusy = true;
        // Compare journeys
        queryapi("usersapi.php",userid,"compare",7,newrmkarrjson);
      } else {
        datajson[7] = newrmkarrjson;
        localStorage.setItem("lsdata7",datajson[7]);
      }

      let rmkrow = document.createElement("span");
      let rmktext = document.createTextNode("");
      rmkrow.classList.add("d-block","w-100","p-2","bg-light");
      rmktext.data = newrmkarritem[2] + ": " + newrmkarritem[3];
      rmkrow.appendChild(rmktext);
      rptctn.prepend(rmkrow);

      thisModalObj.hide();
    }

    thisModal.addEventListener("hide.bs.modal", function(ev) {
      addrmkBtn.removeEventListener("click", dormkact);
    });

    thisModalObj.show();
    // Observação - ação
    addrmkBtn.addEventListener("click", dormkact);
  }  

  if (rmkbtn !== null) {
    rmkbtn.addEventListener("click", rmkAdd);
  }
  /*----------  END SECTION  ----------*/

  /*----------  ONLIME EVENT SECTION  ----------*/
  var intInc = 0;
  setInterval(function() {
    if (conStatus() && intInc > 0 && !syncbusy) {
      queryapi("usersapi.php",userid,"query",0,datajson[0]);
      queryapi("usersapi.php",userid,"query",1,datajson[1]);
      queryapi("usersapi.php",userid,"query",2,datajson[4]);
      queryapi("usersapi.php",userid,"compare",3,datajson[3]);
      queryapi("usersapi.php",userid,"compare",4,datajson[4]);
      queryapi("usersapi.php",userid,"compare",5,datajson[5]);
      queryapi("usersapi.php",userid,"compare",6,datajson[6]);
      queryapi("usersapi.php",userid,"compare",7,datajson[7]);
    }
    intInc = intInc+1;
  },60000);
});
/*----------  END  ----------*/

/*----------  ACTIVITIES REPORT  ----------*/
function generate_report(journeys) {
  let rptpanel = document.querySelector("#jrnreport");
  let revJrnList = dataarray[4];
  let revActList = dataarray[6];
  let rawJrnList = revJrnList.toReversed();
  let rptJrnList = rawJrnList.slice(0,journeys);
  rptpanel.innerHTML = "";
  rptJrnList.forEach(function(rptel) {
    let rawActList = revActList.filter(function(srcel) {
      return srcel[3] == rptel[0];
    });
    let rptActList = rawActList.toReversed();
    let rptActLen = rptActList.length;
    rptActList.forEach(function(actel,idx) {
      if (idx == 0) {
        appendjrnend(rptel);
      }
      appendactivity(actel);
      if (idx == rptActLen-1) {
        appendjrnstart(rptel);  
      }
    });
  });
}

var sendrpt = function(ev) {
  if (conStatus()) {
    var thisbutton = this;
    var thisModal = document.querySelector("#sendrptInterface");
    
    // Iniciar Modal
    var thisModalObj = new bootstrap.Modal(thisModal);
    
    thisModalObj.show();
  } else {
    alert("Ligação à rede indisponível!!");
  }
}

var sendrptBtn = document.querySelector('button[btnaction="send-report"]');

if (sendrptBtn !== null) {
  sendrptBtn.addEventListener("click", sendrpt);
}
/*----------  END  ----------*/

/*----------  REMARKS LIST  ----------*/
function generate_remarklist(journeys) {
  let rptpanel = document.querySelector("#rmkreport");
  let revRmkList = dataarray[7];
  let rawRmkList = revRmkList.toReversed();
  let rptRmkList = rawRmkList.slice(0,journeys);
  rptpanel.innerHTML = "";
  rptRmkList.forEach(function(rptel) {
    appendremark(rptel);
  });
}
/*----------  END  ----------*/