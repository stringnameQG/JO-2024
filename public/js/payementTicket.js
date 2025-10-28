window.onload = () => {
  const ARRAYLISTTICKET = JSON.parse(localStorage.getItem("listTicket")); 
  console.log(ARRAYLISTTICKET);

  $.ajax({
    url: '/commandevalideupdate',
    method: 'GET',
    data: ARRAYLISTTICKET,
    dataType: "json",
    timeout: 10500,
    success: function(data) {
      localStorage.clear();
      console.log(localStorage.getItem("listTicket"));
    },
    error: function(jqXHR, textStatus, errorThrown){
      console.log(textStatus, errorThrown, jqXHR);
    }
  });
}