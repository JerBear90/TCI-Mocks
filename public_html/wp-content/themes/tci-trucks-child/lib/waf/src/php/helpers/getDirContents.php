<?php
function getDirContents( $filesPath ) {
    $files = [];
    // d('get files',$filesPath);
    $dirContents = @scandir($filesPath);
    if( is_array($dirContents) ) 
        foreach( $dirContents as $dir ) {
            if( $dir === '.' || $dir === '..' ) continue;
            $dirP = $filesPath.'/'.$dir;
            if( is_dir($dirP) ) {
                foreach( scandir($dirP) as $file ) {
                    $name = pathinfo( $file, PATHINFO_FILENAME );
                    if( $file === '.' || $file === '..' ) continue;
                    // d($dirP,$file);
                    $content = file_get_contents($dirP.'/'.$file);
                    $content = str_replace( "\n", "", $content );
                    $files[$dir][$name] = $content;
                }
            } else if( file_exists($dirP) ) {
                $name = pathinfo( $dir, PATHINFO_FILENAME );
                $content = file_get_contents($dirP);
                $json = json_decode( $content, true );
                $files[$name] = is_array($json) ? $json : $content;
            }
        }
    return $files;
}