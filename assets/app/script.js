/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */



// start the Stimulus application

import '../bootstrap';

require('bootstrap');

require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');

const $ = require('jquery');

global.$ = global.jQuery = $;

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

$("#legalAgeTrue").on( "click", function() {
    sessionStorage.setItem("legalAge", 1);
    window.location.reload();
} );

$("#legalAgeFalse").on( "click", function() {
    window.location.href='https://www.jeunesetalcool.be/';
} );

if(sessionStorage.getItem("legalAge") == 1){
    $('#modalLegalAge').modal('hide');
}else{
    $('#modalLegalAge').modal('show');
}

const clearInput = () => {
    const input = document.getElementsByTagName("input")[0];
    input.value = "";
}
  
const clearBtn = document.getElementById("clear-btn");
clearBtn.addEventListener("click", clearInput);

window.onscroll = function() {myFunction()};

var topNav = document.getElementById("myTopNav");
var sticky = topNav.offsetTop;

function myFunction() {
  if (window.pageYOffset > sticky) {
    topNav.classList.add("sticky");
  } else {
    topNav.classList.remove("sticky");
  }
}
  
