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

// toHalfWidthの実装
// function toHalfWidth(value) {
//     var half_value = value.replace(/[!-~]/g,
//         function( tmpStr ) {
//           // 文字コードをシフト
//           return String.fromCharCode( tmpStr.charCodeAt(0) - 0xFEE0 );
//         });
//     return half_value;
// }

// submit時にはカンマを消去
function replace_inp() {
    var i = 1;
    var elm = '';
    while((elm = document.getElementById('numdata' + i)) !== null) {
        elm.value = del_separator_conma(elm.value);
        i++;
    }
}

function replace_inp_fixed() {
    var i = 1;
    var elm = '';
    while((elm = document.getElementById('numdata_fixed' + i)) !== null) {
        elm.value = del_separator_conma(elm.value);
        i++;
    }
}
window.onload = function() {


  // 処理をするタイミングをイベント処理する
  // separator_conma:コンマ区切りにする
  // del_separator_conma:コンマ区切りから変更
  var i = 1;
  var elm = '';
  while((elm = document.getElementById('numdata' + i)) !== null) {
      elm.value = separator_conma(elm.value);
      elm.addEventListener('blur', function(){ this.value = separator_conma(this.value);}, false);
      elm.addEventListener('focus', function(){ this.value = del_separator_conma(this.value); }, false);
      i++;
  }
  var fixed_i = 1;
  var fixed_elm = '';
  while((fixed_elm = document.getElementById('numdata_fixed' + fixed_i)) !== null) {
      fixed_elm.value = separator_conma(fixed_elm.value);
      fixed_elm.addEventListener('blur', function(){ this.value = separator_conma(this.value);}, false);
      fixed_elm.addEventListener('focus', function(){ this.value = del_separator_conma(this.value); }, false);
      fixed_i++;
  }
}
