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
            if (inp_day === day) {
                html += '<option value="' + day + '" selected>' + day + '</option>';
            } else {
                html += '<option value="' + day + '">' + day + '</option>';
            }
        }
    }
    document.getElementById('day').innerHTML = html;
}

// targetDay();
// document.getElementById('year').addEventListener('change', targetDay);
// document.getElementById('month').addEventListener('change', targetDay);



window.onload = function() {
    targetDay();
    document.getElementById('year').addEventListener('change', targetDay);
    document.getElementById('month').addEventListener('change', targetDay);
    // リダイレクトした際に元の入力値を復元
    const dayElem = document.getElementById('day');
    dayElem.value = dayElem.getAttribute('data-old-value');
}






// window.addEventListener('load', function(){
//     targetDay();
//     const dayElem = document.getElementById('day');
//     dayElem.value = dayElem.getAttribute('data-old-value');
// })

// targetDay();
// document.getElementById('year').addEventListener('change', targetDay);
// document.getElementById('month').addEventListener('change', targetDay);

// window.onload = function() {
//     // document.getElementById('year').addEventListener('change', targetDay);
//     // document.getElementById('month').addEventListener('change', targetDay);
//     targetDay();
//     // リダイレクトした際に元の入力値を復元
//     const dayElem = document.getElementById('day');
//     dayElem.value = dayElem.getAttribute('data-old-value');
// }
// document.getElementById('year').addEventListener('change', targetDay);
// document.getElementById('month').addEventListener('change', targetDay);
