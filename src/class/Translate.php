<?php

class Translate
{

    private $language = 'pt-br';
    private $lang = array();

    /**
     * @return array
     */
    public function getLanguage()
    {
        return $this->lang;
    }


    public function __construct()
    {
        $language = "en";
        try {
            $language = $this->getLanguage();
        } catch (Exception $exception) {

        } finally {
            $this->language = $language;
        }
    }

    private function search($str, $replaces = array())
    {
        $var_pattern = "%s";
        $translated = $str;
        if (array_key_exists($str, $this->lang[$this->language])) {
            $translated = $this->lang[$this->language][$str];

            if (count($replaces) > 0) {
                $count_patterns = substr_count($translated, $var_pattern);
                if ($count_patterns == count($replaces)) {
                    return vsprintf($translated, $replaces);
                } else {
                    if ($count_patterns > count($replaces)) {
                        return "Too much arguments";
                    } else {
                        return "Too low arguments";
                    }
                }

            }
        }
        return $translated;
    }


    private function replaceOccurence($search, $replace, $subject, $occurrence)
    {
        return substr_replace($search, $replace, $occurrence);
    }

    private function getLanguageDocument()
    {
        $default_path = DIRNAME;
        $default_path .= "../language/" . $this->getLanguageLang() . ".lang";
        if (!file_exists($default_path)) {
            $default_path = DIRNAME . "../language/en.lang";
        }
        return $default_path;
    }


    private function splitStrings($str)
    {
        return explode('=', trim($str));
    }

    public function __($str, $replaces = array())
    {
        if (!array_key_exists($this->language, $this->lang)) {
            if (file_exists($this->getLanguageDocument())) {
                $strings = array_map(array($this, 'splitStrings'), file($this->getLanguageDocument()));
                foreach ($strings as $k => $v) {
                    if (substr($v[0], 0, 1) !== "#" && substr($v[0], 0, 1) !== "") {


                        $this->lang[$this->language][$v[0]] = $v[1];


                    }
                }
                return $this->search($str, $replaces);
            } else {
                return $str;
            }
        } else {
            return $this->search($str, $replaces);
        }
    }

    /*
        public function getLanguage()
        {
            $language = "d41d8cd98f00b204e9800998ecf8427e";
            if (isset($_COOKIE['language']) && $_COOKIE['language'] !== "") {
                $language = $_COOKIE['language'];
            }
            return $language;
        }
    */

    private function getLanguageData($column)
    {
        global $database;
        try {
            $database->query("SELECT * FROM languages WHERE language_token = ?");
            $database->bind(1, $this->getLanguage());
            $resultset = $database->resultset();
            if (count($resultset) > 0) {
                return $resultset[0][$column];
            }
        } catch (Exception $exception) {

        }
    }

    public function getLanguageFlag()
    {
        return $this->getLanguageData("language_flag");
    }

    private function getLanguageLang()
    {
        return $this->getLanguageData("language_data");
    }

    public function getLanguageName()
    {
        global $charset;
        $charset->setString($this->getLanguageData("language_name"));
        $language = $charset->utf8();
        return $language;
    }

    public function getAllLanguageByContinent($id_continent)
    {
        global $database;
        try {
            $database->query("SELECT * FROM languages WHERE id_continent = ?");
            $database->bind(1, $id_continent);
            $resultset = $database->resultset();
            if (count($resultset) > 0) {
                return $resultset;
            }
        } catch (Exception $exception) {

        }
    }

}
