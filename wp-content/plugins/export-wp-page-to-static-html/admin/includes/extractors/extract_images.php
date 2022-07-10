<?php

namespace ExportHtmlAdmin\extract_images;
class extract_images
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
    public function get_images($url="")
    {
        $src = $this->export_Wp_Page_To_Static_Html_Admin->site_data;
        $path_to_dot = $this->export_Wp_Page_To_Static_Html_Admin->rc_path_to_dot($url, true, true);
        $saveAllAssetsToSpecificDir = $this->export_Wp_Page_To_Static_Html_Admin->getSaveAllAssetsToSpecificDir();
        $imgExts = $this->export_Wp_Page_To_Static_Html_Admin->getImageExtensions();

        $images = $src->find('img');
        $image_links = $src->find('a');

        if (!empty($images)) {
            foreach ($images as $img) {
                if (strpos($img->src, 'data:') == false && strpos($img->src, 'svg+xml') == false && strpos($img->src, 'base64') == false) {
                    $img_src = html_entity_decode($img->src, ENT_QUOTES);
                    $img_src = $this->export_Wp_Page_To_Static_Html_Admin->ltrim_and_rtrim($img_src);
                    $img_src = url_to_absolute($url, $img_src);

                    $urlExt  = pathinfo($img_src, PATHINFO_EXTENSION);

                    $exclude_url = apply_filters('wp_page_to_html_exclude_urls_settings_only', false, $img_src, true);

                    if (in_array($urlExt, $imgExts) && !$exclude_url) {
                        $basename = $this->save_image($img_src, $url);
//                        $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($img_src);
//                        $basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($basename);

                        if (!$saveAllAssetsToSpecificDir) {
                            $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($img_src);
                            $img->setAttribute('src', $path_to_dot . $middle_p . $basename);
                        } else {
                            $img->setAttribute('src', $path_to_dot . 'images/' . $basename);
                        }

                    }
                }

                if (isset($img->attr['data-lazyload']) && strpos($img->attr['data-lazyload'], 'data:') == false && strpos($img->attr['data-lazyload'], 'svg+xml') == false && strpos($img->attr['data-lazyload'], 'base64') == false) {
                    $imgSrc = $img->attr['data-lazyload'];

                    $img_src = html_entity_decode($imgSrc, ENT_QUOTES);
                    $img_src = $this->export_Wp_Page_To_Static_Html_Admin->ltrim_and_rtrim($img_src);
                    $imgSrc  = url_to_absolute($url, $img_src);

                    $urlExt  = pathinfo($imgSrc, PATHINFO_EXTENSION);

                    $exclude_url = apply_filters('wp_page_to_html_exclude_urls_settings_only', false, $imgSrc);

                    if (in_array($urlExt, $imgExts) && !$exclude_url) {
                        $basename = $this->save_image($imgSrc, $url);
//                        $basename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($imgSrc);
//                        $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($basename);

                        if (!$saveAllAssetsToSpecificDir) {
                            $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($img->src);
                            $img->setAttribute('data-lazyload', $path_to_dot . $middle_p . $basename);
                        } else {
                            $img->setAttribute('data-lazyload', $path_to_dot . 'images/' . $basename);
                        }
                    }
                }

                if (isset($img->srcset)) {
                    $srcset = $img->srcset;
                    $srcset = explode(' ', $srcset);

                    $imgFind    = array();
                    $imgReplace = array();
                    foreach ($srcset as $key => $item) {
                        $img_src = html_entity_decode($item, ENT_QUOTES);
                        $img_src = $this->export_Wp_Page_To_Static_Html_Admin->ltrim_and_rtrim($img_src);
                        $item_url = url_to_absolute($url, $img_src);

                        $urlExt  = pathinfo($item_url, PATHINFO_EXTENSION);
                        //echo $urlExt;
                        if (in_array($urlExt, $imgExts)) {
//                            $basename  = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($item);
//                            $basename  = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($basename);
                            $basename = $this->save_image($item_url, $url);
                            $imgFind[] = $item;

                            if (!$saveAllAssetsToSpecificDir) {
                                $middle_p     = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($item_url);
                                $imgReplace[] = $path_to_dot . $middle_p . $basename;
                            } else {
                                $imgReplace[] = $path_to_dot . 'images/' . $basename;
                            }

                        }
                    }

                    $img->setAttribute('srcset', str_replace($imgFind, $imgReplace, $img->srcset));
                }

            }
        }

        if (!empty($image_links)){
            foreach ($image_links as $img) {
                if (isset($img->href) && !empty($img->href)) {
                    $src_link = $img->href;
                    $src_link = html_entity_decode($src_link, ENT_QUOTES);

                    $src_link = $this->export_Wp_Page_To_Static_Html_Admin->ltrim_and_rtrim($src_link);

                    $src_link = url_to_absolute($url, $src_link);
                    $host = $this->export_Wp_Page_To_Static_Html_Admin->get_host($src_link);

                    $imageBasename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($src_link);
                    $imageBasename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($imageBasename);

                    $urlExt = pathinfo($imageBasename, PATHINFO_EXTENSION);


                    $exclude_url = apply_filters('wp_page_to_html_exclude_urls_settings_only', false, $src_link);

                    if ( in_array($urlExt, $imgExts) && strpos($url, $host) !== false && !$exclude_url) {

                        $newlyCreatedBasename = $this->save_image($src_link, $url);
                        if(!$saveAllAssetsToSpecificDir){
                            $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($src_link);
                            $img->href = $path_to_dot . $middle_p . $newlyCreatedBasename;
                            $img->src = $path_to_dot . $middle_p . $newlyCreatedBasename;
                        }
                        else {
                            $img->href = $path_to_dot .'images/' . $newlyCreatedBasename;
                        }

                    }
                }
            }
        }


        $this->export_Wp_Page_To_Static_Html_Admin->site_data = $src;
    }

    public function save_image($img_src = "", $found_on = "")
    {
        $pathname_images = $this->export_Wp_Page_To_Static_Html_Admin->getImgPath();
        $saveAllAssetsToSpecificDir = $this->export_Wp_Page_To_Static_Html_Admin->getSaveAllAssetsToSpecificDir();
        $exportTempDir = $this->export_Wp_Page_To_Static_Html_Admin->getExportTempDir();
        $keepSameName = $this->export_Wp_Page_To_Static_Html_Admin->getKeepSameName();




        if (strpos($img_src, 'data:') == false) {
            $img_src = html_entity_decode($img_src, ENT_QUOTES);
            $basename = $this->export_Wp_Page_To_Static_Html_Admin->url_to_basename($img_src);

            $img_src = url_to_absolute($found_on, $img_src);

            $m_basename = $this->export_Wp_Page_To_Static_Html_Admin->middle_path_for_filename($img_src);
            if($saveAllAssetsToSpecificDir && $keepSameName && !empty($m_basename)){
                $m_basename = explode('-', $m_basename);
                $m_basename = implode('/', $m_basename);
            }
            $host = $this->export_Wp_Page_To_Static_Html_Admin->get_host($img_src);

            if (!$this->export_Wp_Page_To_Static_Html_Admin->is_link_exists($img_src)) {
                $this->export_Wp_Page_To_Static_Html_Admin->update_export_log($img_src);
                $this->export_Wp_Page_To_Static_Html_Admin->add_urls_log($img_src, $found_on, 'image');

                if (strpos($basename, '.') == false) {
                    $basename = rand(5000, 9999) . ".jpg";
                    $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($img_src, $basename, 'new_file_name');
                }
                $basename = $this->export_Wp_Page_To_Static_Html_Admin->filter_filename($basename);


                $middle_p = $this->export_Wp_Page_To_Static_Html_Admin->rc_get_url_middle_path_for_assets($img_src);
                if(!$saveAllAssetsToSpecificDir){
                    if(!file_exists($exportTempDir .'/'. $middle_p)){
                        @mkdir($exportTempDir .'/'. $middle_p, 0777, true);
                    }
                    $img_path_src = $exportTempDir .'/'. $middle_p .'/'. $basename;
                }
                else{
                    if($saveAllAssetsToSpecificDir && $keepSameName && !empty($m_basename)){
                        if(!file_exists($pathname_images . $m_basename)){
                            @mkdir($pathname_images . $m_basename, 0777, true);
                        }

                        $img_path_src = $pathname_images . $m_basename . $basename;
                    }else{
                        $img_path_src = $pathname_images . $m_basename . $basename;
                    }
                }



                if (!file_exists($img_path_src)) {
                    $abs_url_to_path = $this->export_Wp_Page_To_Static_Html_Admin->abs_url_to_path($img_src);

                    if (strpos($img_src, $host) !== false && file_exists($abs_url_to_path)){
                        @copy($abs_url_to_path, $img_path_src);
                    }
                    else{
                        $data = $this->export_Wp_Page_To_Static_Html_Admin->get_url_data($img_src);
                        $handle = @fopen($img_path_src, 'w') or die('Cannot open file:  ' . $img_path_src);
                        @fwrite($handle, $data);
                        fclose($handle);
                    }

                    $this->export_Wp_Page_To_Static_Html_Admin->update_urls_log($img_src, 1);
                }

                if ($saveAllAssetsToSpecificDir && !empty($m_basename)){
                    return $m_basename . $basename;
                }
                return $basename;


            }
            else{

                if (!(strpos($basename, ".") !== false) && $this->export_Wp_Page_To_Static_Html_Admin->get_newly_created_basename_by_url($img_src) != false){
                    return $m_basename . $this->export_Wp_Page_To_Static_Html_Admin->get_newly_created_basename_by_url($img_src);
                }

                if ($saveAllAssetsToSpecificDir && !empty($m_basename)){
                    return $m_basename . $basename;
                }
                return $basename;
            }




        }
        return false;

    }
}