<?php

class Modules
{

    private $id_module;
    private $module_name;
    private $module_content;
    private $module_script;
    private $module_url;


    private $modules_path = DIRNAME . "../modules/content/";
    private $scripts_path = DIRNAME . "../modules/scripts/";


    public function __construct()
    {
        global $database;
        global $text;
        $id_current_module = $this->getRequestModule();
        if ($id_current_module > 0) {
            try {
                $database->query("SELECT id_module,  module_name,  module_content,  module_script, module_url FROM modules WHERE id_module = ?");
                $database->bind(1, $id_current_module);
                $result = $database->resultsetObject();
                foreach ($result as $key => $value) {
                    $this->$key = $text->utf8($value);
                }
            } catch (Exception $exception) {
                echo($exception);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getIdModule()
    {
        return $this->id_module;
    }


    /**
     * @return mixed
     */
    public function getModuleName()
    {
        return $this->module_name;
    }

    /**
     * @return mixed
     */
    public function getModuleContent()
    {
        return $this->module_content;
    }

    /**
     * @return mixed
     */
    public function getModuleScript()
    {
        return $this->module_script;
    }

    /**
     * @return mixed
     */
    public function getModuleUrl()
    {
        return $this->module_url;
    }

    private function getRequest($index)
    {
        if (isset($_REQUEST[$index])) {
            $request = $_REQUEST[$index];
            if ($request !== null && $request !== "" && strlen($request) > 0) {
                return $request;
            }
        }
        return false;
    }

    private function getContentPath()
    {
        global $account;
        if ($account->isCustomer()) return DIRNAME . "../modules/customer/";
        return DIRNAME . "../modules/admin/";
    }


    public function getRequestModule()
    {
        global $database;
        $module_url = $this->getRequest("mdurl");
        try {
            if ($module_url) {
                $database->query("SELECT id_module FROM modules WHERE module_url = :module_url OR MD5(module_url) = :module_url");
                $database->bind(":module_url", $module_url);
                $result = $database->resultset();
                if (count($result) > 0) return $result[0]['id_module'];
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
        return null;
    }

    public function getModuleUrlById($id_module)
    {
        global $database;
        global $properties;
        $final_url = "";
        try {
            if ($id_module !== "") {
                $database->query("SELECT module_url FROM modules WHERE id_module = ?");
                $database->bind(1, $id_module);
                $result = $database->resultset();
                if (count($result) > 0) {
                    $module_url = $result[0]['module_url'];
                    $final_url = $properties->getDashboardURL() . $module_url;
                }
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
        return $final_url;
    }

    public function getEncodedModuleUrlById($id_module)
    {
        global $text;
        return $text->base64_encode($this->getModuleUrlById($id_module));
    }


    public function load()
    {
        $module = $this->getContentPath() . $this->getModuleContent();
        if ($module !== null && $module !== "") {
            if (file_exists($module)) {
                return $module;
            }
        }
        return false;
    }

    public function scripts()
    {
        global $properties;
        $scripts = $this->getModuleScript();
        if ($scripts !== null && $scripts !== "") {
            $scripts_path = $this->scripts_path . $scripts;
            if (file_exists($scripts_path)) {
                $url = $properties->getSiteURL() . "public/javascript/" . $scripts . "?v=1";
                //return $url;
                return "<script async  type=\"text/javascript\" src=\"" . $url . "\"></script>";
                //return file_get_contents($scripts_path);
            }
        }
        return false;
    }

    public function getHome()
    {
        global $properties;
        return $properties->getDashboardURL() . "resume";
    }

}