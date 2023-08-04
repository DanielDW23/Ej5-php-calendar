
let modal = document.querySelector("#myModal");
let span = document.getElementsByClassName("close")[0];


span.onclick = function () {
  modal.style.display = "none";
};

window.onclick = function (event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
};

// let btnSlots = document.querySelectorAll(".openModalBtnSlot");


// btnSlots.forEach(function (btnSlot) {
//     btnSlot.onclick = function () {
//     modal.style.display = "block";
//   };
// });

const bookButtons = document.querySelectorAll('.book');

bookButtons.forEach(function(bookButton){
   bookButton.addEventListener('click', function(){
      let timeslot = bookButton.getAttribute('data-timeslot');
    //   document.getElementById('slot').innerHTML = timeslot;
      document.getElementById('timeslot').value = timeslot;
      document.getElementById('myModal').style.display = "block";
   });
});