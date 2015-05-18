e<?php class openexperiencesEpubFactory {

	public $chaptersOrArticlesWithTitles=array();
	public $bookTitle="Title of The Book";
	public $bookAuthor="Author of The Book";
	public $language="en";	
	public $destinationFileFullPath=null;
	
	/** 
	 * Copy a file, or recursively copy a folder and its contents 
	 * 
	 * @author      Aidan Lister <aidan@php.net> 
	 * @version     1.0.1 
	 * @param       string   $source    Source path 
	 * @param       string   $dest      Destination path 
	 * @return      bool     Returns TRUE on success, FALSE on failure 
	 */ 
	public function copyr($source, $dest)  { 
		    // Simple copy for a file 
		    if (is_file($source)) {
			//chmod($dest, 777);
			return copy($source, $dest); 
		    } 
		
		    // Make destination directory 
		    if (!is_dir($dest)) { 
			mkdir($dest); 
		    }
		
		    //chmod($dest, 777);
		
		    // Loop through the folder 
		    $dir = dir($source); 
		    while (false !== ($entry = $dir->read())) { 
			// Skip pointers 
			if ($entry == '.' || $entry == '..') { 
			    continue; 
			} 
		
			// Deep copy directories 
			if ($dest !== "$source/$entry") { 
			    $this->copyr("$source/$entry", "$dest/$entry"); 
			} 
		    } 
		
		    // Clean up 
		    $dir->close(); 
		    return true; 
	} 


	function zipFiles($source_arr, $destination, $destinationPathRemovePartOfItStartingFrom="")
	{
		
	    if (is_string($source_arr)) $source_arr = array($source); // convert it to array
	    
	    $zip = new ZipArchive();
	    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
		return false;
	    }
	
	    for ($i=0;$i<count($source_arr);$i++) {
	    	    $zipFilePath=preg_replace('/^\//s', '',str_replace("\\", "/", str_replace($destinationPathRemovePartOfItStartingFrom,"",$source_arr[$i])));
	    	    if (is_file($source_arr[$i])) {
	    	    	    $zip->addFromString($zipFilePath, file_get_contents($source_arr[$i]));
	    	    } else if (is_dir($source_arr[$i])) {
	    	            $zip->addEmptyDir($zipFilePath);	    	    
	    	    }
	    }
	    
	    
	
	    return $zip->close();
	
	}
	
	/* Taken from php.net */
	protected function dirToArray($dir) {
	  
	   $result = array();
	
	   $cdir = scandir($dir);
	   foreach ($cdir as $key => $value)
	   {
	      if (!in_array($value,array(".","..")))
	      {
		 if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
		 {
		    $result[$value] = $this->dirToArray($dir . DIRECTORY_SEPARATOR . $value);
		 }
		 else
		 {
		    $result[] = $value;
		 }
	      }
	   }
	  
	   return $result;
	}
	
	
	/* Taken from php.net and changed */
	protected function dirToArrayOfFullRelativePaths($source, $whatToReturn=true, &$outcomeArray=null) {

		    // $whatToReturn==true or "all" - all file paths with directory paths
		    // $whatToReturn==false or "false" or "files" - all file paths
		    // $whatToReturn=="directories" or "directories" all directory paths
		
		    
		    
		    $thisIsFirstRecurrenceRunning=false;
		    if ($outcomeArray===null) {
		    	    $thisIsFirstRecurrenceRunning=true;
		    	    $outcomeArray=array();
		    }	    
			
		    // Simple copy for a file 
		    if (is_dir($source)) {
			$outcomeArray[]=realpath($source);
		    }
		    
		    if (is_file($source)) {
			//chmod($dest, 777);
			$outcomeArray[]=realpath($source);
			return true; 
		    } 
		
		    // Loop through the folder 
		    $dir = dir($source); 
		    while (false !== ($entry = $dir->read())) { 
			// Skip pointers 
			if ($entry == '.' || $entry == '..') { 
			    continue; 
			} 
		
			// Deep copy directories 
			if ($dest !== "$source/$entry") { 
			    $this->dirToArrayOfFullRelativePaths("$source/$entry", $whatToReturn, $outcomeArray); 
			} 
		    } 
		
		    // Clean up 
		    $dir->close(); 
		    
		    // $whatToReturn==true or "all" - all file paths with directory paths
		    // $whatToReturn==false or "false" or "files" - all file paths
		    // $whatToReturn=="directories" or "directories" all directory paths

					    
		    if ($whatToReturn!==true&&$whatToReturn!=="all") {

			    if ($whatToReturn===false||$whatToReturn==="files") {
					    
			    	    for ($i=0;$i<count($outcomeArray);$i++) {
			    	    	    if (!is_file($outcomeArray[$i])) {
							array_splice($outcomeArray, $i, 1);
							$i=$i-1;
			    	    	    }
			    	    }
			    	    
			    }		    	    
		    	    
			    if ($whatToReturn==="directories") {
			    	    for ($i=0;$i<count($outcomeArray);$i++) {
			    	    	    if (!is_dir($outcomeArray[$i])) {
							array_splice($outcomeArray, $i, 1);
							$i=$i-1;
			    	    	    }
			    	    }
					    
			    }		    	    
		    	    
		    }	    
		    
		    
		    return $outcomeArray; 
	} 
	
	/*taken from php.net */
	protected function removeDirRecursively($dir) {
	
	
		if (is_dir($dir)) {
		     $objects = scandir($dir);
		     foreach ($objects as $object) {
		       if ($object != "." && $object != "..") {
			 if (filetype($dir."/".$object) == "dir") $this->removeDirRecursively($dir."/".$object); else unlink($dir."/".$object);
		       }
		     }
		     reset($objects);
		     rmdir($dir);
		   } 	
	
	
	} 
	
	

	public function output() {
	
		
			//$temporaryNumber=md5(((string) rand(1, getrandmax()))).((string) rand(1, getrandmax())).((string) rand(1, getrandmax()));
			$temporaryNumber=(string) rand(1, getrandmax()).(string) rand(1, getrandmax());
			
			$workingTempPath="workshopEpub/$temporaryNumber/";
			mkdir($workingTempPath); 
			
			
			$this->copyr("epubsourcebase", $workingTempPath);

			$chapter_htmlfileSource=file_get_contents($workingTempPath."/OEBPS/1_chapter.xhtml");
			

			
			$content_opf_fileSource=file_get_contents($workingTempPath."/OEBPS/content.opf");
			$content_opf_entries_1=array();
			$content_opf_entries_2=array();
			
			$content_opf_fileSource=preg_replace("/(<dc:title[^>]*>).*(<\/dc:title>)/sU", "\${1}".htmlentities($this->bookTitle)."\${2}", $content_opf_fileSource);
			$content_opf_fileSource=preg_replace("/(<dc:language[^>]*>).*(<\/dc:language>)/sU", "\${1}".htmlentities($this->language)."\${2}", $content_opf_fileSource);
			$dateNow=date("Y-m-d")."T".date("H:i:s")."Z";
			$content_opf_fileSource=preg_replace("/(<meta property=\"dcterms:modified\">).*(<\/meta>)/sU", "\${1}".$dateNow."\${2}", $content_opf_fileSource);

			
			$toc_ncx_fileSource=file_get_contents($workingTempPath."/OEBPS/toc.ncx");
			$toc_ncx_entries=array();
			
			$toc_ncx_fileSource=preg_replace("/(<docTitle>.*<text>).*(<\/text>.*<\/docTitle>)/sU", "\${1}".$this->bookTitle."\${2}", $toc_ncx_fileSource);
			$toc_ncx_fileSource=preg_replace("/(<docAuthor>.*<text>).*(<\/text>.*<\/docAuthor>)/sU", "\${1}".$this->bookAuthor."\${2}", $toc_ncx_fileSource);
			
			

			$toc_xhtml_fileSource=file_get_contents($workingTempPath."/OEBPS/toc.xhtml");
			$toc_xhtml_fileSource=preg_replace('/(^.*<title>).*(<\/title>)/sU', "\${1}".htmlentities($this->bookTitle)."\${2}", $toc_xhtml_fileSource);

			$toc_xhtml_entries=array();
			
			$chapterCounter=0;
			
			for ($i=0;$i<count($this->chaptersOrArticlesWithTitles);$i++) {
				
				$htmlForChapter=$chapter_htmlfileSource;
				$chapterCounter++;
				//echo "##############".$htmlForChapter; die();
				$htmlForChapter=preg_replace('/(^.*<title>).*(<\/title>)/sU',"\${1}".$this->chaptersOrArticlesWithTitles[$i]['title']."\${2}", $htmlForChapter);
				$htmlForChapter=preg_replace('/(^.*<body[^>]*>).*(<\/body>.*)$/sU',"\${1}".$this->chaptersOrArticlesWithTitles[$i]['contents']."\${2}", $htmlForChapter);
				
				file_put_contents($workingTempPath."/OEBPS/".$chapterCounter."_chapter.xhtml", $htmlForChapter);
				
				
				$content_opf_entries_1[$i]='<item id="chapter_'.$chapterCounter.'" href="'.$chapterCounter.'_chapter.xhtml" media-type="application/xhtml+xml" />';
				$content_opf_entries_2[$i]='<itemref idref="chapter_'.$chapterCounter.'" />';
				
				
				$toc_ncx_entries[$i]='<navPoint class="chapter" id="chapter'.$chapterCounter.'" playOrder="'.$chapterCounter.'">'."\n"
				.'<navLabel><text>'.$this->chaptersOrArticlesWithTitles[$i]['title'].'</text></navLabel>'."\n"
				.'<content src="'.$chapterCounter.'_chapter.xhtml"/>'."\n"
				.'</navPoint>';				
				
				$toc_xhtml_entries[$i]='<li id="chapter'.$chapterCounter.'"><a href="'.$chapterCounter.'_chapter.xhtml">'.$this->chaptersOrArticlesWithTitles[$i]['title'].'</a></li>';
				
			} 


			$content_opf_fileSource=preg_replace("/(<manifest>.*)(<item id=\"chapter_1\" href=\"1_chapter\.xhtml\" media-type=\"application\/xhtml\+xml\" \/>)(.*<\/manifest>)/sU", "\${1}".implode("\n", $content_opf_entries_1)."\${3}", $content_opf_fileSource);
			$content_opf_fileSource=preg_replace("/(<spine toc=\"ncx\">.*)(<itemref idref=\"chapter_1\" \/>)(.*<\/spine>)/sU", "\${1}".implode("\n", $content_opf_entries_2)."\${3}", $content_opf_fileSource);
			$toc_ncx_fileSource=preg_replace("/(<navMap>.*)(<navPoint class=\"chapter\" id=\"chapter1\" playOrder=\"1\">.*<\/navPoint>)(.*<\/navMap>)/sU", "\${1}".implode("\n", $toc_ncx_entries)."\${3}", $toc_ncx_fileSource); 
			$toc_xhtml_fileSource=preg_replace("/(<nav epub:type=\"toc\" id=\"toc\">.*<ol>.*)(<li id=\"chapter1\"><a href=\"1_chapter\.xhtml\">.*<\/a><\/li>)(.*<\/ol>.*<\/nav>)/sU", "\${1}".implode("\n", $toc_xhtml_entries)."\${3}", $toc_xhtml_fileSource); 
			
			
			file_put_contents($workingTempPath."/OEBPS/content.opf", $content_opf_fileSource);
			file_put_contents($workingTempPath."/OEBPS/toc.ncx", $toc_ncx_fileSource);
			file_put_contents($workingTempPath."/OEBPS/toc.xhtml", $toc_xhtml_fileSource);
			
							
		
			$filePathsToZip=$this->dirToArrayOfFullRelativePaths($workingTempPath, "all");
			
			for($i=0;$i<count($filePathsToZip);$i++) {

				//$filePathsToZip[$i]=str_replace(realpath($workingTempPath),"", $filePathsToZip[$i]);
				
				
				if (preg_match("/~$/", $filePathsToZip[$i])>0) {
					array_splice($filePathsToZip, $i, 1);
					$i--;
				}	
			}
			
			
			
			if ($this->destinationFileFullPath!==null) {
				$this->zipFiles($filePathsToZip, $this->destinationFileFullPath, realpath($workingTempPath));
			} else {
				echo "couldn't have output the epub into a epub file because there had been no file path set up on neither via constructor of the object nor later via the special method of this object";
			}
			
			
			$this->removeDirRecursively($workingTempPath);
			
	
	}	
	
	
	
	
	function __construct($bookTitle=null, $chaptersOrArticlesArray=null, $titlesOfTheChapters=null, $destinationFileFullPath=null, $language="en", $bookAuthor="Author of The Book") {
			
			$this->bookTitle=$bookTitle;
			$this->language=$language;
			$this->bookAuthor=$bookAuthor;
			$this->destinationFileFullPath=$destinationFileFullPath;
			
			$produceOutputOnConstruct=false;
			if (is_string($bookTitle)&&is_array($chaptersOrArticlesArray)&&is_string($destinationFileFullPath)) $produceOutputOnConstruct=true;
		
			if (is_array($chaptersOrArticlesArray)) {
				for ($i=0;$i<count($chaptersOrArticlesArray);$i++) {
					$this->chaptersOrArticlesWithTitles[$i]=array();
					$this->chaptersOrArticlesWithTitles[$i]['contents']=$chaptersOrArticlesArray[$i];
					if (is_array($titlesOfTheChapters)&&isset($titlesOfTheChapters[$i])) {
						$this->chaptersOrArticlesWithTitles[$i]['title']=$titlesOfTheChapters[$i];
					} else {
						$this->chaptersOrArticlesWithTitles[$i]['title']="t/".$i;
					}
					
				}	
			}	
		
		
			if (!is_dir("workshopEpub")) {mkdir("workshopEpub");}
			
			if ($produceOutputOnConstruct===true) {
				$this->output();
			}
			
	
	}



}




?>