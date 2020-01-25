<?php
namespace CommonSetting;
require_once 'InitFunction.php';

$base = new Setting();
//Publicであればpublic/Setting.PHPを
//Adminであればadmin/Seetting.phpを
// どちらもなければこのページを参照(Topページ)
if (strpos($base->GetURL(), 'public')) {
    require_once ('public/Setting.php');
    return true;
} else if (strpos($base->GetURL(), 'admin')) {
    require_once ('admin/Setting.php');
    return true;
}

require_once AddPath(__DIR__, "Config.php", false);
$siteConfig = ['header' => new \Header(), 'footer' => new \Footer()];

if (isset($_SERVER['HTTPS'])) {
    $http = '//';
} else {
    $http = 'http://';
}
$domain = $_SERVER['SERVER_NAME'];
$url = $http . $domain;

// 定数などの定義
$COMMON_DIR = __DIR__;
$FUNCTION_DIR = $COMMON_DIR . '/Function';

// 設定関係のクラス化(実装中)
class Setting {

    private $domain, $url, $public;
    private $client, $css, $js, $image;

    function __construct() {
        // 基本設定
        $this->InitSSL($this->url);
        $this->domain = $this->GetSERVER('SERVER_NAME');
        $this->url = $this->url . $this->domain;
        $this->public = $this->url . '/public/';

        // 公開パス関係
        $this->client = $this->public . 'client/';
        $this->css = $this->client . 'css';
        $this->js = $this->client . 'js';
        $this->image = $this->client . 'image';
        $this->csv = $this->client . 'csv';
    }

    private function InitSSL(&$http) {
        $http_flg = $this->GetSERVER('HTTPS');
        if (isset($http_flg)) {
            $http = '//';
        } else {
            $http = 'http://';
        }
    }

    static private function GetSERVER($elm) {
        if (isset($_SERVER[$elm])) {
            return Sanitize($_SERVER[$elm]);
        } else {
            return null;
        }
    }

    static public function GetServarName() {
        return self::GetSERVER('SERVER_NAME');
    }

    static public function GetPropaty($elm) {
        if (property_exists('PublicSetting\Setting', $elm) !== false) {
            return $elm;
        } else {
            return null;
        }
    }

    static public function GetURI() {
        return self::GetSERVER('REQUEST_URI');
    }

    static public function GetScript() {
        return self::GetSERVER('SCRIPT_NAME');
    }

    static public function GetPosts() {
        return Sanitize($_POST);
    }

    // 指定した要素のPost値を取得
    static public function GetPost($elm = '') {
        $_post = Sanitize($_POST);
        if (key_exists($elm, $_post)) {
            return $_post[$elm];
        } else {
            return null;
        }
    }

    static public function GetRemoteADDR() {
        return self::GetSERVER('REMOTE_ADDR');
    }

    // すべてのGet値を取得
    static public function GetRequest() {
        return Sanitize($_GET);
    }

    // 指定した要素のGet値を取得
    static public function GetQuery($elm = '') {
        $_get = Sanitize($_GET);
        if (key_exists($elm, $_get)) {
            return $_get[$elm];
        } else {
            return null;
        }
    }

    static public function GetFiles() {
        return $_FILES;
    }

    // 公開パスなどのURLを取得
    public function GetUrl($query='', $type = 'url') {
        switch ($type) {
            case 'client':
                $url = $this->client;
                break;
            case 'css':
                $url = $this->css;
                break;
            case 'js':
                $url = $this->js;
                break;
            case 'image':
                $url = $this->image;
                break;
            case 'csv':
                $url = $this->csv;
                break;
            default:
                $url = $this->url;
                break;
        }
        return $url . '/' . $query;
    }

}

// セッションクラス
class Session
{

    private $init;
    private $session;

    function __construct()
    {
        $this->Read();
        $this->init = $this->session;
    }

    private function SessionStart()
    {
        if (!isset($_SESSION) || session_status() === PHP_SESSION_DISABLED) {
            session_start();
        } else {
            // セッションが定義されている場合は更新
            session_regenerate_id();
        }
    }

    // セッションの追加
    private function Add($sessionElm, $sessionVal)
    {
        $this->session[$sessionElm] = $sessionVal;
        $_SESSION = $this->session;
    }

    // セッションの書き込み
    public function Write($tag, $message, $handle = null)
    {
        if (!empty($handle)) {
            $this->$handle();
        }
        $this->Add($tag, $message);
    }

    public function WriteArray($parentId, $childId, $data)
    {
        if ($this->Read($parentId) != NULL) {
            $tmp = $this->Read($parentId);
        } else {
            $tmp = [];
        }

        $tmp[$childId] = $data;
        $this->Write($parentId, $tmp);
    }

    public function Read($sessionElm = null)
    {
        if (!isset($_SESSION)) {
            $this->SessionStart();
        }

        $this->session = $_SESSION;

        if (isset($sessionElm)) {
            if (!isset($this->session[$sessionElm])) {
                return null;
            }
            return $this->session[$sessionElm];
        } else {
            return $this->session;
        }
    }

    public function Delete($sessionElm = null)
    {
        if (!isset($_SESSION)) {
            trigger_error('Session is already deleted.', E_USER_ERROR);
            exit;
        }
        if (isset($sessionElm)) {
            unset($this->session[$sessionElm]);
            $_SESSION = $this->session;
        } else {
            unset($this->session);
            $this->session = $this->init;
        }
    }

    // セッション閲覧用
    public function View($id = null)
    {
        if (isset($id)) {
            if (isset($this->session[$id])) {
                echo $this->session[$id];
            } else {
                return false;
            }
        } else {
            var_dump($this->session);
        }
        return true;
    }

    // セッション判定用
    public function Judge($id = null)
    {
        if (!isset($id)) {
            return null;
        }

        if (!isset($this->session[$id])) {
            return false;
        }

        return true;
    }

    // セッション参照後、該当のセッションを削除する
    public function OnlyView($tag)
    {
        if ($this->Judge($tag) === true) {
            $this->View($tag);
            $this->Delete($tag);
        }
    }

    // セッションの完全な破棄
    public function FinaryDestroy()
    {
        session_unset();

        // セッションを切断するにはセッションクッキーも削除する。
        // Note: セッション情報だけでなくセッションを破壊する。
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }

        // 最終的に、セッションを破壊する
        session_destroy();
    }
}

// Cookieクラス
class Cookie
{
    private $cookie;
    function __construct()
    {
        $this->Init();
    }

    private function Init()
    {
        foreach ($_COOKIE as $_key => $_val) {
            setcookie($_key, "", time() - 100);
        }
        unset($_COOKIE);
        $this->cookie = null;
    }

    public function GetCookie()
    {
        $this->cookie = $_COOKIE;
    }

    public function SetCookie($name, $val = null)
    {
        setCookie($val, $name);
    }

    public function ViewCookie()
    {
        print_r($this->cookie);
    }
}
