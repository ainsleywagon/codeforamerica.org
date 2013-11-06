<?php 

// Config options
$rootPath = $_SERVER['DOCUMENT_ROOT'];
$styleguidePath = '/';
$patternsPath = $rootPath.'/patterns/';
$cssPath = $rootPath.'style/';


// Provide a filter for excluding hidden .git or .svn folders from the inc() function
class ExcludeFilter extends RecursiveFilterIterator {

    public static $FILTERS = array(
        '.svn',
        '.git'
    );
    
    public function accept() {
        // Check if a file is within one of the folders listed in the exclude list
        foreach(self::$FILTERS as $filter) {
            if(strpos($this->current()->getPath(),$filter)) {
                return false;
            }
        }
        return true;
    }
}


function inc($type,$name) {
    global $patternsPath; 
    global $styleguidePath;
    
    $filePath = $patternsPath;

    // Determine which directory to look in based on type: element or partial
    if($type=='element') {
        $filePath = $filePath.'elements';
    } elseif($type=='partial') {
        $filePath = $filePath.'partials';
    } else {
        $filePath = $filePath;
    }

    // Iterate over the appropriate path
    $objects = new RecursiveIteratorIterator(new ExcludeFilter(new RecursiveDirectoryIterator($filePath)));
    foreach($objects as $objName => $object) {
        $pos = stripos($objName, $name);
        if ($pos) {
            include($objName); // Include the fragment if the file is found
            break;
        }
    }
}


function displayPatterns($dir) {
    global $patternsPath;
    global $styleguidePath;

    $ffs = scandir($dir);

    foreach($ffs as $ff) {
        if($ff != '.' && $ff != '..') {
            $fName = basename($ff,'.html');
            $fPlain = ucwords(str_replace('-', '. ', $fName));
            $pathToFile = str_replace($patternsPath, '', $dir);
            
            if(is_dir($dir.'/'.$ff)) { // If main section
                if ($fName == 'elements' || $fName == 'partials') {
                    echo "<section class=\"xx-section\" id=\"".$fName."\">\n";
                    echo "    <h1>".$fPlain."</h1>\n";
                } else {
                    echo "<section class=\"xx-section\" id=\"".$fName."\">\n";
                    echo "    <h1 class=\"section-title\">".$fPlain."</h1>\n";
                }
            } else { // If sub section
                if(pathinfo($ff,PATHINFO_EXTENSION) == 'html') { // Skip non-HTML files
                    echo "<div class=\"pattern\" id=\"".$fName."\">\n";
                    echo "    <details class=\"pattern-details\">\n";
                    echo "        <summary class=\"pattern-name\">".$fName." <a class=\"pattern-link\" rel=\"bookmark\" href=\"".$styleguidePath."?url=".$pathToFile."/".$ff."\" title=\"View just this pattern\">#</a></summary>\n";
                    echo "            <code class=\"pattern-markup\"><textarea class=\"pattern-code\" rows=\"8\">".htmlspecialchars(@file_get_contents($dir.'/'.$ff))."</textarea></code>\n";
                    echo "        <pre class=\"pattern-usage\"><strong>Usage:</strong> ".htmlspecialchars(@file_get_contents($dir.'/'.str_replace('.html','.txt',$ff)))."</pre>\n";
                    echo "    </details>\n";
                    echo "\n";
                    include $dir.'/'.$ff;
                    echo "\n";
                    echo "</div>\n\n";
                }
            }
            
            if(is_dir($dir.'/'.$ff)) { // If main section
                displayPatterns($dir.'/'.$ff);
                echo "</section>\n\n";
            }
        }
    }
}


function displayOptions($dir) {
    global $patternsPath;
    global $styleguidePath;

    $ffs = scandir($dir);

    foreach($ffs as $ff) {
        if($ff != '.' && $ff != '..') {
            $fName = basename($ff,'.html');
            $fPlain = ucwords(str_replace('-', '. ', $fName));
            $pathToFile = str_replace($patternsPath, '', $dir);

            if(is_dir($dir.'/'.$ff)) { // If main section
                if ($fName == 'elements' || $fName == 'partials' || $fName == 'forms') {
                    echo "<optgroup label=\"".$fPlain."\"/>\n";
                } else {
                    echo "    <option value=\"#".$fName."\">".$fPlain."</option>\n";
                }
            } else { // If sub section
                if(pathinfo($ff,PATHINFO_EXTENSION) == 'html') { // Skip non-HTML files
                    echo "    <option value=\"#".$fName."\">&#160;&#160;&#160;&#160;".$fName."</option>\n";
                }
            }

            if(is_dir($dir.'/'.$ff)) {
                displayOptions($dir.'/'.$ff);
            }
            
            if(is_dir($dir.'/'.$ff)) { // If main section
                if ($fName == 'elements' || $fName == 'partials') {
                    echo "</optgroup>\n";
                }
            }
        }
    }
}


function displayList($dir) {
    global $patternsPath;
    global $styleguidePath;
echo 'wtf?';
    $ffs = scandir($dir);

    foreach($ffs as $ff) {
        if($ff != '.' && $ff != '..') {
            $fName = basename($ff,'.html');
            $fPlain = ucwords(str_replace('-', '. ', $fName));
            $pathToFile = str_replace($patternsPath, '', $dir);

            if(is_dir($dir.'/'.$ff)) { // If main section
                if ($fName == 'elements' || $fName == 'partials') {
                    echo "<li><a href=\"#".$fName."\">$fPlain</a>\n";
                } else {
                    echo "<li><a href=\"#".$fName."\">$fPlain</a>\n";
                }
            } else { // If sub section
                if(pathinfo($ff,PATHINFO_EXTENSION) == 'html') { // Skip non-HTML files
                    echo "    <li><a href=\"#".$fName."\">".$fName."</a></li>\n";
                }
            }
            
            if(is_dir($dir.'/'.$ff)) { // If main section
                if ($fName == 'elements' || $fName == 'partials') {
                    echo "</li>\n";
                }
            }
        }
    }
}

?>