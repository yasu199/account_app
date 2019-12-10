// 色を自動で選択させる関数
function rand_color() {
    var r = Math.floor(Math.random() * 255);
    var g = Math.floor(Math.random() * 255);
    var b = Math.floor(Math.random() * 255);
    var a = Math.floor(Math.random() * 4 + 4) / 10;
    return 'rgba(' + r + ',' + g + ',' + b + ',' + a + ')';
}

window.onload = function() {
    // 期間の日付を取得
    var first_year = document.getElementById('first_year').dataset.val;
    var first_month = document.getElementById('first_month').dataset.val;
    var last_year = document.getElementById('last_year').dataset.val;
    var last_month = document.getElementById('last_month').dataset.val;

    // セットする用のデータを作る
    // 日付データ
    var selected_date = document.getElementById('selected_date').dataset.val;
    // コンマ区切りを配列に変換
    selected_date = selected_date.split(',');
    // ジャンルidのデータ
    var genre_id = document.getElementById('genre_id').dataset.val;
    genre_id = genre_id.split(',');
    // ジャンル名のデータ
    var genre_name = document.getElementById('genre_name').dataset.val;
    genre_name = genre_name.split(',');
    // 支払いのデータ
    var i = 0;
    var payment_box = [];
    var elm = '';
    while ((elm = document.getElementById('payment' + i)) !== null) {
        var payment = '';
        payment = elm.dataset.val;
        payment = payment.split(',');
        payment = payment.map(function(tmp) { return Number(tmp);});
        payment_box[i] = payment;
        i++;
    }

    // グラフ用のデータをセットする
    var graph_dataset = [];
    var length = genre_id.length;
    for(var i = 0; i < length; i++) {
        // 線の色を取得
        var color_str = rand_color();
        // 点の色を取得
        var point_color = color_str.toString().replace(/(0\.\d)/, '1');
        graph_dataset[i] =
            {
                label: genre_name[i],
                data: payment_box[i],
                fill: false,
                borderColor: color_str,
                backgroundColor: color_str,
                pointBorderColor: point_color,
            };
    }


    var ctx = document.getElementById("myChart");
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: selected_date,
            datasets: graph_dataset,
        },
        options: {
            title: {
                display: true,
                text: '支払データ（' + first_year + '年' + first_month + '月～' + last_year + '年' + last_month + '月）'
            },
            scales: {
                yAxes: [{
                    ticks: {
                        callback: function (value, index, values) {
                            // 三桁カンマ区切りにする
                            return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + '円';
                        }
                    }
                }]
            },
            tooltips: {
                callbacks: {
                    label: function (tooltipValue, data) {
                        var tmp_data = tooltipValue.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                        return data.datasets[tooltipValue.datasetIndex].label + ':' + tmp_data + '円';
                    }
                }
            }

        }
      });
}
