function popup(event_id) {
  //alert('test');
  
  let modalContent = '<h2>Event Id - '+event_id+ '</h2><div class="content">Thank to pop me out of that button</div>';


  let modal = document.createElement("div"),
     
  modalClose = '<button class="js-modal-close" id="js_modal_close">X</button>',
  theBody = document.getElementsByTagName('body')[0];

  // Add content and attributes to the modal
  modal.setAttribute("class", "js-modal");

  document.head.innerHTML += '<link href="http://localhost/ytcr_backend/public/assets/css/style1.css" rel="stylesheet" type="text/css" />';

  modal.innerHTML = '<div class="js-modal-inner">' + modalContent + modalClose + '</div>';
  theBody.appendChild(modal);

  modalClose = document.querySelector("#js_modal_close");

  // Close the modal on button-click
  if(modalClose) {
    modalClose.addEventListener('click', function() {
      modal.remove();
      //modalStyle.remove();
    });
  }
  
}

