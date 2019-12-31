<?php

# Validations という名前空間の宣言が必要
namespace Validations;

class MenuValidation {

    public static function check($input) {                                               //入力関数
        $check = self::checkMenuNumber($input);
        if (!$check) {
            return false;
        }
        return true;
    }

    public static function checkMenuNumber($input) {                              //メニュー番号のバリデーションチェック
        $errors = array();
        if ($input === '') {
            $errors[] = '未入力です';
        }
        if (!ctype_digit(strval($input))) {
            $errors[] = '整数を入力してください';
        }
        if (!($input >= 1 && $input <= 6)) {
            $errors[] = '[1, 2, 3, 4, 5, 6]を入力してください。' . PHP_EOL;
        }
        if ($errors) {
            foreach ($errors as $error) {
                echo $error . PHP_EOL;
            }
            return false;
        }
        return true;
    }
}
