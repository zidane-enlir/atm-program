<?php

# Models という名前空間の宣言が必要
namespace Models;


use Models\User;
use Validations\MenuValidation;
use Validations\PasswordValidation;
use Validations\MoneyValidation;
use Validations\NameValidation;
use Validations\IdValidation;

class ATM {
    public $playUser;
    public $user_client;
    public $transferUser;
    private const MENU_TYPE_BALANCE = 1;
    private const MENU_TYPE_DEPOSIT = 2;
    private const MENU_TYPE_WITHDRAWAL = 3;
    private const MENU_TYPE_TRANSFERMONEY = 4;
    private const MENU_TYPE_SELECTUSER = 5;
    private const MENU_TYPE_END = 6;
    private const INPUT_TYPE_MENU = 'menu_info';
    private const INPUT_TYPE_PASSWORD = 'password_number';
    private const INPUT_TYPE_MONEY = 'money';
    private const INPUT_TYPE_ID = 'id';
    private const INPUT_TYPE_NAME = 'name';
    private const PASSWORD_INPUT_COUNT = 4;


    public function  __construct() {
        $this->user_client = new User();
        $this->selectUser();
    }

    public function selectUser() {
        echo 'IDを入力してください。' . PHP_EOL;
        $user_id = $this->input('id');
        if (!$this->user_client->checkUserList($user_id)) {
            echo 'IDが見つかりません。' . PHP_EOL;
            return $this->selectUser();
        }
        if ($this->playUser['id'] === $user_id) {
            echo 'あなたのIDです。' . PHP_EOL;
            return $this->selectUser();
        }
        $this->playUser = $this->user_client->getUserById((int)$user_id);
    }

    public function play() {
        echo '1:残高照会, 2:入金, 3:引き出し, 4:振込, 5:ユーザーを切り替え, 6:終了' . PHP_EOL;
        echo 'ご利用のメニュー番号を入力してください。' . PHP_EOL;
        switch ($this->input('menu_info')) {
            case self::MENU_TYPE_BALANCE:
                $this->balanceInquiry();
                return $this->play();
                break;
            case self::MENU_TYPE_DEPOSIT:
                $this->deposit();
                return $this->play();
                break;
            case self::MENU_TYPE_WITHDRAWAL:
                $this->withdrawal();
                return $this->play();
                break;
            case self::MENU_TYPE_TRANSFERMONEY:
                $this->transferMoney();
                return $this->play();
                break;
            case self::MENU_TYPE_SELECTUSER:
                $this->selectUser();
                return $this->play();
                break;
            case self::MENU_TYPE_END:
                echo 'ご利用ありがとうございました。' . PHP_EOL;
                break;
        }
    }

    public function balanceInquiry() {
        $this->checkPassword();
        $balance = $this->playUser['balance'];
        $balance = number_format($balance);
        echo '￥' . $balance . PHP_EOL;
    }

    public function deposit() {
        $balance = $this->playUser['balance'];
        $deposit_amount;
        echo '入金額を入力してください。' . PHP_EOL;
        $deposit_amount = $this->input('money');
        if ($deposit_amount > 500000) {
            echo '入金額が多すぎます' . PHP_EOL;
            return;
        }
        $balance += $deposit_amount;
        $deposit_amount = number_format($deposit_amount);
        echo '￥' . $deposit_amount . '入金しました。' . PHP_EOL;
        $this->playUser['balance'] = $balance;
        $user_list = $this->user_client->getUserList();
        $user_list[$this->playUser['id']] = $this->playUser;
        $this->user_client->writeUserList($this->playUser['id'], $user_list[$this->playUser['id']]);
        $this->user_client->overWrite();
    }


    public function withdrawal() {
        $balance = $this->playUser['balance'];
        $withdrawal_amount;
        $this->checkPassword();
        echo '出金額を入力してください。' . PHP_EOL;
        $withdrawal_amount = $this->input('money');
        if ($balance  < $withdrawal_amount) {
            echo '引き出し金額が残高を超えています。' . PHP_EOL;
            return;
        }
        $balance -= $withdrawal_amount;
        $withdrawal_amount = number_format($withdrawal_amount);
        echo '￥' . $withdrawal_amount . '引きだしました。' . PHP_EOL;
        $this->playUser['balance'] = $balance;
        $user_list = $this->user_client->getUserList();
        $user_list[$this->playUser['id']] = $this->playUser;
        $this->user_client->writeUserList($this->playUser['id'], $user_list[$this->playUser['id']]);
        $this->user_client->overWrite();
    }

    public function transferMoney() {
        $balance = $this->playUser['balance'];
        $transferMoney_amount;
        echo '振込先の名前を入力してください'. PHP_EOL;
        $transferName = $this->input('name');
        $user_list = $this->user_client->getUserList($transferName);
        foreach ($user_list as $user) {
            if ($user['name'] === $transferName) {
                $this->transferUser = $user;
            }
        }
        if (!$this->transferUser) {
            echo '振込先がありません' . PHP_EOL;
            return $this->transferMoney();
        }
        if($this->playUser === $this->transferUser) {
            echo 'あなたの口座です。' . PHP_EOL;
            return $this->transferMoney();
        }
        echo '振込金額を入力してください。' . PHP_EOL;
        $transferMoney_amount = $this->input('money');
        if ($balance  < $transferMoney_amount) {
            echo '振込金額が残高を超えています。' . PHP_EOL;
            return $this->transferMoney();
        }
        $balance -= $transferMoney_amount;
        $this->transferUser['balance'] += $transferMoney_amount;
        $transferMoney_amount = number_format($transferMoney_amount);
        echo '￥' . $transferMoney_amount . '振り込みました' . PHP_EOL;
        $this->playUser['balance'] = $balance;
        $user_list = $this->user_client->getUserList();
        $user_list[$this->playUser['id']] = $this->playUser;
        $this->user_client->writeUserList($this->playUser['id'], $user_list[$this->playUser['id']]);
        $this->user_client->overWrite();
        $user_list = $this->user_client->getUserList();
        $user_list[$this->transferUser['id']] = $this->transferUser;
        $this->user_client->writeUserList($this->transferUser['id'], $user_list[$this->transferUser['id']]);
        $this->user_client->overWrite();
    }

    public function checkPassword() {
        for ($i=1; $i <= self::PASSWORD_INPUT_COUNT; $i++) {
            echo '暗証番号を入力してください' . PHP_EOL;
            $password = $this->input('password_number');
            $password = strval($password);
            if (!($this->playUser["password"] === $password)) {
                echo '暗証番号が違います。' . PHP_EOL;
            }
            if ($this->playUser["password"] === $password) {
                return true;
            }
            if ($i === self::PASSWORD_INPUT_COUNT) {
                exit('強制終了します。');
            }
        }
    }

    public function input($type) {
        $input = trim(fgets(STDIN));
        switch ($type) {
            case self::INPUT_TYPE_MENU:
                $check = MenuValidation::check($input);
                break;
            case self::INPUT_TYPE_PASSWORD:
                $check = PasswordValidation::check($input);
                break;
            case self::INPUT_TYPE_MONEY:
                $check = MoneyValidation::check($input);
                break;
            case self::INPUT_TYPE_ID:
                $check = IdValidation::check($input);
                break;
            case self::INPUT_TYPE_NAME:
                $check = NameValidation::check($input);
                break;
        }
        if (!$check) {                                          
            return $this->input($type);
        }
        return $input;
    }

}

