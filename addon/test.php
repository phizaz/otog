<?php
$file = fopen("http://otog.org/judge/upload/", "r");
while(!feof($file)){
    $line = fgets($file);
    for($i = 0; $i < strlen($line); $i++)
    {
    	if($line[$i]=='<')
    		echo "&lt";
    	else if($line[$i]=='<')
    		echo "&gt;";
    	else if($line[$i]=='	')
    		echo "<span style='padding-left:2em'></span>";
    	else if($line[$i]==' ')
    		echo "&nbsp;";
    	else
    		echo $line[$i];
    }
    echo "<br>";
    # do same stuff with the $line
}
fclose($file);
?>