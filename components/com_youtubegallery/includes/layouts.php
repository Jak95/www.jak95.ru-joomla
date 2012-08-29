<?php
/*
 [galleryname]
 
 
 
*/



class YoutubeGalleryLayouts
{
	function getTableClassic(&$row, $shadowbox_activated)
	{
		
		
		$result='';
		
		if($row->showgalleryname==1)
		{
			if($row->gallerynamestyle!='')
				$result.='<p style="'.$row->gallerynamestyle.'">[galleryname]</p>';
			else
				$result.='<h3>[galleryname]</h3>';
		}
		
		if($row->description==1 and $row->descr_position==0 )
		{
			
			$result.='[if:videodescription]';
			
				if($row->descr_style!='')
					$result.='<p style="'.$row->descr_style.'">[videodescription]</p>';
				else
					$result.='<h4>[videodescription]</h4>';
			
			$result.='[endif:videodescription]';
		}
		
		$result.='[videoplayer]';
		
		if($row->showactivevideotitle==1)
		{
			$result.='[if:videotitle]';
			
				if($row->activevideotitlestyle!='')
					$result.='<p style="'.$row->activevideotitlestyle.'">[videotitle]</p>';
				else
					$result.='<h3>[videotitle]</h3>';
		
			$result.='[endif:videotitle]';
			
		}	
	
		if($row->description==1 and $row->descr_position==1 )
		{
			$result.='[if:videodescription]';
			
				if($row->descr_style!='')
					$result.='<p style="'.$row->descr_style.'">[videodescription]</p>';
				else
					$result.='<h4>[videodescription]</h4>';
			
			$result.='[endif:videodescription]';
		}

		if(!$shadowbox_activated)
		{
		
			//pagination 1 - on top only
			//pagination 2 - on bottom only
			//pagination 3 - both
		
			if($row->pagination==1 or $row->pagination==3 )
				$result.='[pagination]';

	
			$result.='
				[if:count]
							
							<hr '.($row->linestyle!='' ? ' style="'.$row->linestyle.'" ' : '').' />
							[navigationbar:classictable,simple]
						
				[endif:count]
			';
		
			if($row->pagination==2 or $row->pagination==3)
				$result.='[pagination]';
		}	
			
		
		return $result;
	}
}