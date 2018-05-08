<?php

class ModelPaymentMokaPayment extends Model {

    public function install() {
        $this->disableErrorSettings();
    }

    public function uninstall() {
        
    }

    public function logger($message) {
        $log = new Log('moka_payment.log');
        $log->write($message);
    }

    public function createOrderEntry($data) {
        
    }

    public function updateOrderEntry($data, $id) {
        
    }

    public function disableErrorSettings() {
        $store_id = (int) $this->config->get('config_store_id');
        $key = 'config_error_display';
        $group = 'config';
        $response = $this->db->query("SELECT `setting_id` FROM " . DB_PREFIX . "setting WHERE store_id = '{$store_id}' AND `group` = '{$group}' AND `key` = '{$key}'");
        if ($response->num_rows > 0) {
            $id = (int) $response->row['setting_id'];
            $this->db->query("UPDATE " . DB_PREFIX . "setting SET `value` = '0' WHERE `setting_id` = '{$id}' ");
        } else {
            $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '{$store_id}', `group` = '{$group}', `key` = '{$key}', `value` = '0'");
        }
    }

    
    
    public function disableErrorSettings() {
        $store_id = (int) $this->config->get('config_store_id');
        $key = 'config_error_display';
        $group = 'config';
        $response = $this->db->query("SELECT `setting_id` FROM " . DB_PREFIX . "setting WHERE store_id = '{$store_id}' AND `group` = '{$group}' AND `key` = '{$key}'");
        if ($response->num_rows > 0) {
            $id = (int) $response->row['setting_id'];
            $this->db->query("UPDATE " . DB_PREFIX . "setting SET `value` = '0' WHERE `setting_id` = '{$id}' ");
        } else {
            $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '{$store_id}', `group` = '{$group}', `key` = '{$key}', `value` = '0'");
        }
     }
    public function versionCheck($opencart, $moka) {
        $serverdomain = $_SERVER['HTTP_HOST'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://api.kahvedigital.com/version');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "opencart=$opencart&moka=$moka&type=opencart&domain=$serverdomain");
        $response = curl_exec($ch);
        $response = json_decode($response, true);
        return $response;
    }

    public function update($version_updatable) {

        function recurse_copy($src, $dst) {
            $dir = opendir($src);
            @mkdir($dst);
            while (false !== ( $file = readdir($dir))) {
                if (( $file != '.' ) && ( $file != '..' )) {
                    if (is_dir($src . '/' . $file)) {
                        recurse_copy($src . '/' . $file, $dst . '/' . $file);
                    } else {
                        copy($src . '/' . $file, $dst . '/' . $file);
                    }
                }
            }
            closedir($dir);
        }

        function rrmdir($dir) {
            if (is_dir($dir)) {
                $objects = scandir($dir);
                foreach ($objects as $object) {
                    if ($object != "." && $object != "..") {
                        if (filetype($dir . "/" . $object) == "dir")
                            rrmdir($dir . "/" . $object);
                        else
                            unlink($dir . "/" . $object);
                    }
                }
                reset($objects);
                rmdir($dir);
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://api.kahvedigital.com/update');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "new_version=$version_updatable");
        $response = curl_exec($ch);
        $response = json_decode($response, true);

        $serveryol = $_SERVER['DOCUMENT_ROOT'];
        $ch = curl_init();
        $source = $response['file_dest'];
        curl_setopt($ch, CURLOPT_URL, $source);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);

        $foldername = $response['version_name'];
        $fullfoldername = $serveryol . '/' . $foldername;
        if (!file_exists($fullfoldername)) {
            mkdir($fullfoldername);
        }
        if (file_exists($fullfoldername)) {
            $unzipfilename = 'mokaupdate.zip';
            $file = fopen($fullfoldername . '/' . $unzipfilename, "w+");
            fputs($file, $data);
            fclose($file);

            $path = pathinfo(realpath($fullfoldername . '/' . $unzipfilename), PATHINFO_DIRNAME);
            $zip = new ZipArchive;
            $res = $zip->open($fullfoldername . '/' . $unzipfilename);
            if ($res === TRUE) {
                $zip->extractTo($path);
                $zip->close();
                $zip_name_folder = $response['zip_name_folder'];

                recurse_copy($fullfoldername . '/' . $zip_name_folder . '/admin', DIR_APPLICATION);
                recurse_copy($fullfoldername . '/' . $zip_name_folder . '/catalog', DIR_CATALOG);
                recurse_copy($fullfoldername . '/' . $zip_name_folder . '/system', DIR_SYSTEM);

                rrmdir($fullfoldername);
            } else {
                return 0;
            }
        } else {
            return 0;
        }
        return 1;
    }

}
