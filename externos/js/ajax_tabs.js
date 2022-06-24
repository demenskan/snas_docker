/********************************************************************************************/
/* AJAX Simple Tabs by developersnippets, This code is intended for practice purposes.      */
/* You may use these functions as you wish, for commercial or non-commercial applications,  */
/* but please note that the author offers no guarantees to their usefulness, suitability or */
/* correctness, and accepts no liability for any losses caused by their use.                */
/********************************************************************************************/

var req;
function callPage(pageUrl, divElementId, loadinglMessage, pageErrorMessage) {
     document.getElementById(divElementId).innerHTML = loadinglMessage;
     try {
     req = new XMLHttpRequest(); /* e.g. Firefox */
     } catch(e) {
       try {
       req = new ActiveXObject("Msxml2.XMLHTTP");  /* some versions IE */
       } catch (e) {
         try {
         req = new ActiveXObject("Microsoft.XMLHTTP");  /* some versions IE */
         } catch (E) {
          req = false;
         } 
       } 
     }
     req.onreadystatechange = function() {responsefromServer(divElementId, pageErrorMessage);};
     req.open("GET",pageUrl,true);
     req.send(null);
  }

function responsefromServer(divElementId, pageErrorMessage) {
   var output = '';
   if(req.readyState == 4) {
      if(req.status == 200) {
         output = req.responseText;
         document.getElementById(divElementId).innerHTML = output;
         } else {
         document.getElementById(divElementId).innerHTML = pageErrorMessage+"\n"+output;
         }
      }
  }
  
/* This Function is for Tab Panels */
function activeTab(tab, piTor, piSsn, piAux)
	{   
		document.getElementById("tab1").className = "";
		document.getElementById("tab2").className = "";
		document.getElementById("tab3").className = "";
		document.getElementById("tab4").className = "";
		document.getElementById("tab5").className = "";
		document.getElementById("tab6").className = "";
		document.getElementById("tab7").className = "";
		document.getElementById("tab8").className = "";
		document.getElementById("tab"+tab).className = "active";
		switch (tab) {
			case 1:
				callPage('Listado/xi_tabla_general.php?tor='+piTor+'&ssn='+piSsn, 'content', '<img src=\"img/loading.gif\" /> Content is loading, Please Wait...', 'Error in Loading page <img src=\"img/error_caution.gif\" />');
				break;
			case 2:
				callPage('Listado/xi_tabla_x_grupos.php?tor='+piTor+'&ssn='+piSsn, 'content', '<img src=\"img/loading.gif\" /> Content is loading, Please Wait...', 'Error in Loading page <img src=\"img/error_caution.gif\" />');
				break;
			case 3:
				callPage('Listado/xi_calendario_regular.php?tor='+piTor+'&ssn='+piSsn+'&jor='+piAux, 'content', '<img src=\"img/loading.gif\" /> Content is loading, Please Wait...', 'Error in Loading page <img src=\"img/error_caution.gif\" />');
				break;
			case 4:
				callPage('Listado/xi_calendario_equipos.php?tor='+piTor+'&ssn='+piSsn+'&tm='+piAux, 'content', '<img src=\"img/loading.gif\" /> Content is loading, Please Wait...', 'Error in Loading page <img src=\"img/error_caution.gif\" />');
				break;
			case 5:
				callPage('Listado/xi_playoffs.php?tor='+piTor+'&ssn='+piSsn, 'content', '<img src=\"img/loading.gif\" /> Content is loading, Please Wait...', 'Error in Loading page <img src=\"img/error_caution.gif\" />');
				break;
			case 6:
				callPage('Listado/xi_goleo.php?tor='+piTor+'&ssn='+piSsn, 'content', '<img src=\"img/loading.gif\" /> Content is loading, Please Wait...', 'Error in Loading page <img src=\"img/error_caution.gif\" />');
				break;
			case 7:
				callPage('Listado/xi_tarjetas.php?tor='+piTor+'&ssn='+piSsn, 'content', '<img src=\"img/loading.gif\" /> Content is loading, Please Wait...', 'Error in Loading page <img src=\"img/error_caution.gif\" />');
				break;
			case 8:
				callPage('Listado/xi_detalles_partido.php?tor='+piTor+'&ssn='+piSsn+'&cve='+piAux, 'content', '<img src=\"img/loading.gif\" /> Content is loading, Please Wait...', 'Error in Loading page <img src=\"img/error_caution.gif\" />');
				break;
		}	
	}