<?php

# Validations という名前空間の宣言が必要
namespace Validations;

class NameValidation {

    public static function check($input) {                                     //入力関数
        $check = self::checkName($input);
        if (!$check) {                                          
            return false;
        }
        return true;
    }

    public static function checkName($input) {                               //名前のバリデーションチェック
        $errors = array();
        if ($input === '') {
            $errors[] = '未入力です';
        }
        if (!preg_match("/^[a-zA-Z]+$/", $input)) {
            $errors[] = '文字を入力してください';
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
