<?php

# Models という名前空間の宣言が必要
namespace Models;

class User {
    private static $id;
    private static $name;
    private static $balance;
    private $user_id;
    private $user_list;
    public const FILE_PATH = './csv/user_list.csv';


    public function __construct() {
        $this->setUserList();
    }

    private function setUserList() {
        $this->fileCheck();
        $line_count = 0;
        $fp = fopen(self::FILE_PATH, 'r');
        if (!fgetcsv($fp)) {
            echo 'ファイルにデータがありません' . PHP_EOL;
            echo 'ファイルにデータを入れてください' . PHP_EOL;
            exit;
        }
        fclose($fp);
        $fp = fopen(self::FILE_PATH, 'r');
        while (!feof($fp)) {
            $line = fgetcsv($fp);
            if ($line) {
                if ($line_count === 0) {
                    $header = str_replace(' ', '', $line);
                    $line_count++;
                    continue;
                }
                $user = array_combine($header, $line);
                $this->user_list[$line_count] = $user;
                $line_count++;
            }
        }
    }

    public function fileCheck() {
        if (!file_exists(self::FILE_PATH)) {
            echo 'ファイルがありません。' . PHP_EOL;
            echo 'ファイルを作成します・・・' . PHP_EOL;
            if (file_exists('./csv/')) {
                $fp = fopen(self::FILE_PATH, 'w');
                fclose($fp);
                echo '作成に成功しました。';
                exit;
            }
            if (mkdir('./csv', 0777)) {
                $fp = fopen(self::FILE_PATH, 'w');
                fclose($fp);
                echo '作成に成功しました。';
                exit;
            }else {
                echo '作成に失敗しました。';
                exit;
            }
        }
    }

    public function getUserList() {
        return $this->user_list;
    }

    public function writeUserList($id, $value) {
        $this->user_list[$id] = $value;
        return $this->user_list;
    }

    public function overWrite() {                   
        $fp = fopen(User::FILE_PATH, 'w');
        $header = 'id, name, password, balance';
        fwrite($fp, $header . PHP_EOL);
        $user_list = $this->getUserList();
        foreach ($user_list as $user) {
            fputcsv($fp, $user);
        }
        fclose($fp);
    }

    public function getUserById($id) {
        $line_count = 0;
        $fp = fopen(self::FILE_PATH, 'r');
        if ($fp) {
            while (!feof($fp)) {
                $user = fgetcsv($fp);
                $user = str_replace(' ', '', $user);
                if ($line_count === 0) {
                    $header = $user;
                }
                if ($line_count === $id) {
                    $this->user_id = array_combine($header, $user);
                }
                $line_count++;
            }
            return $this->user_id;
            fclose($fp);
        }else {
            return;
        }
    }

    public function checkUserList($id) {
        if (!isset($this->user_list[$id])) {
            return false;
        }
        return true;
    }
}

