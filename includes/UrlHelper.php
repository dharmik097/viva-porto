<?php
class UrlHelper {
    private static $baseUrl = 'https://vivaporto.adelinomasioli.com';

    public static function getBaseUrl() {
        return self::$baseUrl;
    }

    public static function getUrl($path = '') {
        return self::$baseUrl . '/' . ltrim($path, '/');
    }

    public static function getDestinationUrl($id) {
        return self::getUrl("destination/{$id}");
    }

    public static function getCategoryUrl($category) {
        return self::getUrl("category/" . urlencode($category));
    }

    public static function getAdminUrl($path = '') {
        return self::getUrl("admin/" . ltrim($path, '/'));
    }

    public static function redirect($path) {
        header('Location: ' . self::getUrl($path));
        exit();
    }

    public static function getCurrentUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    public static function isCurrentUrl($path) {
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $checkPath = self::getUrl($path);
        return $currentPath === $checkPath;
    }
}
