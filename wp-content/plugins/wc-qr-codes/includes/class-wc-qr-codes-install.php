<?php

if (!defined('ABSPATH')) {
    exit;
}

class WCQRCodesInstall {

    public static function install() {
        self::create_files();
    }

    public static function create_files() {
        $upload_dir = wp_upload_dir();
        $files = array(
            array(
                'base' => $upload_dir['basedir'] . '/wcqrc-images',
                'file' => 'index.html',
                'content' => '',
            )
        );
        foreach ($files as $file) {
            if (wp_mkdir_p($file['base']) && !file_exists(trailingslashit($file['base']) . $file['file'])) {
                if ($file_handle = @fopen(trailingslashit($file['base']) . $file['file'], 'w')) {
                    fwrite($file_handle, $file['content']);
                    fclose($file_handle);
                }
            }
        }
    }

}
