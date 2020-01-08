// 入力が終了し、カーソルが離れているときはカンマ区切りにする

function separator_conma(value) {
    // 空のデータの場合にはそのまま返却
    if (value === '') {
        return '';
    }
    // 数値以外の入力があった場合はそのまま返却する。
    // var num_value = toHalfWidth(value).replace(/,/g,"").trim();
    var num_value = value.toString().replace(/,/g, "").trim();
    if (/^[+|-]?(\d*)(\.\d+)?$/.test(num_value) === false) {
        return value;
    }
    // 小数部分と整数部分を分離
    var num_data = num_value.toString().split('.');
    // 整数部分について、コンマ区切りを適用
    num_data[0] = Number(num_data[0]).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    // 小数部分と結合する
    return num_data.join('.');
}

// 入力中はカンマ区切りを消去
function del_separator_conma(value) {
    return value.replace(/,/g,'');
}


// submit時にはカンマを消去
function replace_inp(i) {
    var elm = '';
    elm = document.getElementById('payment' + i);
    elm.value = del_separator_conma(elm.value);
    return true;
}

window.onload = function() {
  var i = 1;
  var elm_payment = '';
  while((elm_payment = document.getElementById('payment' + i)) !== null) {
      elm_payment.value = separator_conma(elm_payment.value);
      elm_payment.addEventListener('blur',  function(){ this.value = separator_conma(this.value);}, false);
      elm_payment.addEventListener('focus', function(){ this.value = del_separator_conma(this.value);}, false);
      i++;
  }
  // メッセージを表示する部分
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
