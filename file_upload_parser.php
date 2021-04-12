<?php
$target_dir = "./uploads/";
$new_file_name = date('Y_m_d_h_i_s_') . basename($_FILES["file1"]["name"]);
$target_file = $target_dir . $new_file_name;
$uploadOk = 1;
$fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if file already exists
if (file_exists($target_file)) {
  echo "Sorry, file already exists.";
  $uploadOk = 0;
}

// Check file size
if ($_FILES["file1"]["size"] > 500000) {
  echo "Sorry, your file is too large.";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["file1"]["tmp_name"], $target_file)) {
    echo htmlspecialchars( $new_file_name );
  } else {
    echo "Sorry, there was an error uploading your file.";
  }
}
?>