var appVersion = "9.1.3";

window.onload = function() {
    if ('caches' in window) {
      caches.keys().then(function(keyList) {
        keyList.forEach(function(key) {
          caches.delete(key);
        });
      });
    }
    var storedVersion = localStorage.getItem('appVersion');
    if (storedVersion !== appVersion) {
      var confirmed = confirm("نسخه جدیدی از نرم افزار در دسترس است. آپدیت میکنید؟");
      if (confirmed) {
        localStorage.setItem('appVersion', appVersion);
        
        // window.location.reload(true);
        if ('caches' in window) {
          window.caches.keys().then(function(keyList) {
            keyList.forEach(function(key) {
              caches.delete(key);
            });
          });
        }
        var scripts = document.getElementsByTagName('script');
        for (var i = 0; i < scripts.length; i++) {
          var script = scripts[i];
          if (script.src) {
            script.src = script.src + '?v=' + appVersion;
          }
        }
        var links = document.getElementsByTagName('link');
        for (var i = 0; i < links.length; i++) {
          var link = links[i];
          if (link.rel === 'stylesheet' && link.href) {
            link.href = link.href + '?v=' + appVersion;
          }
        }
        window.location.reload(true);
      }
    }
  };
  