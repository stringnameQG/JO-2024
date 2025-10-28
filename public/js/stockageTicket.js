window.onload = () => {
  const INPUTTICKET   = document.querySelectorAll("input");
  let arrayListTicket = JSON.parse(localStorage.getItem("listTicket")); 

  if (arrayListTicket != null) {
    Object.keys(arrayListTicket).forEach(function (element) {
      const KEY = element.split("nombreDePlace");
      if (INPUTTICKET[KEY[1]] - 1 != undefined) {
        INPUTTICKET[[KEY[1]] - 1].value = arrayListTicket[element];
      }
    });
  }
  
  INPUTTICKET.forEach(element => {
    element.addEventListener("click", function () {
      arrayListTicket[element.id] = element.value;
      localStorage.setItem("listTicket", JSON.stringify(arrayListTicket));
    })
  });
}