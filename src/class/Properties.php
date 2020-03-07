<?php

class Properties
{

    private $site_name;
    private $site_url;
    private $development;

    public function __construct()
    {
        $json = file_get_contents(DIRNAME . "./../properties/settings.json");
        $data = json_decode($json, true);
        foreach ($data['website'] AS $key => $value) $this->{$key} = $value;
    }


    public function getSiteName()
    {
        return $this->site_name;
    }

    public function getDevelopment()
    {
        return $this->development;
    }

    public function getSiteURL()
    {
        return $this->site_url;
    }

    public function getDashboardURL()
    {
        return $this->getSiteURL() . "d/";
    }

    public function getLoginURL()
    {
        return $this->getSiteURL() . "login";
    }

}