// create a style element to hold the spinner CSS
var $style = $('<style>').text('.spinner { width: 50px; height: 50px; border-radius: 50%; border: 5px solid #ccc; border-top-color: #000; animation: spin 1s linear infinite; } @keyframes spin { to { transform: rotate(360deg); } }');

// create a div to hold the loader
var $loader = $('<div>', {
  'class': 'loader',
  'id': 'preloader',
  css: {
    'display': 'none',
    'position': 'fixed',
    'top': '0',
    'left': '0',
    'width': '100%',
    'height': '100%',
    'z-index': '9999',
    'background-color': 'rgba(255, 255, 255, 0.5)'
  }
});

// create the loader element
var $loaderElement = $('<div>', {
  'class': 'loader-element',
  css: {
    'display': 'flex',            // set display to flex
    'justify-content': 'center', // center horizontally
    'align-items': 'center',     // center vertically
    'position': 'absolute',
    'top': '50%',
    'left': '50%',
    'transform': 'translate(-50%,-50%)'
  }
});

// create the spinner element
var $spinner = $('<div>', {
  'class': 'spinner'
});

// create the text element
var $text = $('<div>', {
  'class': 'loader-text',
  text: 'منتظر بمانید',
  css: {
    'text-align': 'center',
    'font-size': '14px',
    'margin-top': '90px',
    'font-weight': 'bold',
    'position': 'absolute',
    'width': '200px'
  }
});

// add the style, spinner, and text to the loader element
$loaderElement.append($style).append($spinner).append($text);

// add the loader element to the loader
$loader.append($loaderElement);

// add the loader to the body
$('body').append($loader);

