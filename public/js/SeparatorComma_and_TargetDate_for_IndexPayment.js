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

// 日付操作のための関数
// 入力日の取得
function targetDay(elm_year, elm_month, elm_day, input_day) {
    // 年数を取得
    const year = elm_year.value;
    // 月の取得
    const month = elm_month.value;

    // 入力した日の取得
    const inp_day = Number(input_day);
    // 日の部分を変数的に挿入するHTML
    let html = '';
    // 年月が有効な値か確認
    if (year !== '' && month !== '') {
        // 年月の最後の日付を取得する
        const last_day = (new Date(year, month, 0)).getDate();
        // htmlに挿入するoptionを月末まで設定する
        for (let day = 1; day <= last_day; day++) {
            if (day < 10) {
                if (inp_day === day) {
                    html += '<option value="0' + day + '" selected>0' + day + '</option>';
                } else {
                    html += '<option value="0' + day + '">0' + day + '</option>';
                }
            } else {
                if (inp_day === day) {
                    html += '<option value="' + day + '" selected>' + day + '</option>';
                } else {
                    html += '<option value="' + day + '">' + day + '</option>';
                }
            }
        }
    }
    elm_day.innerHTML = html;
}


window.onload = function() {
  var i = 1;
  var elm_year = '';
  var elm_month = '';
  var elm_day = '';
  var elm_payment = '';
  var payment_day = '';
  var old_day_flag = '';
  while((elm_payment = document.getElementById('payment' + i)) !== null) {
      elm_year        = document.getElementById('year'  + i);
      elm_month       = document.getElementById('month' + i);
      elm_day         = document.getElementById('day'   + i);
      if (elm_year !== null) {
          payment_day = document.getElementById('pay_day' + i).dataset.val;
          targetDay(elm_year, elm_month, elm_day, payment_day);
          if (elm_day.hasAttribute("data-old-value")) {
              elm_day.value = elm_day.getAttribute('data-old-value');
          }
      }
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
