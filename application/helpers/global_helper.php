<?php
if(!defined('BASEPATH'))
    exit('No direct script access allowed');

// check admin user login return true or false
if(!function_exists('adminLoginCheck'))
{
    function adminLoginCheck()
    {
        // get the codeigniter instance
        $CI = &get_instance();
        // check if user data to the session
        if($CI->session->userdata('user_id'))
        {
            // load the specific model
            $CI->load->model('user_model');
            // get the specific user information
            $userInfo = $CI->user_model->getSingleUserInfo($CI->session->userdata('user_id'));
            // if found then set the user data to the session
            if(!empty($userInfo))
            {
                $user_data = array(
                    'user_id'    => $userInfo->user_id,
                    'user_name'  => $userInfo->user_name,
                    'first_name' => $userInfo->first_name,
                    'status'     => $userInfo->status,
                    'email'      => $userInfo->email,
                    'photo'      => $userInfo->photo
                );
                $CI->session->set_userdata($user_data);
                return TRUE;
            }
            else
                return FALSE;
        }
        else
            return FALSE;
    }
}

// get current user id
if(!function_exists('get_current_user_id'))
{
    function get_current_user_id()
    {
        $CI = &get_instance();

        if($CI->session->userdata('user_id'))
        {
            return $CI->session->userdata('user_id');
        }
    }
}

// generate the password and secret
if(!function_exists('geneSecurePass'))
{

    function geneSecurePass($password, $secret = FALSE)
    {

        if($secret)
        {
            // create the salt using secret
            list($salt1, $salt2) = str_split($secret, ceil(strlen($secret) / 2));
            $code = md5($salt1 . $password . $salt2);
        }
        else
        {
            // generate the randomcode
            $code['secret'] = $secret = rand(100000, 999999);
            // create the salt using secret
            list($salt1, $salt2) = str_split($secret, ceil(strlen($secret) / 2));
            // generate the password
            $code['password'] = md5($salt1 . $password . $salt2);
        }

        return $code;
    }
}

if(!function_exists('get_customer_info'))
{
    function get_customer_info($user_id)
    {
        $CI    = &get_instance();
        $query = $CI->db->select('*')
            ->from('rma_users')
            ->where('user_id', $user_id)
            ->get();

        if($query->num_rows() > 0)
        {
            return $query->row();
        }
        else
        {
            return FALSE;
        }
    }
}


// create paggination configuratin
if(!function_exists('createPagging'))
{

    function createPagging($page_url, $total_rows, $per_page, $num_links = 2)
    {
        $CI = &get_instance();
        //load the pagging library
        $CI->load->library('pagination');
        // set the configuration
        $config['base_url']   = site_url($page_url);
        $config['total_rows'] = $total_rows;
        $config['per_page']   = $per_page;
        $config['num_links']  = $num_links;

        $config['page_query_string']    = TRUE;
        $config['reuse_query_string']   = TRUE;
        $config['query_string_segment'] = 'page';
        $config['use_page_numbers']     = TRUE;

        // pagging design section
        //full tag
        $config['full_tag_open']  = '<ul class="pagination justify-content-center">';
        $config['full_tag_close'] = '</ul>';

        // first tag
        $config['first_link']      = '<i class="fa fa-angle-double-left" aria-hidden="true"></i>';
        $config['first_tag_open']  = '<li>';
        $config['first_tag_close'] = '</li>';

        //Last Link
        $config['last_link']      = '<i class="fa fa-angle-double-right" aria-hidden="true"></i>';
        $config['last_tag_open']  = '<li>';
        $config['last_tag_close'] = '</li>';

        //“Next” Link
        $config['next_link']      = '<i class="fa fa-angle-right" aria-hidden="true"></i>';
        $config['next_tag_open']  = '<li>';
        $config['next_tag_close'] = '</li>';

        //"privious" link
        $config['prev_link']      = '<i class="fa fa-angle-left" aria-hidden="true"></i>';
        $config['prev_tag_open']  = '<li>';
        $config['prev_tag_close'] = '</li>';


        //"Current Page" Link
        $config['cur_tag_open']  = '<li class = "active"><a href = "javascript:void(0)">';
        $config['cur_tag_close'] = '</a></li>';

        // "Digit" Link
        $config['num_tag_open']  = '<li>';
        $config['num_tag_close'] = '</li>';

        // Produces: class="myclass"
        //        $config['attributes'] = array('class' => 'page-link next_page');


        $CI->pagination->initialize($config);
    }
}

// image resize
function img_resize($ini_path, $dest_path, $params = array())
{

    $width        = !empty($params['width']) ? $params['width'] : NULL;
    $height       = !empty($params['height']) ? $params['height'] : NULL;
    $constraint   = !empty($params['constraint']) ? $params['constraint'] : FALSE;
    $rgb          = !empty($params['rgb']) ? $params['rgb'] : 0xFFFFFF;
    $quality      = !empty($params['quality']) ? $params['quality'] : 100;
    $aspect_ratio = isset($params['aspect_ratio']) ? $params['aspect_ratio'] : TRUE;
    $crop         = isset($params['crop']) ? $params['crop'] : TRUE;

    if(!file_exists($ini_path))
        return FALSE;

    if(!is_dir($dir = dirname($dest_path)))
        mkdir($dir);

    $img_info = getimagesize($ini_path);

    if($img_info === FALSE)
        return FALSE;


    $ini_p = $img_info[0] / $img_info[1];
    if($constraint)
    {
        $con_p  = $constraint['width'] / $constraint['height'];
        $calc_p = $constraint['width'] / $img_info[0];

        if($ini_p < $con_p)
        {
            $height = $constraint['height'];
            $width  = $height * $ini_p;
        }
        else
        {
            $width  = $constraint['width'];
            $height = $img_info[1] * $calc_p;
        }
    }
    else
    {
        if(!$width && $height)
        {
            $width = ($height * $img_info[0]) / $img_info[1];
        }
        else if(!$height && $width)
        {
            $height = ($width * $img_info[1]) / $img_info[0];
        }
        else if(!$height && !$width)
        {
            $width  = $img_info[0];
            $height = $img_info[1];
        }
    }

    preg_match('/\.([^\.]+)$/i', basename($dest_path), $match);
    $ext           = strtolower($match[1]);
    $output_format = ($ext == 'jpg') ? 'jpeg' : $ext;

    $format = strtolower(substr($img_info['mime'], strpos($img_info['mime'], '/') + 1));
    $icfunc = "imagecreatefrom" . $format;

    $iresfunc = "image" . $output_format;

    if(!function_exists($icfunc))
        return FALSE;

    $dst_x = $dst_y = 0;
    $src_x = $src_y = 0;
    $res_p = $width / $height;
    if($crop && !$constraint)
    {
        $dst_w = $width;
        $dst_h = $height;
        if($ini_p > $res_p)
        {
            $src_h = $img_info[1];
            $src_w = $img_info[1] * $res_p;
            $src_x = ($img_info[0] >= $src_w) ? floor(($img_info[0] - $src_w) / 2) : $src_w;
        }
        else
        {
            $src_w = $img_info[0];
            $src_h = $img_info[0] / $res_p;
            $src_y = ($img_info[1] >= $src_h) ? floor(($img_info[1] - $src_h) / 2) : $src_h;
        }
    }
    else
    {
        if($ini_p > $res_p)
        {
            $dst_w = $width;
            $dst_h = $aspect_ratio ? floor($dst_w / $img_info[0] * $img_info[1]) : $height;
            $dst_y = $aspect_ratio ? floor(($height - $dst_h) / 2) : 0;
        }
        else
        {
            $dst_h = $height;
            $dst_w = $aspect_ratio ? floor($dst_h / $img_info[1] * $img_info[0]) : $width;
            $dst_x = $aspect_ratio ? floor(($width - $dst_w) / 2) : 0;
        }
        $src_w = $img_info[0];
        $src_h = $img_info[1];
    }

    $isrc  = $icfunc($ini_path);
    $idest = imagecreatetruecolor($width, $height);
    if(($format == 'png' || $format == 'gif') && $output_format == $format)
    {
        imagealphablending($idest, FALSE);
        imagesavealpha($idest, TRUE);
        imagefill($idest, 0, 0, IMG_COLOR_TRANSPARENT);
        imagealphablending($isrc, TRUE);
        $quality = 0;
    }
    else
    {
        imagefill($idest, 0, 0, $rgb);
    }
    imagecopyresampled($idest, $isrc, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
    $res = $iresfunc($idest, $dest_path, $quality);


    //imagedestroy($isrc);
    //imagedestroy($idest);

    return $res;
}

/**
 * @package Helper
 * @subpackage mailSend
 * @return string
 * @author Aditya <aditya.cse04@gmail.com>
 */
if(!function_exists('mailSend'))
{
    function mailSend($to_address = array(), $subject = '', $message = '', $attachment = FALSE)
    {
        return TRUE;

        if(ENVIRONMENT == 'development')
        {
            return TRUE;
        }
        // get the CI instanse
        $CI = &get_instance();
        $CI->load->library('email');
        $config = array(
            'protocol'     => 'smtp',
            'smtp_host'    => 'pmanage.technobd.com',
            'smtp_port'    => '25',
            'smtp_user'    => 'support@pmanage.technobd.com',
            'smtp_pass'    => 'tj,fs&o#58HJ',
            'charset'      => 'utf-8',
            'newline'      => '\r\n',
            'mailtype'     => 'html',
            'smtp_timeout' => 30,
            'validation'   => TRUE
        );
        // clear the mail
        $CI->email->clear(TRUE);
        $CI->email->initialize($config);
        $CI->email->from('support@pmanage.technobd.com', $CI->config->item('site_title'));
        $CI->email->to($to_address);

        $CI->email->subject($subject);

        $CI->email->message($message);
        // attach file if found
        if($attachment)
        {
            $CI->email->attach($attachment);
        }
        try
        {
            if($CI->email->send())
            {
                return TRUE;
            }
            else
            {
                return FALSE;
            }
        } catch (Exception $e)
        {
            return FALSE;
        }
    }
}

/**
 * get parent variation id
 * @ param: product_id
 * @ param: flavor_id
 * @ return: parent_variation_id
 */
function get_parent_variation_id($product_id, $flavor_id)
{
    $ci = get_instance();

    $variation = $ci->global_model->get_row('product_variation', array('product_id'=>$product_id, 'is_parent'=>1, 'flavor_id'=>$flavor_id));

    if(!empty($variation))
    {
        return $variation->id;
    }
}