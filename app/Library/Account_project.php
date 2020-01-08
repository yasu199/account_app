<?php
namespace App\Library;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Account_project {
    // 支払データをAnalyzePayment、ComparePaymentで使用するか判断するflag
    public const TO_DESIDE_USE_PAYMENT_FLAG = 1;
    public const TO_DESIDE_NOT_USE_PAYMENT_FLAG = 0;

    // genreが変動費が、固定費が判断するflag
    public const GENRE_STATUS_VARIABLE_COSTS = 1;
    public const GENRE_STATUS_FIXED_COSTS = 2;

    // DBよりデータが取得できたかを判断するflag
    public const DATA_EXIST = 1;
    public const DATA_NOT_EXIST = 2;

    // データがチェックされているかどうかを判断するstatus
    public const CHEAKED_STATUS     = 1;
    public const NOT_CHEAKED_STATUS = 0;

    // index_paymentでのsort順を示すflag
    public const SORT_BY_DATE_FLAG = 1;
    public const SORT_BY_GENRE_FLAG = 2;

    public static function get_todays_date() {
        return date('Ym');
    }

    // 'YYYYmm'から'YYYY'のみ取得
    public static function get_year($date) {
        return substr($date, 0, 4);
    }
    // 'YYYYmm'から'mm'のみ取得
    public static function get_month($date) {
        return substr($date, 4, 2);
    }
    // 'YYYYmmdd'より'dd'のみ取得
    public static function get_day($date) {
        return substr($date, 6, 2);
    }
    // 月末日付を返す
    public static function get_end_of_month(string $year, string $month) {
        $date = $year . '-' . $month;
        $day  = date('d', strtotime('last day of ' . $date));
        return $day;
    }

    // もしflagがtrueであれば加算して返す
    public static function increase_only_true (bool $flag, int $num) {
        if ($flag === true) {
            $num++;
        }
        return $num;
    }

    // メッセージの内容を決める
    public static function decide_message_by_num (int $update_num, int $create_num, string $message, string $other_message) {
        if ($update_num === 0 && $create_num !== 0) {
            return $other_message;
        } else {
            return $message;
        }
    }

    // 今から一年前のYYYYmmを返す
    public static function get_one_year_ago() {
        $now_date = self::get_todays_date();
        // 一年前に設定
        $now_date = (int) $now_date;
        $first_date = $now_date - 100;
        $first_date = (string) $first_date;
        return $first_date;
    }

    // comma区切りのデータを配列にして返す
    public static function explode_data_by_commma(string $data) {
        return explode(',', $data);
    }

    // viewへ渡すための年数のデータを返す関数
    public static function get_years_for_selected_by_users(int $start, int $end) {
        $years = [];
        for ($i = $start; $i <= $end; $i++) {
            $years[] = (string) $i;
        }
        return $years;
    }
    // viewへ渡すための月のデータを返す関数
    public static function get_months_for_selected_by_users() {
        $months = [];
        for ($i = 1; $i < 13; $i++) {
            $months[] = sprintf('%02d', $i);
        }
        return $months;
    }
    // viewへ渡すための月のデータを返す関数
    public static function get_day_for_selected_by_users(int $last) {
        $days = [];
        for ($i = 1; $i <= $last; $i++) {
            $days[] = sprintf('%02d', $i);
        }
        return $days;
    }

    // YYYYmmをYYYY/mmで返す
    public static function get_date_explode_slash($date) {
        $year = self::get_year($date);
        $month = self::get_month($date);
        return $year . '/' . $month;
    }
    // YYYY12のとき,YYY(Y+1)00として返す関数
    public static function change_next_year($date) {
        $month = self::get_month($date);
        if ($month === '12') {
            $date += 88;
        }
        return $date;
    }

    // 配列のデータをcomma区切りにして返す関数
    public static function change_comma_string(Array $data) {
        $return_value = '';
        foreach($data as $key => $value) {
            if ($key === 0) {
                $return_value = $value;
            } else {
                $return_value .= ', ' . $value;
            }
        }
        return $return_value;
    }

    public static function decide_message_by_return (int $return, string $message, string $other_message) {
        if ($return === 1) {
            return $message;
        } else {
            return $other_message;
        }
    }
    public static function decide_message_by_flag (bool $flag, string $message, string $other_message) {
        if ($flag === true) {
            return $message;
        } else {
            return $other_message;
        }
    }
    // モバイルか否か判定。pcならtrue、モバイルならfalse
    public static function decide_device ($request) {
        $device =  $request->header('User-Agent');
        if ((strpos($device, 'iPhone') !== false)
            || (strpos($device, 'iPod') !== false)
            || (strpos($device, 'Android') !== false)) {
            return false;
        } else {
            return true;
        }
    }

}
