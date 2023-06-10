$('notif-dropbtn').click(function(){

    // do something.
   
    document.getElementById("notificationDropdown").classList.toggle("show");
  
  /*
  // Close the dropdown menu if the user clicks outside of it
  window.onclick = function(event) {
    if (!event.target.matches('.notification-dropbtn')) {
      var dropdowns = document.getElementsByClassName("notification-dropdown-content");
      var i;
      for (i = 0; i < dropdowns.length; i++) {
        var openDropdown = dropdowns[i];
        if (openDropdown.classList.contains('show')) {
          openDropdown.classList.remove('show');
        }
      }
    }*/
});