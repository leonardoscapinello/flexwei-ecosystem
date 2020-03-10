<?php

class StyleSheetCompiler
{

    private $files = array();
    private $replaces = array();
    private $output_file;
    private $date_reason = "dmYHis";

    public function __construct($development = false)
    {
        global $properties;
        if ($development) {
            $this->add(DIRNAME . "../../public/stylesheet/fontawesome.all.min.css");
            $this->add(DIRNAME . "../../public/stylesheet/line-awesome.min.css");
            $this->add(DIRNAME . "../../public/stylesheet/reset.css");
            $this->add(DIRNAME . "../../public/stylesheet/container.css");
            $this->add(DIRNAME . "../../public/stylesheet/stylesheet.css");
            $this->add(DIRNAME . "../../public/stylesheet/controltabs.css");
            $this->add(DIRNAME . "../../public/stylesheet/tooltip.css");
            $this->setOutputFile(DIRNAME . "../../public/stylesheet/stylesheet");
            $this->addReplace("../images/", $properties->getSiteURL() . "public/images/");
            $this->addReplace("../fonts/",  $properties->getSiteURL() . "public/fonts/");
            $this->compileCSS();
        }
    }

    private function add($location)
    {
        if (file_exists($location)) {
            array_push($this->files, $location);
        }
    }

    private function addReplace($search, $replace)
    {
        array_push($this->replaces, array($search, $replace));
    }

    private function setOutputFile($filename)
    {
        $this->output_file = $filename;
    }

    private function compileCSS()
    {
        $end_file = $this->output_file . ".min.css";
        $end_file_last_update = "";
        if (file_exists($end_file)) {
            $end_file_last_update = date($this->date_reason, filemtime($end_file));
        }
        $compile = false;
        for ($i = 0; $i < count($this->files); $i++) {
            $current_file_last_update = date($this->date_reason, filemtime($this->files[$i]));
            if ($end_file_last_update !== $current_file_last_update) {
                $file2_content = file_get_contents($this->files[$i]);
                $file2 = fopen($this->files[$i], "w") or die("Unable to open file!");
                fwrite($file2, $file2_content);
                fclose($file2);
                $compile = true;
            }
        }
        $buffer = "";
        if ($compile) {
            for ($i = 0; $i < count($this->files); $i++) {
                $buffer .= file_get_contents($this->files[$i]);
            }
            $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
            $buffer = str_replace(': ', ':', $buffer);
            $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);

            if (count($this->replaces) > 0) {
                for ($x = 0; $x < count($this->replaces); $x++) {
                    $buffer = str_replace($this->replaces[$x][0], $this->replaces[$x][1], $buffer);
                }
            }

            $file = fopen($end_file, "w") or die("Unable to open file!");
            fwrite($file, $buffer);
            fclose($file);
        }

    }

    public function inlineCSS($file)
    {
        if (file_exists($file)) {
            return "<style type=\"text/css\">" . file_get_contents($file) . "</style>";
        }
    }

    public function minifyAndPrintInlint($file)
    {
        if (file_exists($file)) {
            $buffer = "";
            $buffer .= file_get_contents($file);
            $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
            $buffer = str_replace(': ', ':', $buffer);
            $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
            if (count($this->replaces) > 0) {
                for ($x = 0; $x < count($this->replaces); $x++) {
                    $buffer = str_replace($this->replaces[$x][0], $this->replaces[$x][1], $buffer);
                }
            }
            return "<style type=\"text/css\">" . $buffer . "</style>";
        }
    }

}