<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class GenresTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('genres')->insert([
          [
            'genre_name' => '食費',
            'status' => 1
          ],
          [
            'genre_name' => '日用品費',
            'status' => 1
          ],
          [
            'genre_name' => '美容費',
            'status' => 1
          ],
          [
            'genre_name' => '交通費',
            'status' => 1
          ],
          [
            'genre_name' => '交際費',
            'status' => 1
          ],
          [
            'genre_name' => '娯楽費',
            'status' => 1
          ],
          [
            'genre_name' => 'その他支出',
            'status' => 1
          ],
          [
            'genre_name' => '住居費',
            'status' => 2
          ],
          [
            'genre_name' => '水光熱費',
            'status' => 2
          ],
          [
            'genre_name' => '電話料金',
            'status' => 2
          ],
          [
            'genre_name' => '保険料',
            'status' => 2
          ],
          [
            'genre_name' => '教育費',
            'status' => 2
          ]
        ]);
    }
}
