<?php

namespace ExportHtmlAdmin\extract_scripts;
class extract_scripts
{

    private $export_Wp_Page_To_Static_Html_Admin;

    public function __construct($export_Wp_Page_To_Static_Html_Admin)
    {
        $this->export_Wp_Page_To_Static_Html_Admin = $export_Wp_Page_To_Static_Html_Admin;
    }


    /**
     * @since 2.0.0
     * @param string $url
     * @return array
     */
    public function get_scripts($url="")
    {
        $saveAllAssetsToSpecificDir = $this->export_Wp_Page_To_Static_Html_Admin->getSaveAllAssetsToSpecificDir();
        $src = $this->export_Wp_Page_To_Static_Html_Admin->site_data;
        $jsLinks = $src->find('script');
        $path_to_dot = $this->export_Wp_Page_To_Static_Html_Admin->rc_path_to_dot($url, true, true);

        if (!empty($jsLinks)) {
            foreach ($jsLinks as $key => $link) {
                if (isset($link->src) && !empty($link->src)) {
                    $src_link = $link->src;
                    $src_link = html_entity_decode($src_link, ENT_QUOTES);
                    $src_link = $this->export_Wp_Page_To_Static_Html_Admin->ltrim_and_rtrim($src_link);
                    $src_link = url_to_absolute($url, $src_link);
                    $host = $this->export_Wp_Page_To_Static_Html_Admin->get_host($src_link);
                    $exclude_url = apply_filters('wp_page_to_html_exclude_urls_settings_only', false, $src_link);

                    if (!empty($host) && strpos($src_link, '.js') !== false && strpos($url, $host) !== false && !$exclude_url) {
                        $newlyCreatedBasename = $this->save_scripts($src_link, $url);
                        if(!$saveAllAssetsToSpecificDir){
                            $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($src_link);
                            $link->src = $path_to_dot . $middle_p . $newlyCreatedBasename;
                        }
                        else {
                            $link->src = $path_to_dot .'js/'. $newlyCreatedBasename;
                        }
                    }
                }
            }

            $this->export_Wp_Page_To_Static_Html_Admin->site_data = $src;
        }

    }

    public function save_scripts($script_url_prev = "", $found_on = "")
    {
        $script_url = $script_url_prev;
        $pathname_js = $this->export_Wp_Page_To_Static_Html_Admin->getJsPath();
        $script_url = url_to_absolute($found_on, $script_url);
        $m_basename = $this->export_Wp_Page_To_Static_Html_Admin->middle_path_for_filename($script_url);
        $saveAllAssetsToSpecificDir = $this->export_Wp_Page_To_Static_Html_Admin->getSaveAllAssetsToSpecificDir();
        $exportTempDir = $this->export_Wp_Page_To_Static_Html_Admin->getExportTempDir();
        $host = $this->export_Wp_Page_To_Static_Html_Admin->get_host($script_url);
        $keepSameName = $this->export_Wp_Page_To_Static_Html_Admin->getKeepSameName();
        $basename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($script_url);

        if($saveAllAssetsToSpecificDir && $keepSameName && !empty($m_basename)){
            $m_basename = explode('-', $m_basename);
            $m_basename = implode('/', $m_basename);
        }

        if (!$this->export_Wp_Page_To_Static_Html_Admin->is_link_exists($script_url_prev)) {
            $this->export_Wp_Page_To_Static_Html_Admin->add_urls_log($script_url, $found_on, 'js');

            if (!(strpos($basename, ".") !== false)) {
                $basename = rand(5000, 9999) . ".js";
                $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($script_url_prev, $basename, 'new_file_name');
            }
            $basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($basename);

            $my_file = $pathname_js . $m_basename . $basename;

            if(!$saveAllAssetsToSpecificDir){
                $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($script_url);
                if(!file_exists($exportTempDir .'/'. $middle_p)){
                    @mkdir($exportTempDir .'/'. $middle_p, 0777, true);
                }
                $my_file = $exportTempDir .'/'. $middle_p .'/'. $basename;
            }
            else{
                if($saveAllAssetsToSpecificDir && $keepSameName && !empty($m_basename)){
                    if(!file_exists($pathname_js .'/'. $m_basename)){
                        @mkdir($pathname_js . $m_basename, 0777, true);
                    }

                    $my_file = $pathname_js . $m_basename . $basename;
                }
            }

            if (!file_exists($my_file)) {
                $abs_url_to_path = $this->export_Wp_Page_To_Static_Html_Admin->abs_url_to_path($script_url);
                if (strpos($script_url, $host) !== false && file_exists($abs_url_to_path)){
                    @copy($abs_url_to_path, $my_file);
                }
                else{
                    $handle = @fopen($my_file, 'w') or die('Cannot open file:  ' . $my_file);

                    $data = $this->export_Wp_Page_To_Static_Html_Admin->get_url_data($script_url);
                    $data .= "\n/*This file was exported by \"Export WP Page to Static HTML\" plugin which created by ReCorp (https://myrecorp.com) */";
                    @fwrite($handle, $data);
                    fclose($handle);
                }

                $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($script_url_prev, 1);

            }


            if ($saveAllAssetsToSpecificDir && !empty($m_basename)){
                return $m_basename . $basename;
            }
            return $basename;

        }
        else{

            if (!(strpos($basename, ".") !== false) && $this->export_Wp_Page_To_Static_Html_Admin->get_newly_created_basename_by_url($script_url) != false){
                return $m_basename . $this->export_Wp_Page_To_Static_Html_Admin->get_newly_created_basename_by_url($script_url);
            }

            if ($saveAllAssetsToSpecificDir && !empty($m_basename)){
                return $m_basename . $basename;
            }
            return $basename;
        }

        return false;
    }
}