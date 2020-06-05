<?php

$folder_in = "./Google Photos Backup 2020-05-27\Grouped\Takeout\Google Photos/";

function startsWith ($string, $startString) 
{ 
    $len = strlen($startString); 
    return (substr($string, 0, $len) === $startString); 
} 

function endsWith($string, $endString) 
{ 
    $len = strlen($endString); 
    if ($len == 0) { 
        return true; 
    } 
    return (substr($string, -$len) === $endString); 
} 

$dateFolders = scandir($folder_in);

foreach($dateFolders as $i => $dateFolder){
    if($dateFolder == "." || $dateFolder == ".."){
        continue;
    }

    //if($dateFolder != "__trial"){
    //    continue;
    //}

    $dateData = Array();

    print "$dateFolder<br>";
    $photosFolderForDate = $folder_in . $dateFolder;
    $photoFilesForDate = scandir($photosFolderForDate);
    foreach($photoFilesForDate as $i => $photoFile){
        if($photoFile == "." || $photoFile == ".."){
            continue;
        }

        if(is_dir($photosFolderForDate."/".$photoFile)){
            continue;
        }

        $extension = strtolower(pathinfo($photoFile, PATHINFO_EXTENSION));

        print "<div style='margin-left: 40px;'>$extension: $photoFile</div>";
        
        if($extension != "json"){
            continue;
        }
        
        if(startsWith($photoFile, "metadata")){
            continue;
        }

        $jsonData = json_decode(file_get_contents($photosFolderForDate."/".$photoFile), true);

        $photoTitle = $jsonData["title"];

        $dateData[$photoTitle] = $jsonData;
    }

    //Move original files
    $originalsFolder = $photosFolderForDate."/original";
    if(!file_exists($originalsFolder)){
        mkdir($originalsFolder);
    }

    foreach($dateData as $photoID => $photoData){
        $photoTitleFull = $photoData["title"];
        $photoTitleExtension = pathinfo($photoTitleFull, PATHINFO_EXTENSION);
        $photoTitleFullWithoutExtension = pathinfo($photoTitleFull, PATHINFO_FILENAME);
        $photoTitleClampedName = substr($photoTitleFullWithoutExtension, 0, 50 - strlen($photoTitleExtension)).".".$photoTitleExtension;
        if(file_exists($photosFolderForDate."/".$photoTitleClampedName)){
            print "Moving file: ".$photoTitleClampedName."<br>";
            rename($photosFolderForDate."/".$photoTitleClampedName, $originalsFolder."/".$photoTitleClampedName);
        }else{
            print "Missing file: ".$photoTitleClampedName."<br>";
        }
    }

    //Move edited photos
    $editedFolder = $photosFolderForDate."/edited";
    if(!file_exists($editedFolder)){
        mkdir($editedFolder);
    }
    
    $photoFilesForDateAfterMoveOriginals = scandir($photosFolderForDate);
    foreach($photoFilesForDateAfterMoveOriginals as $i => $photoFile){
        if($photoFile == "." || $photoFile == ".."){
            continue;
        }

        if(is_dir($photosFolderForDate."/".$photoFile)){
            continue;
        }

        $extension = strtolower(pathinfo($photoFile, PATHINFO_EXTENSION));
        if($extension == "json"){
            continue;
        }

        if($extension != "jpg" && $extension != "jpeg"){
            continue;
        }

        print "Found edited file: ".$photoFile."<br>";
        if(exif_imagetype($photosFolderForDate."/".$photoFile)){
            print "VALID image - MOVING<br>";
            rename($photosFolderForDate."/".$photoFile, $editedFolder."/".$photoFile);
        }else{
            print "ERROR image<br>";
        }
    }
    
    //Convert remaining photos to mp4
    $recoveredVideosFolder = $photosFolderForDate."/recoveredVideos";
    if(!file_exists($recoveredVideosFolder)){
        mkdir($recoveredVideosFolder);
    }
    
    $photoFilesForDateAfterMoveOriginalsAndEdited = scandir($photosFolderForDate);
    foreach($photoFilesForDateAfterMoveOriginalsAndEdited as $i => $photoFile){
        if($photoFile == "." || $photoFile == ".."){
            continue;
        }

        if(is_dir($photosFolderForDate."/".$photoFile)){
            continue;
        }

        $extension = strtolower(pathinfo($photoFile, PATHINFO_EXTENSION));
        if($extension == "json"){
            continue;
        }

        if($extension != "jpg" && $extension != "jpeg"){
            continue;
        }

        print "Found probable mislabeled video file: ".$photoFile."<br>";

        $photoFileFileName = pathinfo($photoFile, PATHINFO_FILENAME);
        rename($photosFolderForDate."/".$photoFile, $recoveredVideosFolder."/".$photoFileFileName.".mp4");
    }
    
    //Store JSON data
    $jsonPhotoFolder = $photosFolderForDate."/jsonPhoto";
    if(!file_exists($jsonPhotoFolder)){
        mkdir($jsonPhotoFolder);
    }
    $jsonAlbumFolder = $photosFolderForDate."/jsonAlbum";
    if(!file_exists($jsonAlbumFolder)){
        mkdir($jsonAlbumFolder);
    }
    
    $photoFilesForDateAfterAllMove = scandir($photosFolderForDate);
    foreach($photoFilesForDateAfterAllMove as $i => $photoFile){
        if($photoFile == "." || $photoFile == ".."){
            continue;
        }

        if(is_dir($photosFolderForDate."/".$photoFile)){
            continue;
        }

        $extension = strtolower(pathinfo($photoFile, PATHINFO_EXTENSION));
        if($extension != "json"){
            continue;
        }

        if(startsWith($photoFile, "metadata")){
            print "Found Album Json File: ".$photoFile."<br>";
            rename($photosFolderForDate."/".$photoFile, $jsonAlbumFolder."/".$photoFile);
        }else{
            print "Found Photo Json File: ".$photoFile."<br>";
            rename($photosFolderForDate."/".$photoFile, $jsonPhotoFolder."/".$photoFile);
        }
    }

    print_r($dateData);
}

?>
