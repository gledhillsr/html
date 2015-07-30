<?	/* This is the Header. You can use HTML and PHP code here. 
           It will be included at the top of the page.             */ 
if($page_title) {
  echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
  echo "<html>\n";
  echo "<head>\n";
  echo "<title>" . $page_title . "</title>\n";

if (!$agent) {
  echo "<link rel=\"STYLESHEET\" type=\"text/css\" href=\"includes/styles.css\">";
}

  if (file_exists($styles_file)) {
    include($styles_file); 
  }
  if($extra_header_str)
    echo $extra_header_str;
//force page NOT to be cached
  echo "<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">\n";
  echo "<META HTTP-EQUIV=\"Expires\" CONTENT=\"-1\">\n";
// end of cache code

  echo "</head>\n";
  echo "<body bgcolor=\"" . $body_bgcolor  ."\" link=\"" . $body_link_color . "\" vlink=\"" . $body_vlink_color . "\" alink=\"" . $body_alink_color . "\">";

} // if no page title, assume DOCTYPE, <HEAD>, AND <BODY> ARE DONE
if ($agent) {
 include("agent_header.php"); 
} else {
 include("default_header.php");
} 
?>
