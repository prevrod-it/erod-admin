window.addEventListener("DOMContentLoaded", function() {

  function maskpwd(pwdstr) {
    var len = pwdstr.length;
    var strmask = "";
    var n = 0;
    var pos = 0;
    for (var i = 1; i <= len; i++) {
      pos = i%2;
      if (pos == 0) {
        n = Math.floor((Math.random() * 10));
      } else {
        n = String.fromCharCode(Math.floor((Math.random() * 10)) + 97);
      }
      
      strmask += n;
    }
    var maskedpwd = strmask;
    
    return maskedpwd;
  }

  function encrypt(str) {
    
    var date = new Date();
    var wday = date.getUTCDay().toString();
    var h = "0" + date.getUTCHours().toString();
      h = h.substr(-2);
    var m = "0" + date.getUTCMinutes().toString();
      m = m.substr(-2);
    var tc = m + wday + h;
      tc = tc.split("").reverse().join("");
    var strenc = "";
    var i = 0;

    str = str.split("").reverse().join("");
    
    while (i < str.length) {
        var n = str.charAt(i);
        n = String.fromCharCode(n.charCodeAt(0) + 5);
        if (n == n.toUpperCase()) {           
            n = n.toLowerCase() + (i%10);
        } else {
            n = n.toUpperCase() + (i%10);
        }
        i += 1;
        strenc += n; 
    }
    var result = strenc + tc;

    return result;
  }

  /*----------  SEND LOGIN FORM  ----------*/
  var loginFormSend = function(ev) {   
    var curform = this.form;
    var pwdfld = document.getElementById('ppwd');
    var pwdencfld = document.getElementById('vardumb');
    
    var orgnpwd = pwdfld.value;
    var maskedpwd = maskpwd(orgnpwd);
    var pwdenc = encrypt(orgnpwd);

    pwdfld.value = maskedpwd;
    pwdencfld.value = pwdenc;
    curform.submit(); 
  }

  var loginFld = document.querySelector('#pusr');
  var loginpwdFld = document.querySelector('#ppwd');
  var loginBtn = document.querySelector('#login');
  var lgnstatus = document.querySelector('#login-form-status');
 
  loginFld.addEventListener("keyup", function(event) {
    if (event.keyCode === 9 || event.keyCode === 13) {
      event.preventDefault();
      loginpwdFld.focus();
    }
  });
  loginFld.addEventListener("focus", function(event) {
    lgnstatus.classList.replace("d-block","d-none");
  });

  if (navigator.userAgent.match(/Android/i) || navigator.userAgent.match(/webOS/i)) {
    loginpwdFld.addEventListener("keypress", function(event) {
      if (event.keyCode === 13) {
        event.preventDefault();
        loginBtn.click();
      }
      lgnstatus.classList.replace("d-block","d-none");
    });
  } else {
    loginpwdFld.addEventListener("keyup", function(event) {
      if (event.keyCode === 13) {
        event.preventDefault();
        loginBtn.click();
      }
      lgnstatus.classList.replace("d-block","d-none");
    });
  }

  loginBtn.addEventListener("click", loginFormSend);
});