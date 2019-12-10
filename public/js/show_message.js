window.onload = function() {
  var message = document.getElementById('js-message');
  if(!message) return;
  message.classList.add('is-show');

  var blackBg = document.getElementById('js-black-bg');
  var closeBtn = document.getElementById('js-close-btn');

  close_message(blackBg);
  close_message(closeBtn);
  function close_message(elem) {
    if(!elem) return;
    elem.addEventListener('click', function() {
      message.classList.remove('is-show');
    })
  }
}
