<?
function writelog( $string ) {
	global $logfile, $maxloglines, $text, $currentuser, $cms;
	
	$remhost = getenv( "REMOTE_HOST" );
	if( !$remhost ) {
		$remip = getenv("REMOTE_ADDR");
		$remhost = @gethostbyaddr( $remip );
	}
	if( $cms[support] && $currentuser )
		$string .= " $text[accessedby] User: $currentuser";
	elseif( $remhost )
		$string .= " $text[accessedby] $remhost";
	else
		$string .= " $text[accessedby] $remip";

	$lines = file( $logfile );
	if( $maxloglines && sizeof( $lines ) >= $maxloglines ) {
		array_pop( $lines );
	}
	$updated = date ("D d M Y h:i:s A");
	array_unshift( $lines, "$updated $string.\n" );
	
	$fp = @fopen( $logfile, "w" );
	if( !$fp ) { die ( "Cannot open $logfile" ); }
	
	flock( $fp, LOCK_EX );
	foreach ( $lines as $line ) {
		trim( $line );
		if( $line ) {
			fwrite( $fp, "$line" );
		}
	}
	flock( $fp, LOCK_UN );
	fclose( $fp );
}
?>
