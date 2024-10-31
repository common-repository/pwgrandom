<?php
/*
Plugin Name: PWGRandom
Plugin URI: http://chezju.318racing.com/plugins
Description: Show a random picture of your PhpWebGallery in your SideBar
Version: 1.11
Author: Julien Baessens
Author URI: http://chezju.318racing.com

 Copyright 2008  Julien Baessens  (email : j.baessens@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function PWGRandom_display_picture() 
{
	global $wpdb;
	
	// Get the plugin options : picture size, lightbox, path and text
	$max_size 			= get_option("pwgrandom_size");
	$lb 				= get_option("pwgrandom_lb");
	$pwg_path 			= get_option("pwgrandom_path");
	$pwg_url_path 		= get_option("pwgrandom_url_path");
	$pwg_title			= get_option("pwgrandom_title");
	$pwg_category		= get_option("pwgrandom_category");
	$pwg_nbImages		= get_option("pwgrandom_nb_images");
	$pwg_nbImages_row	= get_option("pwgrandom_nb_images_row");
	$pwg_image_spacing	= get_option("pwgrandom_nb_image_spacing");
	
	// Check if the path are correcly insert in the option menu
	if($pwg_path == "" || $pwg_url_path == "")
	{
		echo'Check the PWGRandom configuration';
		return;
	}
	
	// Include the sql configuration of phpwebgallery
	include($pwg_path."/include/mysql.inc.php");
	
	//Select the sql database of phpwebgallery if different from wordpress one
	if($cfgBase != DB_NAME);
		$wpdb->select($cfgBase);
	
	//Create the table names in phpwebgallery database
	$tableNameImages = $prefixeTable."images";
	$tableNameCategories = $prefixeTable."categories";
	
	$resImage = mysql_query("SELECT tn_ext,path,storage_category_id FROM ".$tableNameImages);
	$nb=mysql_num_rows($resImage);

	//Display the pictures
	if($pwg_title!="")
		echo'<h2>'.$pwg_title.'</h2>';
		
	echo"<div align='center'><table width='".$max_size."' cellspacing='".$pwg_image_spacing."'>";
	for($i = 0; $i < $pwg_nbImages; $i++)
	{
		if($i % $pwg_nbImages_row == 0)
			echo'<tr>';
			
		echo'<td align="center">';
			
		// Select a random picture
		$imageIndex = rand(0,$nb-1);

		//Find the picture and thumbnail path
		$imagePath 	= mysql_result($resImage , $imageIndex , 'path');
		$categoryId = mysql_result($resImage , $imageIndex , 'storage_category_id');
		$ext		= mysql_result($resImage , $imageIndex , 'tn_ext');
		
		$lastSlash 	= strrpos($imagePath,"/");
		$folderPath = substr($imagePath,1,$lastSlash);
		$lastPoint 	= strrpos($imagePath,".");
		$imageName 	= substr($imagePath,$lastSlash+1,$lastPoint-$lastSlash);
				
		$thumbnailRelativePath 	= $pwg_path.$folderPath."thumbnail/TN-".$imageName.$ext;	

		$originImg = imagecreatefromjpeg($thumbnailRelativePath);		
		if($originImg != "")
		{		
			$imagePath = $pwg_url_path.substr($imagePath,1);
			$lastSlash = strrpos($imagePath,"/");
			$thumbnailPath = substr($imagePath,0,$lastSlash)."/thumbnail/TN-".substr($imagePath,$lastSlash+1);
			
			// Get the picture size
			$width = imagesx($originImg);
			$height = imagesy($originImg);
			imagedestroy($originImg);
		
			//Active lightbox or not
			$html = '<a ';
			if($lb=="on") 
				$html .= 'rel="lightbox"';
			$html .= 'href="'.$imagePath.'"><img ';
			
			//Set the maximum size if needed
			if($width > $height && $width >= $max_size)
				$html .= 'width = "'.$max_size.'"';
			elseif($width < $height && $height >= $max_size)
				$html .= 'height = "'.$max_size.'"';
			
			$html .= ' border="0" src="'.$thumbnailPath.'"></a>';
			
			//Add the html code for the picture
			echo $html;
		}
		else
		{
			echo"The image found in PWG doesn't exist on this server!";
		}
			
		//Get the category name and link
		$resCat = mysql_query("SELECT name FROM ".$tableNameCategories." WHERE id=".$categoryId);
		$categoryName = mysql_result($resCat , 0 , 'name');
		$categoryLink = $pwg_url_path."/index.php?/category/".$categoryId;
			
		//Display the text behind the picture
		echo '<br/>'.$pwg_category.'<a target="_blank" href="'.$categoryLink.'">'.$categoryName.'</a>';
			
		echo'</td>';
		
		if($i % $pwg_nbImages_row == $pwg_nbImages_row-1)
			echo'</tr>';
	}
	
	//Return to the sql database of wordpress
	$wpdb->select(DB_NAME);
	echo'</table></div>';
}

//Add options
add_option("pwgrandom_size", "128", "", "no");
add_option("pwgrandom_path", "../phpwebgallery", "", "no");
add_option("pwgrandom_url_path", "", "", "no");
add_option("pwgrandom_db", "", "", "no");
add_option("pwgrandom_lightbox", "", "", "no");
add_option("pwgrandom_title", "Random picture", "", "no");
add_option("pwgrandom_category", "Category : ", "", "no");
add_option("pwgrandom_nb_images", "1", "", "no");
add_option("pwgrandom_nb_images_row", "1", "", "no");
add_option("pwgrandom_nb_image_spacing", "0", "", "no");

// action function for above hook
function PWGRandom_add_pages() 
{
add_options_page("PWGRandom options", "PWGRandom", 8, "pwgrandom", "PWGRandom_displayoptions");
}

function PWGRandom_displayoptions()
{

	// variables for the field and option names 
    $hidden_field_name = 'pwgrandom_submit_hidden';

    // Read in existing option value from database
    $opt_max_size = get_option( "pwgrandom_size" );
	$opt_gallery_path = get_option( "pwgrandom_path" );
	$opt_gallery_url_path = get_option( "pwgrandom_url_path" );
	$opt_db = get_option( "pwgrandom_db" );
	$opt_lb = get_option( "pwgrandom_lb" );
	$opt_title = get_option( "pwgrandom_title" );
	$opt_category = get_option( "pwgrandom_category" );
	$opt_nb_images = get_option( "pwgrandom_nb_images" );
	$opt_nb_images_row = get_option( "pwgrandom_nb_images_row" );
	$opt_nb_image_spacing = get_option( "pwgrandom_nb_image_spacing" );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( $_POST[ $hidden_field_name ] == 'Y' ) 
	{
        // Read their posted value
        $opt_max_size = $_POST[ "pwgrandom_size" ];
		$opt_gallery_path = $_POST[ "pwgrandom_path" ];
		$opt_gallery_url_path = $_POST[ "pwgrandom_url_path" ];
		$opt_db = $_POST[ "pwgrandom_db" ];
		$opt_lb = $_POST[ "pwgrandom_lb" ];
		$opt_title = $_POST[ "pwgrandom_title" ];
		$opt_category = $_POST[ "pwgrandom_category" ];
		$opt_nb_images = $_POST[ "pwgrandom_nb_images" ];
		$opt_nb_images_row = $_POST[ "pwgrandom_nb_images_row" ];
		$opt_nb_image_spacing = $_POST[ "pwgrandom_nb_image_spacing" ];
		
        // Save the posted value in the database
        update_option( "pwgrandom_size", $opt_max_size );
		update_option( "pwgrandom_path", $opt_gallery_path );
		update_option( "pwgrandom_url_path", $opt_gallery_url_path );
		update_option( "pwgrandom_db", $opt_db );
		update_option( "pwgrandom_lb", $opt_lb );
		update_option( "pwgrandom_title", $opt_title );
		update_option( "pwgrandom_category", $opt_category );
		update_option( "pwgrandom_nb_images", $opt_nb_images );
		update_option( "pwgrandom_nb_images_row", $opt_nb_images_row );
		update_option( "pwgrandom_nb_image_spacing", $opt_nb_image_spacing	 );
?>
<div class="updated"><p><strong><?php _e('Options saved.', 'PWGRandom' ); ?></strong></p></div>
<?php
    }    
?>
<div class="wrap">
	<h2><?php _e('PWGRandom options menu') ?></h2>
	<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Title:</th>
				<td>
					<input type="text" name="pwgrandom_title" value="<?php echo $opt_title; ?>" size="20">
					<br/> The title of the widget
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Category text:</th>
				<td>
					<input type="text" name="pwgrandom_category" value="<?php echo $opt_category; ?>" size="20">
					<br/> Text before the category name. eg : "Category :" Category Name
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Gallery path:</th>
				<td>
					<input type="text" name="pwgrandom_path" value="<?php echo $opt_gallery_path; ?>" size="20">
					<br/> Insert the relative path from your blog root to the phpwebgallery root folder. eg : ../phpwebgallery
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Gallery url:</th>
				<td>
					<input type="text" name="pwgrandom_url_path" value="<?php echo $opt_gallery_url_path; ?>" size="20">
					<br/> Insert the full url of your phpwebgallery. eg : http://yourdomain.com/phpwebgallery
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Max Thumbnail size:</th>
				<td>
					<input type="text" name="pwgrandom_size" value="<?php echo $opt_max_size; ?>" size="20">
					<br/> The maximum size (apply to largest side) of the picture in the sidebar (in px).
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Number of images:</th>
				<td>
					<input type="text" name="pwgrandom_nb_images" value="<?php echo $opt_nb_images; ?>" size="20">
					<br/> The number of pictures displayed in the sidebar.
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Number of images in a row:</th>
				<td>
					<input type="text" name="pwgrandom_nb_images_row" value="<?php echo $opt_nb_images_row; ?>" size="20">
					<br/> The number of pictures displayed in a row.
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Space between images:</th>
				<td>
					<input type="text" name="pwgrandom_nb_image_spacing" value="<?php echo $opt_nb_image_spacing; ?>" size="20">
					<br/> The space in pixel btween each picture.
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Activate Lightbox:</th>
				<td>
					<?php
					if($opt_lb == "on")
						echo'<input type="checkbox" name="pwgrandom_lb" checked size="20">';
					else
						echo'<input type="checkbox" name="pwgrandom_lb" size="20">';
					?>
					<br/> Activate or not the lightbox effect when you open the picture.
				</td>
			</tr>
		</table>
		<p class="submit">
		<input type="submit" name="Submit" value="<?php _e('Save Changes' ) ?>" />
		</p>
	</form>
</div>

<?php
}

function PWGRandom_widget_displayoptions()
{
	// variables for the field and option names 
    $hidden_field_name = 'pwgrandom_submit_hidden';

    // Read in existing option value from database
	$opt_lb = get_option( "pwgrandom_lb" );
	$opt_nb_images = get_option( "pwgrandom_nb_images" );
	$opt_nb_images_row = get_option( "pwgrandom_nb_images_row" );
	$opt_title = get_option( "pwgrandom_title" );
	$opt_category = get_option( "pwgrandom_category" );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( $_POST[ $hidden_field_name ] == 'Y' ) 
	{
        // Read their posted value
		$opt_lb = $_POST[ "pwgrandom_lb" ];
		$opt_title = $_POST[ "pwgrandom_title" ];
		$opt_category = $_POST[ "pwgrandom_category" ];
		$opt_nb_images = $_POST[ "pwgrandom_nb_images" ];
		$opt_nb_images_row = $_POST[ "pwgrandom_nb_images_row" ];
		
        // Save the posted value in the database
		update_option( "pwgrandom_lb", $opt_lb );
		update_option( "pwgrandom_nb_images", $opt_nb_images );
		update_option( "pwgrandom_nb_images_row", $opt_nb_images_row );
		update_option( "pwgrandom_title", $opt_title );
		update_option( "pwgrandom_category", $opt_category );
    }    
?>
	<p>
	<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
	<label for="pwgrandom_title">
	The title :<br/>
	<input type="text" id="pwgrandom_title" name="pwgrandom_title" value="<?php echo $opt_title; ?>" style="width: 200px;">
	</label>
	<br/><br/> 
	
	<label for="pwgrandom_category">
	Text before the category name :<br/>
	<input type="text" id="pwgrandom_category" name="pwgrandom_category" value="<?php echo $opt_category; ?>" style="width: 200px;">
	</label>
	<br/><br/> 
	
	<label for="pwgrandom_nb_images">
	Number of pictures :
	<input type="text" id="pwgrandom_nb_images" name="pwgrandom_nb_images" value="<?php echo $opt_nb_images; ?>" style="width: 20px;">
	</label>
	<br/><br/>
	
	<label for="pwgrandom_nb_images_row">
	Number of pictures in a row :
	<input type="text" id="pwgrandom_nb_images_row" name="pwgrandom_nb_images_row" value="<?php echo $opt_nb_images_row; ?>" style="width: 20px;">
	</label>
	<br/><br/>

	<label for="pwgrandom_lb">
	Activate Lightbox effect :
		<?php
		if($opt_lb == "on")
			echo'<input type="checkbox" id="pwgrandom_lb" name="pwgrandom_lb" checked style="width: 20px;">';
		else
			echo'<input type="checkbox" id="pwgrandom_lb" name="pwgrandom_lb" style="width: 20px;">';
		?>
	</label>
	<br/><br/>
	More options are on the <a href="options-general.php?page=pwgrandom.php">plugin page</a>.
	<br/>
	</p>

<?php
}


function widget_pwgrandom_init() 
{
	if ( !function_exists('register_sidebar_widget') )
		return;
		
	// Register widget for use
	register_sidebar_widget(array('PWGRandom', 'widgets'), 'PWGRandom_display_picture');

	// Register settings
	register_widget_control(array('PWGRandom', 'widgets'), 'PWGRandom_widget_displayoptions',300,60);
}

// Run code and init
add_action('widgets_init', 'widget_pwgrandom_init');

// Hook for adding admin menus
add_action('admin_menu', 'PWGRandom_add_pages');

?>