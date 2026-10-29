window.onscroll = function() {myFunction()};

let header = document.getElementById("myHeader");
let filler = document.getElementById("filler")
let sticky = header.offsetTop;

function myFunction() {
  if (window.pageYOffset > sticky) {
    header.classList.add("sticky");
    filler.classList.add("visible")
    filler.classList.remove("invisible")

  } else {
    header.classList.remove("sticky");
    filler.classList.remove("visible")
    filler.classList.add("invisible")


  }
}