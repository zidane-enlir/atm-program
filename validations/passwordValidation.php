<?php

# Validations という名前空間の宣言が必要
namespace Validations;

class PasswordValidation {

    public static function check($input) {                                     //入力関数
        $check = self::checkNumber($input);
        if (!$check) {
            return false;
        }
        return true;
    }

    public static function checkNumber($input) {                              //番号のバリデーションチェック
        $errors = array();
        if ($input === '') {
            $errors[] = '未入力です';
        }
        if (ctype_digit(strval($input)) === false) {
            $errors[] = '整数を入力してください';
        }
        if (!preg_match('/^([0-9]{4})$/', $input)) {
            $errors[] = '4桁の数字を入力してください。';
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
