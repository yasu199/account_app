// 色を自動で選択させる関数
function rand_color() {
    var r = Math.floor(Math.random() * 255);
    var g = Math.floor(Math.random() * 255);
    var b = Math.floor(Math.random() * 255);
    var a = Math.floor(Math.random() * 4 + 4) / 10;
    return 'rgba(' + r + ',' + g + ',' + b + ',' + a + ')';
}

// 円グラフを出力する関数
function circle_chart(elm, genre_name, inp_data, year, month, title) {
    // データラベルにパーセント表示させるプラグインを準備
    // データの合計値を算出（グラフを表示させるときにも使うので、dataLabel変数外で定義）
    // 合計はゼロで初期化
    var dataSum = 0;
    // データセットの中身のデータについて、要素をひとつづつ加算
    inp_data.forEach(function (dataset) {
        dataset.data.forEach(function (value) {
            dataSum += value;
        });
    });
    var dataLabel = {
        afterDatasetsDraw: function (chart) {
            var ctx = chart.ctx;
            // データセットをいじりたいので、foreachで要素を取得
            chart.data.datasets.forEach(function (dataset, i) {
                // // 合計はゼロで初期化
                // var dataSum = 0;
                // // データセットの中身のデータについて、要素をひとつづつ加算
                // dataset.data.forEach(function (value) {
                //     dataSum += value;
                // });

                // 現在のデータセットに一致するデータセットののmetaデータをいじるので、変数として格納
                var meta = chart.getDatasetMeta(i);
                if (!meta.hidden) {
                    meta.data.forEach(function (element, index) {
                          //フォントの設定をする
                          ctx.fillStyle = 'rgb(255, 255, 255)';
                          var fontSize = 12;
                          var fontStyle = 'normal';
                          var fontFamily = 'Helvetica Neue';
                          ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);
                          // 出力する文字を設定
                          var labelName = chart.data.labels[index];
                          // パーセントを算出するとともに出力用設定を進める
                          var percentage = Math.round(dataset.data[index] / dataSum * 100 * 10) / 10;
                          var dataPar = percentage.toString() + '%';

                          ctx.textAlign = 'center';
                          ctx.textBaseline = 'middle';

                          var padding = 5;
                          var position = element.tooltipPosition();
                          if (percentage >= 10) {
                              ctx.fillText(labelName + ':' + dataPar, position.x, position.y - (fontSize / 2) - padding);
                          }
                    });
                }
            });
        }
    };

  // 円グラフを表示させるための処理
    var myCircleChart = new Chart(elm, {
        type : 'pie',
        // genre_nameとかinp_dataとかは先に成形してから引数として渡されている
        data: {
            labels: genre_name,
            datasets: inp_data,
        },
        options: {
            title: {
                display: true,
                text: title + '(' + year + '年' + month + '月)'
            },
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, data) {
                        var tmp_data = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                        var percentage = Math.round(data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index] / dataSum * 100 * 10) / 10;
                        var return_label = [data.labels[tooltipItem.index] + ':' + tmp_data + '円', '割合' + ':' + percentage.toString() + '%'];
                        return return_label;
                        // return data.labels[tooltipItem.index] + ':' + tmp_data + '円' + "\n" + percentage.toString() + '%';
                    }
                }
            }
        },
        plugins: [dataLabel],
    });
}


// 棒グラフを出力する関数
function bar_chart(elm, genre_name, payment_data, year, month, title) {
    // グラフを出力するための処理
    var myBoubleBarChart = new Chart(elm, {
        type: 'bar',
        data: {
            labels: genre_name,
            datasets: payment_data,
        },
        options: {
            title: {
                display: true,
                text: title + '(' + year + '年' + month + '月)'
            },
            scales: {
                yAxes: [{
                    ticks: {
                        callback: function (value, index, values) {
                            // 三桁カンマ区切りにする
                            // return 'a';
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

window.onload = function() {
    // データを取得
    var genre_name = document.getElementById('genre_name').dataset.val;
    var selected_date = document.getElementById('selected_date').dataset.val;
    var selected_year = document.getElementById('selected_year').dataset.val;
    var selected_month = document.getElementById('selected_month').dataset.val;
    var budget = document.getElementById('budget').dataset.val;
    var payment = document.getElementById('payment').dataset.val;
    var exit_budget = document.getElementById('exit_budget').dataset.val;
    var exit_payment = document.getElementById('exit_payment').dataset.val;
    // セットする用のデータを作る
    // ジャンル名のデータ
    genre_name = genre_name.split(',');
    // 支払いのデータ
    payment = payment.split(',');
    payment = payment.map(function(tmp) { return Number(tmp);});
    budget = budget.split(',');
    budget = budget.map(function(tmp) { return Number(tmp);});
    // 色のデータをセット
    var colors = [];
    var length = genre_name.length;
    for (var i = 0; i < length; i++) {
        colors[i] = rand_color();
    }

    // グラフ用のデータをセットする
    var budget_circle_dataset = [];
    var payment_circle_dataset = [];
    var budget_bar_dataset = [];
    var payment_bar_dataset = [];
    var double_bar_payment = [];
    budget_circle_dataset[0] = {
        // label: genre_name,
        data: budget,
        backgroundColor: colors,
    };
    payment_circle_dataset[0] = {
        // label: genre_name,
        data: payment,
        backgroundColor: colors,
    };
    budget_bar_dataset[0] = {
        label: '予算データ',
        data: budget,
        backgroundColor: "rgba(117, 255, 126, 0.9)",
    };
    payment_bar_dataset[0] = {
        label: '支払データ',
        data: payment,
        backgroundColor: "rgba(89, 194, 255, 0.9)",
    };
    double_bar_payment[0] = budget_bar_dataset[0];
    double_bar_payment[1] = payment_bar_dataset[0];



    // グラフを作るときのidを取得し、グラフを作成
    if (exit_budget === '1') {
        var elm_circle_budget = document.getElementById("myCircleBudgetChart");
        // circle_chart(elm_circle_budget, genre_name, test, selected_year, selected_month, '予算データ');
        circle_chart(elm_circle_budget, genre_name, budget_circle_dataset, selected_year, selected_month, '予算データ');
        if (exit_payment === '1') {
            var elm_bar = document.getElementById("myBarChart");
            bar_chart(elm_bar, genre_name, double_bar_payment, selected_year, selected_month, '予算・支払データ');
        } else {
            var elm_budget_bar = document.getElementById("myBarBudgetChart");
            bar_chart(elm_budget_bar, genre_name, budget_bar_dataset, selected_year, selected_month, '予算データ');
        }
    }
    if (exit_payment === '1') {
        var elm_circle_payment = document.getElementById("myCirclePaymentChart");

        circle_chart(elm_circle_payment, genre_name, payment_circle_dataset, selected_year, selected_month, '支払データ');
        if (exit_budget === '0') {
            var elm_payment_bar = document.getElementById("myBarPaymentChart");
            bar_chart(elm_payment_bar, genre_name, payment_bar_dataset, selected_year, selected_month, '支払データ');
        }
    }
}
