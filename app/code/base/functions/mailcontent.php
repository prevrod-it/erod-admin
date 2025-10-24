<?php
function mail_content($tittle,$greeting,$contentitens) {
	if (is_array($contentitens)) {
		$contentlines = "";
		foreach ($contentitens as $line) {
			if ($line != "<br>") {
				$contentlines .= "$line<br>";	
			} else {
				$contentlines .= "$line";	
			}			
		}
	} else {
		$contentlines = $contentitens;
	}
	
	$content = "<html>\n";

	$content .= "<head>\n";
	$content .= "<title>$tittle</title>\n";
	$content .= "<style type=\"text/css\">\n";
	$content .= "<!--\n";
	$content .= "body { background-color: #FFFFFF; }\n";
	$content .= "#wrapper { width: 100%; margin: 0px; }\n";
	$content .= "#container { width: 100%; margin: 0px; }\n";
	$content .= "#header { padding-bottom: 0px; }\n";
	$content .= "#content { padding-bottom: 0px; }\n";
	
	$content .= ".body { font-family: Calibri; font-size: 12pt; font-style: normal; font-weight: normal; color: #000000; }\n";
	$content .= ".hd0 { font-family: Calibri; font-size: 14pt; font-style: normal; font-weight: bold; color: #000000; }\n";
	$content .= ".hd1 { font-family: Calibri; font-size: 12pt; font-style: normal; font-weight: bold; color: #000000; }\n";
	$content .= ".hl0 { font-family: Calibri; font-size: 12pt; font-style: normal; font-weight: bold; color: #0d6efd; }\n";
	$content .= ".hl1 { font-family: Calibri; font-size: 12pt; font-style: normal; font-weight: bold; color: #dc3545; }\n";
	$content .= ".hl2 { font-family: Calibri; font-size: 12pt; font-style: normal; font-weight: bold; color: #ffc107; }\n";
	$content .= ".hl3 { font-family: Calibri; font-size: 12pt; font-style: normal; font-weight: bold; color: #198754; }\n";
	$content .= ".signature { font-size: 13pt; font-style: normal; font-weight: bold; color: #000000; }\n";
	$content .= ".small { font-size: 10pt; font-style: nromal; color: #333333; }\n";
	$content .= ".smaller { font-size: 9pt; font-style: normal; color: #333333; }\n";
	$content .= ".link { font-size: 12pt; }\n";
	$content .= "-->\n";
	$content .= "</style>\n";
	$content .= "</head>\n";
	$content .= "<body class=\"body\">\n";
	$content .= "<div id=\"wrapper\">\n";
	$content .= "<div id=\"container\">\n";
	$content .= "<div id=\"header\">\n";
	$content .= "<p>$greeting</p>\n";
	$content .= "</div>\n";
	$content .= "<div id=\"content\">\n";
	$content .= "<p>$contentlines</p>\n";
	$content .= "</div>\n";
	$content .= "<div id=\"footer\" class=\"small\">\n";
	$content .= "<span class=\"signature\">Prevrod - Consultadoria, Lda</span><br>\n";
	$content .= "Rua Joaquim Maria Simões, nº1 Edifico Smartspace<br>\n";
	$content .= "2560-281 TORRES VEDRAS<br>\n";
	$content .= "Telefone: 967 464 161<br>\n";
	$content .= "<a href = \"mailto:e-rod@prevrod.com\">e-rod@prevrod.com</a> | <a href = \"http://www.prevrod.com\">www.prevrod.com</a><br><br>\n";
	$content .= "<img src=\"cid:logo\">\n";
	$content .= "</div>\n";
	$content .= "</div>\n";
	$content .= "</div>\n";
	$content .= "</body>\n";
	
	$content .= "</html>\n";
		
	return $content; 
}
?>