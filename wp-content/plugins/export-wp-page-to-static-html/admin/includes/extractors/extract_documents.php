<?php

namespace ExportHtmlAdmin\extract_documents;

class extract_documents
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
    public function get_documents($url="")
    {
        $src = $this->export_Wp_Page_To_Static_Html_Admin->site_data;
        $documentHrefLinks = $src->find('a');
        $path_to_dot = $this->export_Wp_Page_To_Static_Html_Admin->rc_path_to_dot($url, true, true);

        $saveAllAssetsToSpecificDir = $this->export_Wp_Page_To_Static_Html_Admin->getSaveAllAssetsToSpecificDir();

        if (!empty($documentHrefLinks)){
            foreach ($documentHrefLinks as $link) {
                if (isset($link->href) && !empty($link->href)) {
                    $src_link = $link->href;
                    $src_link = html_entity_decode($src_link, ENT_QUOTES);

                    $src_link = $this->export_Wp_Page_To_Static_Html_Admin->ltrim_and_rtrim($src_link);

                    $src_link = url_to_absolute($url, $src_link);
                    $host = $this->export_Wp_Page_To_Static_Html_Admin->get_host($src_link);

                    $docsExts = $this->export_Wp_Page_To_Static_Html_Admin->getDocsExtensions();
                    $documentBasename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($src_link);
                    $documentBasename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($documentBasename);

                    $urlExt = pathinfo($documentBasename, PATHINFO_EXTENSION);


                    $exclude_url = apply_filters('wp_page_to_html_exclude_urls_settings_only', false, $src_link);

                    if ( in_array($urlExt, $docsExts) && strpos($url, $host) !== false && !$exclude_url) {

                        $newlyCreatedBasename = $this->save_document($src_link, $url);
                        if(!$saveAllAssetsToSpecificDir){
                            $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($src_link);
                            $link->href = $path_to_dot . $middle_p . $newlyCreatedBasename;
                            $link->src = $path_to_dot . $middle_p . $newlyCreatedBasename;
                        }
                        else {

                            $link->href = $path_to_dot .'documents/' . $newlyCreatedBasename;
                            $link->src = $path_to_dot .'documents/' . $newlyCreatedBasename;
                        }

                    }
                }
            }
        }
        $this->export_Wp_Page_To_Static_Html_Admin->site_data = $src;


    }

    public function save_document($document_url_prev = "", $found_on = "")
    {
        $document_url = $document_url_prev;
        $documents_path = $this->export_Wp_Page_To_Static_Html_Admin->getDocsPath();
        $document_url = url_to_absolute($found_on, $document_url);
        $m_basename = $this->export_Wp_Page_To_Static_Html_Admin->middle_path_for_filename($document_url);
        $saveAllAssetsToSpecificDir = $this->export_Wp_Page_To_Static_Html_Admin->getSaveAllAssetsToSpecificDir();
        $exportTempDir = $this->export_Wp_Page_To_Static_Html_Admin->getExportTempDir();
        $keepSameName = $this->export_Wp_Page_To_Static_Html_Admin->getKeepSameName();
        $host = $this->export_Wp_Page_To_Static_Html_Admin->get_host($document_url);
        $basename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($document_url);

        if($saveAllAssetsToSpecificDir && $keepSameName && !empty($m_basename)){
            $m_basename = explode('-', $m_basename);
            $m_basename = implode('/', $m_basename);
        }

        if (
            !$this->export_Wp_Page_To_Static_Html_Admin->is_link_exists($document_url)
            && $this->export_Wp_Page_To_Static_Html_Admin->update_export_log($document_url)
        ) {
            $this->export_Wp_Page_To_Static_Html_Admin->add_urls_log($document_url, $found_on, 'document');

            $basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($basename);

            $my_file = $documents_path . $m_basename . $basename;

            $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($document_url);
            if(!$saveAllAssetsToSpecificDir){

                if(!file_exists($exportTempDir .'/'. $middle_p)){
                    @mkdir($exportTempDir .'/'. $middle_p, 0777, true);
                }
                $my_file = $exportTempDir .'/'. $middle_p .'/'. $basename;
            }
            else{
                if($saveAllAssetsToSpecificDir && $keepSameName && !empty($m_basename)){
                    if(!file_exists($documents_path .'/'. $m_basename)){
                        @mkdir($documents_path . $m_basename, 0777, true);
                    }

                    $my_file = $documents_path . $m_basename . $basename;
                }
            }

            if (!file_exists($my_file)) {
                $abs_url_to_path = $this->export_Wp_Page_To_Static_Html_Admin->abs_url_to_path($document_url);
                if (strpos($document_url, $host) !== false && file_exists($abs_url_to_path)){
                    @copy($abs_url_to_path, $my_file);
                }
                else{
                    $data = $this->export_Wp_Page_To_Static_Html_Admin->get_url_data($document_url);
                    $handle = @fopen($my_file, 'w') or die('Cannot open file:  ' . $my_file);

                    $data .= "\n/*This file was exported by \"Export WP Page to Static HTML\" plugin which created by ReCorp (https://myrecorp.com) */";
                    @fwrite($handle, $data);
                    fclose($handle);
                }



                $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($document_url_prev, 1);

            }

            if ($saveAllAssetsToSpecificDir && !empty($m_basename)){
                return $m_basename . $basename;
            }
            return $basename;
        }
        else{

            if (!(strpos($basename, ".") !== false) && $this->export_Wp_Page_To_Static_Html_Admin->get_newly_created_basename_by_url($document_url) != false){
                return $m_basename . $this->export_Wp_Page_To_Static_Html_Admin->get_newly_created_basename_by_url($document_url);
            }

            if ($saveAllAssetsToSpecificDir && !empty($m_basename)){
                return $m_basename . $basename;
            }
            return $basename;
        }


        return false;
    }
}
