<?php

# Validations という名前空間の宣言が必要
namespace Validations;

class MoneyValidation {

    public static function check($input) {                                     //入力関数
        $check = self::checkMoney($input);
        if (!$check) {                                          // エラーがある場合は、やり直し
            return false;
        }
        return true;
    }

    public static function checkMoney($input) {                               //金額のバリデーションチェック
        $errors = array();
        if ($input === '') {
            $errors[] = '未入力です';
        }
        if (ctype_digit(strval($input)) === false) {
            $errors[] = '整数を入力してください';
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
