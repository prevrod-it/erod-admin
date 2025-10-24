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

function isInViewport(el) {
  const rect = el.getBoundingClientRect();
  return (
    rect.top >= 0 &&
    rect.left >= 0 &&
    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
    rect.right <= (window.innerWidth || document.documentElement.clientWidth)
  );
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

/*----------  AT FULL CONTENT LOADED  ----------*/
/*----------  START  ----------*/
window.addEventListener("DOMContentLoaded", function() {
  
  var body = document.body;
  var wheight = window.innerHeight;
  var userid = document.getElementById("loginuserid").value;
  var fullsw = document.querySelector('#fullsw');
  var logout = document.querySelector('#logout');
  
  logout.addEventListener("click", function(ev) {
    if (conStatus()) {
      location.replace("/index.php?role=public&pg=logout");
    } else {
      alert("Não é possível terminar sessão sem ligação à internet!");
    }
  });

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
})
/*----------  END  ----------*/