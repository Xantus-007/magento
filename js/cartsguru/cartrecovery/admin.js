/**
* Carts Guru
*
* @author    LINKT IT
* @copyright Copyright (c) LINKT IT 2017
* @license   Commercial license
*/
(function(document) {
  function switchView(view, backToView) {
    if (!view){
      view =  window.cg_backto;
      window.cg_backto = null;
    }

    var welcome = document.getElementById('cartsguru-welcome');
    welcome.className = view;
    window.cg_backto = backToView;
  }
  //Declare global functions
  window.cg_switchView = switchView;
})(document);
