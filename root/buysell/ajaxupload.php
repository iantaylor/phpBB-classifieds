<?php
define('IN_PHPBB', true);

$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);
include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);


// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/classified');

	function uploadImage($fileName, $maxSize, $max_width, $fullPath, $relPath, $color_red, $color_green, $color_blue, $max_height = null){
	
	global $user;
	
		$folder = $relPath;
		$maxlimit = $maxSize;
		$allowed_ext = "jpg,jpeg,gif,png,bmp";
		$match = "";
		$filesize = $_FILES[$fileName]['size'];
		if($filesize > 0){	
			$filename = strtolower($_FILES[$fileName]['name']);
			$filename = preg_replace('/\s/', '_', $filename);
		   	if($filesize < 1)
		   	{ 
				$errorList[] = $user->lang['EMPTY_FILE'];
			}
			if($filesize > $maxlimit)
			{ 
				$errorList[] = $user->lang['TO_BIG'];
			}
			$errorList = array();
			if(count($errorList)<1)
			{
				$file_ext = preg_split("/\./",$filename);
				$allowed_ext = preg_split("/\,/",$allowed_ext);
				foreach($allowed_ext as $ext)
				{
					if($ext==end($file_ext))
					{
						$match = "1"; // File is allowed
						$NUM = time();
						$front_name = substr($file_ext[0], 0, 15);
						$newfilename = $front_name."_".$NUM.".".end($file_ext);
						$filetype = end($file_ext);
						$save = $folder.$newfilename;
						if(!file_exists($save)){
							list($width_orig, $height_orig) = getimagesize($_FILES[$fileName]['tmp_name']);
							if($max_height == null){
								if($width_orig < $max_width)
								{
									$fwidth = $width_orig;
								}else
								{
									$fwidth = $max_width;
								}
								$ratio_orig = $width_orig/$height_orig;
								$fheight = $fwidth/$ratio_orig;
								
								$blank_height = $fheight;
								$top_offset = 0;
									
							}
							else
							{
								if($width_orig <= $max_width && $height_orig <= $max_height)
								{
									$fheight = $height_orig;
									$fwidth = $width_orig;
								}
								else
								{
									if($width_orig > $max_width)
									{
										$ratio = ($width_orig / $max_width);
										$fwidth = $max_width;
										$fheight = ($height_orig / $ratio);
										if($fheight > $max_height){
											$ratio = ($fheight / $max_height);
											$fheight = $max_height;
											$fwidth = ($fwidth / $ratio);
										}
									}
									if($height_orig > $max_height)
									{
										$ratio = ($height_orig / $max_height);
										$fheight = $max_height;
										$fwidth = ($width_orig / $ratio);
										if($fwidth > $max_width){
											$ratio = ($fwidth / $max_width);
											$fwidth = $max_width;
											$fheight = ($fheight / $ratio);
										}
									}
								}
								if($fheight < 45)
								{
									$blank_height = 45;
									$top_offset = round(($blank_height - $fheight)/2);
								}
								else
								{
									$blank_height = $fheight;
								}
							}
							$image_p = imagecreatetruecolor($fwidth, $blank_height);
							$white = imagecolorallocate($image_p, $color_red, $color_green, $color_blue);
							imagefill($image_p, 0, 0, $white);
							switch($filetype)
							{
								case "gif":
									$image = @imagecreatefromgif($_FILES[$fileName]['tmp_name']);
								break;
								case "jpg":
									$image = @imagecreatefromjpeg($_FILES[$fileName]['tmp_name']);
								break;
								case "jpeg":
									$image = @imagecreatefromjpeg($_FILES[$fileName]['tmp_name']);
								break;
								case "png":
									$image = @imagecreatefrompng($_FILES[$fileName]['tmp_name']);
								break;
							}
							@imagecopyresampled($image_p, $image, 0, $top_offset, 0, 0, $fwidth, $fheight, $width_orig, $height_orig);
							switch($filetype){
								case "gif":
								
									if(!@imagegif($image_p, $save))
									{
										$errorList[]= $user->lang['NO_PERM_GIF'];
									}
								break;
								case "jpg":
									if(!@imagejpeg($image_p, $save, 100))
									{
										$errorList[]= $user->lang['NO_PERM_JPG'];
									}
								break;
								case "jpeg":
									if(!@imagejpeg($image_p, $save, 100))
									{
										$errorList[]= $user->lang['NO_PERM_JPEG'];
									}
								break;
								case "png":
									if(!@imagepng($image_p, $save, 0))
									{
										$errorList[]= $user->lang['NO_PERM_PNG'];
									}
								break;
							}
							@imagedestroy($filename);
						}
						else
						{
							$errorList[]= $user->lang['DUP_IMAGE'];
						}	
					}
				}		
			}
		}
		else
		{
			$errorList[]= $user->lang['NO_FILE'];
		}
		if(!$match)
		{
		   	$errorList[]= $user->lang['BAD_TYPE']."<br /> $filename";
		}
		if(sizeof($errorList) == 0)
		{
			return $fullPath.$newfilename;
		}
		else
		{
			$eMessage = array();
			for ($x=0; $x<sizeof($errorList); $x++)
			{
				$eMessage[] = $errorList[$x];
			}
		   	return $eMessage;
		}
	}
	
	$filename = strip_tags($_REQUEST['filename']);
	$maxSize = strip_tags($_REQUEST['maxSize']);
	$max_width = strip_tags($_REQUEST['max_width']);
	$fullPath = strip_tags($_REQUEST['fullPath']);
	$relPath = strip_tags($_REQUEST['relPath']);
	$color_red = strip_tags($_REQUEST['color_red']);
	$color_green = strip_tags($_REQUEST['color_green']);
	$color_blue = strip_tags($_REQUEST['color_blue']);
	$max_height = strip_tags($_REQUEST['max_height']);
	$filesize_image = $_FILES[$filename]['size'];
	if($filesize_image > 0)
	{
		$upload_image = uploadImage($filename, $maxSize, $max_width, $fullPath, $relPath, $color_red, $color_green, $color_blue, $max_height);
		if(is_array($upload_image))
		{
			foreach($upload_image as $key => $value) 
			{
				if($value == "-ERROR-") 
				{
					unset($upload_image[$key]);
				}
			}
			$document = array_values($upload_image);
			for ($x=0; $x<sizeof($document); $x++)
			{
				$errorList[] = $document[$x];
			}
			$imgUploaded = false;
		}
		else
		{
			$imgUploaded = true;
		}
	}
	else
	{
		$imgUploaded = false;
		$errorList[] = $user->lang['EMPTY_FILE'] ;
	}
?>
<?php
	if($imgUploaded)
	{
		echo '<img src="./images/success.gif" width="16" height="16" border="0" style="marin-bottom: -4px;" />' . $user->lang['SUCC'] .'<br /><img src="' . $upload_image . '" border="0" style="max-width: 200px" /><br />';
		if($max_width == 100)
		{
		echo $user->lang['THUMB_INSTRUCTIONS'] . '<br /><form><input type="text" name="theText" onClick="javascript:this.form.theText.focus();this.form.theText.select();" value="' . generate_board_url() . str_replace('..','',$upload_image) . '" /></form><br />';
		}
		else
		{
		echo $user->lang['INSTRUCTION'] . '<br /><form><input type="text" name="theText" onClick="javascript:this.form.theText.focus();this.form.theText.select();" value="[img]' . generate_board_url() . str_replace('..','',$upload_image) . '[/img]" /></form><br />';
		}	
		
		
	}
	else
	{
		echo '<img src="./images/error.gif" width="16" height="16px" border="0" style="marin-bottom: -3px;" />  ' . $user->lang['ERRORS_FOUND'] . '<br />';
		foreach($errorList as $value)
		{
	    		echo $value.',';
		}
	}
?>