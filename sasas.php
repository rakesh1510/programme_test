<?php
//it called by Defaul when hot the URl
$filename = 'test.txt';
$text = 'This text will be converted to audio recording';
$outputfile = basename($filename, ".txt");
$outputfile = $outputfile . '.wav';

if (!$handle = fopen($filename, "w"))
{
    //we cannot open the file handle!
    return false;
}
// Write $text to our opened file.
if (fwrite($handle, $text) === FALSE)
{
    //couldn't write the file...Check permissions
    return false;
}

fclose($handle);

//initialise and execute the festival C engine
//make sure that your environment is set to find the festival binaries!
$cmd = "text2wave $filename -o $outputfile";
//execute the command
exec($cmd);

unlink($filename);
echo "Recording is successful. \nRecording File: " . $outputfile;
//finally return the uptput file path and filename
return $outputfile;
echo $outputfile;
?>