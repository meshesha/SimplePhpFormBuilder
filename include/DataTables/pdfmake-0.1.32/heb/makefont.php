<?php

    $output = "this.pdfMake = this.pdfMake || {}; this.pdfMake.vfs = {";
    $phpDir=dir('.');
    while (($file=$phpDir->read())!==false) {
        if ($file!='..' && $file!='.' && $file!='makefont.php' && $file!='vfs_fonts.js') {
            $output .= '"';
            $output .= $file;
            $output .= '":"';
            $output .= base64_encode(file_get_contents($file));
            $output .= '",';
        }
    }
    $output=substr($output,0,-1);
	$output .= "}";

    if (isset($_REQUEST['tofile'])) {
		$fh = fopen('vfs_fonts.js', 'w') or die("CAN'T OPEN FILE FOR WRITING");
		fwrite($fh,$output);
		fclose($fh);
        echo 'vjs_fonts.js created';
    } else {
        echo $output;
    }