<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Apps
{
    var $name = "198703012020121004";
    var $title = "<b>198703012020121004</b> ";
    var $release = "Version 1.0.0";
    var $ver = "Version 1.0.0";
    var $modname = "";
    var $moddesc = "";
    var $copyright = " &copy; 2023";
    var $statnav = "active";

    public function __construct()
    {
    }

    public function modulname()
    {
        return $this->modname;
    }

    public function moduldesc()
    {
        return $this->moddesc;
    }

    public function titlepage($param)
    {
        return $param;
    }

    public function modulsource($param)
    {
        return $param;
    }

    public function idmenu($param)
    {
        return $param;
    }

    public function namamenu($param)
    {
        return $param;
    }
}
