<?php

class ModulesOldVersion
{

    private $id_module;
    private $id_category;
    private $module_name;
    private $module_token;
    private $module_content;
    private $module_heading;
    private $module_script;
    private $module_url;


    private $modules_path = DIRNAME . "../modules/content/";
    private $modules_head_path = DIRNAME . "../modules/content-head/";
    private $scripts_path = DIRNAME . "../modules/scripts/";


    public function __construct()
    {
        global $database;
        global $text;
        $id_current_module = $this->getRequestModule();
        $id_current_category = $this->getRequestCategory();

        if ($id_current_module > 0) {
            if ($id_current_category > 0) {
                try {
                    $database->query("SELECT id_module, id_category, module_name, module_token, module_content, module_heading, module_script, module_url FROM modules WHERE id_category = ? AND id_module = ?");
                    $database->bind(1, $id_current_category);
                    $database->bind(2, $id_current_module);
                    $result = $database->resultsetObject();
                    foreach ($result as $key => $value) {
                        $this->$key = $text->utf8($value);
                    }
                } catch (Exception $exception) {
                    echo($exception);
                }
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
    public function getIdCategory()
    {
        return $this->id_category;
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
    public function getModuleToken()
    {
        return $this->module_token;
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

    /**
     * @return mixed
     */
    public function getModuleHeading()
    {
        return $this->module_heading;
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

    public function getRequestCategory()
    {
        global $database;
        $category_name = $this->getRequest("mdcat");
        try {
            if ($category_name) {
                $database->query("SELECT id_category FROM modules_category WHERE category_url = :categoryname");
                $database->bind(":categoryname", $category_name);
                $result = $database->resultset();
                if (count($result) > 0) return $result[0]['id_category'];
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
        return 0;
    }

    public function getRequestModule()
    {
        global $database;
        $module_url = $this->getRequest("mdurl");
        $id_category = $this->getRequestCategory();

        try {
            if ($module_url) {
                if ($id_category > 0) {
                    $database->query("SELECT id_module FROM modules WHERE (module_url = :module_url OR MD5(module_url) = :module_url) AND id_category = :id_category");
                    $database->bind(":module_url", $module_url);
                    $database->bind(":id_category", $id_category);
                } else {
                    $database->query("SELECT id_module FROM modules WHERE module_url = :module_url OR MD5(module_url) = :module_url");
                    $database->bind(":module_url", $module_url);
                }
                $result = $database->resultset();
                if (count($result) > 0) return $result[0]['id_module'];
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
        return null;
    }

    public function getModuleUrlByKey($module_token)
    {
        global $database;
        $final_url = SERVER_ADDRESS;
        try {
            if ($module_token !== "") {

                $database->query("SELECT category_url, module_url FROM modules m LEFT JOIN modules_category mc ON mc.id_category = m.id_category WHERE module_token = ?");
                $database->bind(1, $module_token);

                $result = $database->resultset();
                if (count($result) > 0) {
                    $category_url = $result[0]['category_url'];
                    $module_url = $result[0]['module_url'];
                    $final_url .= $category_url . "/" . $module_url;
                }
            }
        } catch (Exception $exception) {
            error_log($exception);
        }
        return $final_url;
    }


    public function load()
    {
        $module = $this->getModuleContent();
        if ($module !== null && $module !== "") {
            $module_path = $this->modules_path . $module;
            if (file_exists($module_path)) {
                return $module_path;
            }
        }
        return false;
    }

    public function loadHeading()
    {
        $module = $this->getModuleHeading();
        if ($module !== null && $module !== "") {
            $module_path = $this->modules_head_path . $module;
            if (file_exists($module_path)) {
                return $module_path;
            }
        }
        return false;
    }

    public function scripts()
    {
        $scripts = $this->getModuleScript();
        if ($scripts !== null && $scripts !== "") {
            $scripts_path = $this->scripts_path . $scripts;
            if (file_exists($scripts_path)) {
                $url = SERVER_ADDRESS . "edge/scripts/" . $scripts . "?v=1";
                //return $url;
                return "<script async  type=\"text/javascript\" src=\"" . $url . "\"></script>";
                //return file_get_contents($scripts_path);
            }
        }
        return false;
    }


    public function getMenuStructure()
    {
        global $database;
        global $text;
        try {

            $final_menu = "<ul class=\"kt-menu__nav \">";
            $database->query("SELECT CONCAT(mc.category_name, ',', mc.category_url) AS category, GROUP_CONCAT(m.module_name, ',', m.module_url, ',', m.module_icon ORDER BY m.module_name ASC SEPARATOR ';') AS modules FROM modules m LEFT JOIN modules_category mc ON mc.id_category = m.id_category GROUP BY mc.id_category ORDER BY m.id_category ASC");
            $result = $database->resultset();
            for ($i = 0; $i < count($result); $i++) {

                $category = $result[$i]['category'];
                $modules = $result[$i]['modules'];

                $modules_split = explode(";", $modules);
                if (count($modules_split) > 0) {

                    $category_split = explode(",", $category);

                    if (count($category_split) > 0) {


                        $category_name = $category_split[0];
                        $category_url = $category_split[1];

                        // active item class :  kt-menu__item--here kt-menu__item--here

                        $final_menu .= "<li class=\"kt-menu__item  kt-menu__item--open kt-menu__item--submenu kt-menu__item--rel kt-menu__item--open \" data-ktmenu-submenu-toggle=\"click\" aria-haspopup=\"true\">";
                        $final_menu .= "    <a href=\"javascript:;\" class=\"kt-menu__link kt-menu__toggle\">";
                        $final_menu .= "        <span class=\"kt-menu__link-text\">{{category_name}}</span>";
                        $final_menu .= "            <i class=\"kt-menu__ver-arrow la la-angle-right\"></i>";
                        $final_menu .= "    </a>";
                        $final_menu .= "<div class=\"kt-menu__submenu kt-menu__submenu--classic kt-menu__submenu--left\">";
                        $final_menu .= "    <ul class=\"kt-menu__subnav\">";


                        $final_menu = str_replace("{{category_name}}", $text->utf8($category_name), $final_menu);

                        for ($x = 0; $x < count($modules_split); $x++) {

                            $module_item = $modules_split[$x];
                            $module_item_unique = explode(",", $module_item);

                            $module_title = $module_item_unique[0];
                            $module_url = $this->createRoute($category_url . "/" . $module_item_unique[1]);
                            $module_icon = $module_item_unique[2];
                            $final_menu .= $this->createUniqueSidebarItem($module_url, $module_title, $module_icon);

                        }

                        $final_menu .= "    </ul>";
                        $final_menu .= "</div>";
                        $final_menu .= "</li>";


                    } else {

                        $module_item = $modules_split[0];
                        $module_item_unique = explode(",", $module_item);

                        $module_title = $module_item_unique[0];

                        $final_menu = str_replace("{{category_name}}", $text->utf8($module_title), $final_menu);


                    }

                }


            }
            $final_menu .= "</ul>";
            return $final_menu;
        } catch (Exception $exception) {
            error_log($exception);
        }
    }

    private function createUniqueSidebarItem($module_url, $module_title, $module_icon)
    {
        global $text;
        //   kt-menu__item--active
        //$module_url = substr(SERVER_ADDRESS, 0,-1) . $module_url;
        $module_url = $module_url;
        $final_menu = "        <li class=\"kt-menu__item \" aria-haspopup=\"true\">";
        $final_menu .= "            <a href=\"" . $module_url . "\" class=\"kt-menu__link \">";
        $final_menu .= "                <i class=\"kt-menu__link-bullet kt-menu__link-bullet--dot\"><span></span></i>";
        $final_menu .= "                    <span class=\"kt-menu__link-text\">" . $text->utf8($module_title) . "</span>";
        $final_menu .= "            </a>";
        $final_menu .= "        </li>";
        return $final_menu;
    }

    private function createRoute($route)
    {
        return $route;
    }

    public function getHome()
    {
        global $properties;
        return $properties->getDashboardURL() . "d/account/profile";
    }

}