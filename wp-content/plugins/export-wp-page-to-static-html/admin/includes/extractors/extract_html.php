<?php

namespace ExportHtmlAdmin\extract_html;

class extract_html
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
    public function get_HTMLs($url="")
    {
        $src = $this->export_Wp_Page_To_Static_Html_Admin->site_data;
        $htmlHrefLinks = $src->find('a');
        $path_to_dot = $this->export_Wp_Page_To_Static_Html_Admin->rc_path_to_dot($url, true, true);

        $saveAllAssetsToSpecificDir = $this->export_Wp_Page_To_Static_Html_Admin->getSaveAllAssetsToSpecificDir();
        
        if (!empty($htmlHrefLinks)){
            foreach ($htmlHrefLinks as $link) {
                if (isset($link->href) && !empty($link->href)) {
                    $src_link = $link->href;
                    $src_link = html_entity_decode($src_link, ENT_QUOTES);

                    $src_link = $this->export_Wp_Page_To_Static_Html_Admin->ltrim_and_rtrim($src_link);

                    $src_link = url_to_absolute($url, $src_link);
                    $host = $this->export_Wp_Page_To_Static_Html_Admin->get_host($src_link);

                    $htmlExts = $this->export_Wp_Page_To_Static_Html_Admin->getHtmlExtensions();
                    $htmlBasename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($src_link);
                    $htmlBasename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($htmlBasename);

                    $urlExt = pathinfo($htmlBasename, PATHINFO_EXTENSION);


                    $exclude_url = apply_filters('wp_page_to_html_exclude_urls_settings_only', false, $src_link);

                    if ( in_array($urlExt, $htmlExts) && strpos($url, $host) !== false && !$exclude_url) {

                        $this->save_html($src_link, $url);

                        $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($src_link);
                        $link->href = $path_to_dot . $middle_p . $htmlBasename;
                        $link->src = $path_to_dot . $middle_p . $htmlBasename;


                    }
                }
            }
        }
        $this->export_Wp_Page_To_Static_Html_Admin->site_data = $src;


    }

    public function save_html($html_url_prev = "", $found_on = "")
    {
        $html_url = $html_url_prev;
        $html_url = url_to_absolute($found_on, $html_url);
        $exportTempDir = $this->export_Wp_Page_To_Static_Html_Admin->getExportTempDir();
        $host = $this->export_Wp_Page_To_Static_Html_Admin->get_host($html_url);
        $basename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($html_url);

        if (
            !$this->export_Wp_Page_To_Static_Html_Admin->is_link_exists($html_url)
            && $this->export_Wp_Page_To_Static_Html_Admin->update_export_log($html_url)
        ) {
            $this->export_Wp_Page_To_Static_Html_Admin->add_urls_log($html_url, $found_on, 'html');


            if (!(strpos($basename, ".") !== false)) {
                $basename = rand(5000, 9999) . ".mp3";
                $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($html_url_prev, $basename, 'new_file_name');
            }
            $basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($basename);

            $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($html_url);

            if(!file_exists($exportTempDir .'/'. $middle_p)){
                @mkdir($exportTempDir .'/'. $middle_p, 0777, true);
            }
            $my_file = $exportTempDir .'/'. $middle_p .'/'. $basename;


            if (!file_exists($my_file)) {
                $abs_url_to_path = $this->export_Wp_Page_To_Static_Html_Admin->abs_url_to_path($html_url);
                if (strpos($html_url, $host) !== false && file_exists($abs_url_to_path)){
                    @copy($abs_url_to_path, $my_file);
                }
                else{
                    $data = $this->export_Wp_Page_To_Static_Html_Admin->get_url_data($html_url);
                    $handle = @fopen($my_file, 'w') or die('Cannot open file:  ' . $my_file);

                    $data .= "\n/*This file was exported by \"Export WP Page to Static HTML\" plugin which created by ReCorp (https://myrecorp.com) */";
                    @fwrite($handle, $data);
                    fclose($handle);
                }


                $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($html_url_prev, 1);

            }

        }

        return false;
    }
}
