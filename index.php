<?php
// use database\DataBase;


require_once('database/DataBase.php');


session_start();
define('BASE_PATH', __DIR__);
define('CURRENT_DOMAIN', currentDomain() . '/News');
define('DISPLAY_ERROR', true);
define('DB_HOST', 'localhost');
define('DB_NAME', 'project');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
$db = new \database\DataBase();
// Routing Function
function uri(string $reservedUrl, string $class, string $method, string $requestMethod = 'GET')
{
    $parameters = [];
    $currentUrl = explode('?', currentUrl())[0];
    $currentUrl = str_replace(CURRENT_DOMAIN, '', $currentUrl);
    $currentUrl = trim($currentUrl, '/');
    $currentUrlArray = explode('/', $currentUrl);
    // delete empty indexes of array
    $currentUrlArray = array_filter($currentUrlArray);
    //reserve URL

    $reservedUrl = trim($reservedUrl, '/');
    $reservedUrlArray = explode('/', $reservedUrl);
    $reservedUrlArray = array_filter($reservedUrlArray);
    if (sizeof($currentUrlArray) != sizeof($reservedUrlArray) || methodField() != $requestMethod) {
        return false;
    }
    for ($key = 0; $key < sizeof($currentUrlArray); $key++) {
        if ($reservedUrlArray[$key][0] == "{" && $reservedUrlArray[$key][strlen($reservedUrlArray[$key]) - 1] == "}") {
            array_push($parameters, $currentUrlArray[$key]);
        } elseif ($currentUrlArray[$key] !== $reservedUrlArray[$key]) {
            return false;
        }
    }
    if (methodField() == 'POST') {
        $request = isset($_FILES) ? array_merge($_POST, $_FILES) : $_POST;
        $parameters = array_merge([$request], $parameters);
    }
    $object = new $class;
    call_user_func_array(array($object, $method), $parameters);
    exit;
}
// helpers
function protocol()
{
    return stripos(
        $_SERVER['SERVER_PROTOCOL'],
        'https'
    ) === true ? 'https://' : 'http://';
}
exit;
function currentDomain()
{
    return protocol() . $_SERVER['HTTP_HOST'];
}
function asset($src)
{
    $domain = trim(CURRENT_DOMAIN, '/');
    $src = $domain . '/' . trim($src . '/');
    return $src;
}
function url($url)
{
    $domain = trim(CURRENT_DOMAIN, '/');
    $url = $domain . '/' . trim($url . '/');
    return $url;
}
function currentUrl()
{
    return currentDomain() . $_SERVER['REQUEST_URI'];
}
function methodField()
{
    return $_SERVER['REQUEST_METHOD'];
}
function displayError($displayError)
{
    if ($displayError) {
        ini_set('display_errors', 1);
        ini_set('display_startup_error', 1);
        error_reporting(E_ALL);
    } else {
        ini_set('display_errors', 0);
        ini_set('display_startup_error', 0);
        error_reporting(0);
    }
}
// show Messages
global $flashMessage;
if (isset($_SESSION['flash_message'])) {
    $flashMessage = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}
function flash($name, $value = null)
{
    if ($value === null) {
        global $flashMessage;
        $message = isset($flashMessage[$name]) ? $flashMessage[$name] : '';
        return $message;
    } else {
        $_SESSION['flash_message'][$name] = $value;
    }
}
function dd($var)
{
    echo '<pre/>';
    var_dump($var);
    exit;

}
?>