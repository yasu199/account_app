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
function replace_inp() {
    var i = 1;
    var elm = '';
    while((elm = document.getElementById('numdata' + i)) !== null) {
        elm.value = del_separator_conma(elm.value);
        i++;
    }
}

// 日付操作のための関数
// 入力日の取得
function targetDay() {



    // 年数を取得
    const year = document.getElementById('year').value;
    // 月の取得
    const month = document.getElementById('month').value;

    // 入力した日の取得
    const inp_day = Number(document.getElementById('day').value);
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
    document.getElementById('day').innerHTML = html;
}


window.onload = function() {
  targetDay();
  document.getElementById('year').addEventListener('change', targetDay);
  document.getElementById('month').addEventListener('change', targetDay);
  // リダイレクトした際に元の入力値を復元
  const dayElem = document.getElementById('day');
  dayElem.value = dayElem.getAttribute('data-old-value');

  // 処理をするタイミングをイベント処理する
  // separator_conma:コンマ区切りにする
  // del_separator_conma:コンマ区切りから変更
  var i = 1;
  var elm = '';
  while((elm = document.getElementById('numdata' + i)) !== null) {
      elm.value = separator_conma(elm.value);
      elm.addEventListener('blur', function(){ this.value = separator_conma(this.value);}, false);
      elm.addEventListener('focus', function(){ this.value = del_separator_conma(this.value) }, false);
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
