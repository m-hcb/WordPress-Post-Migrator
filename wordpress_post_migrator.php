<?php
/*
Template Name: WordPress Post Migrator
*/
?>

<?php
get_header();
?>

<?php

set_time_limit(-1);

function set_featured_image($post_id, $filename)
{
    $wp_filetype = wp_check_filetype(basename($filename), null);
    $attachment  = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
        'post_content' => '',
        'post_status' => 'publish'
    );
    $attach_id   = wp_insert_attachment($attachment, $filename, $post_id);
    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
    if (wp_update_attachment_metadata($attach_id, $attach_data)) {
        return update_post_meta($post_id, '_thumbnail_id', $attach_id);
    }
}

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "test";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM not_wordpress_posts";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    
    while ($row = $result->fetch_assoc()) {
        
        $the_post_id = $row['ID'];
        
        $the_author_address = $row['ADDRESS'];
        
        $the_post_content = $row['content'];
        
        $post = array(
            'post_content' => $the_post_content,
            'post_name' => $row['TITLE'],
            'post_title' => $row['TITLE'],
            'post_status' => 'publish',
            'post_type' => 'custom_post_type',
            'post_author' => 1,
            'post_category' => array(
                15
            )
        );
        
        $post_id = wp_insert_post($post, $wp_error);
        
        $option_array[0] = array(
            'optionLabel' => 'ADDRESS',
            'optionValue' => $the_author_address,
            'optionUrl' => '',
            'optionlabelordernumber' => ''
        );
        
        update_post_meta($post_id, 'qode_portfolio', $option_array);
        
        unset($post_image_array);
        
        $upload_dir = wp_upload_dir();
        
        $the_img_url_for_featured = $upload_dir['basedir'] . "/2015/04/";
        
        $the_img_url = "http://localhost/yoursite/wp-content/uploads/2015/04/";
        
        $sql4img = "SELECT * FROM pics WHERE POSTID=$the_post_id";
        
        $result4img = $conn->query($sql4img);
        
        if ($result4img->num_rows > 0) {
            
            $j = 0;
            
            while ($row4img = $result4img->fetch_assoc()) {
                
                if ($j == 0) {
                    
                    set_featured_image($post_id, $the_img_url_for_featured . $row4img['FILE']);
                    
                    $j++;
                }
                
                $post_image_array[$ind] = array(
                    'portfolioimg' => $the_img_url . $row4img['FILE'],
                    'portfoliotitle' => '',
                    'portfolioimgordernumber' => '',
                    'portfoliovideotype' => '',
                    'portfoliovideoid' => ''
                );
                $ind++;
            }
        }
        
        
        update_post_meta($post_id, 'qode_portfolio_images', $post_image_array);
        update_post_meta($post_id, 'qode_portfolio_address', $full_address);
        update_post_meta($post_id, 'qode_choose-portfolio-single-view', true);
        
    }
} else {
    echo "0 results";
}


?>


<?php
get_footer();
?>
