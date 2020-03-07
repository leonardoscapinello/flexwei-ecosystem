<?php

class UsuariosSettings extends Usuarios
{

    private $id_account = 0;
    private $setting_key;
    private $setting_value;

    private function getSettingValue($setting_key)
    {

        global $database;
        global $charset;
        $setting_value = null;
        try {
            if ($this->getIdAccount() > 0) {
                $database->query("SELECT setting_value FROM accounts_settings WHERE id_account = ? AND setting_key = ?");
                $database->bind(1, $this->getIdAccount());
                $database->bind(2, $setting_key);
                $resultset = $database->resultset();
                if (count($resultset) > 0) {
                    $charset->setString($resultset[0]['setting_value']);
                    $setting_value = $charset->utf8();
                } else {

                    $database->query("SELECT setting_value FROM settings WHERE setting_key = ?");
                    $database->bind(1, $setting_key);
                    $resultset = $database->resultset();
                    if (count($resultset) > 0) {
                        $charset->setString($resultset[0]['setting_value']);
                        $setting_value = $charset->utf8();
                    }

                }
            }
        } catch (Exception $exception) {
            return $exception;
        } finally {
            return $setting_value;
        }
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->getSettingValue("LANGUAGE");
    }

    public function getDateFormat()
    {
        return $this->getSettingValue("DATE_FORMAT");
    }

    public function getDateTimeFormat()
    {
        return $this->getSettingValue("DATETIME_FORMAT");
    }

    public function getLastCompany()
    {
        return $this->getSettingValue("LAST_ACCESS_COMPANY");
    }

}
