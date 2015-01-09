<?php
	/*!
	*	Copyright (c) 2015 Nuno Silva (little.coding.fox@gmail.com)
	*	
	*	This software is provided 'as-is', without any express or implied
	*	warranty. In no event will the authors be held liable for any damages
	*	arising from the use of this software.
	*	
	*	Permission is granted to anyone to use this software for any purpose,
	*	including commercial applications, and to alter it and redistribute it
	*	freely, subject to the following restrictions:
	*	
	*	1. The origin of this software must not be misrepresented; you must not
	*	   claim that you wrote the original software. If you use this software
	*	   in a product, an acknowledgement in the product documentation would be
	*	   appreciated but is not required.
	*	2. Altered source versions must be plainly marked as such, and must not be
	*	   misrepresented as being the original software.
	*	3. This notice may not be removed or altered from any source distribution.
	*/
	class Template
	{
		private $contents = "";
		
		function __construct($filename)
		{
			if(strpos($filename, "://") !== FALSE)
			{
				$curlSession = curl_init();
				curl_setopt($curlSession, CURLOPT_URL, $filename);
				curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
				curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);

				$this->contents = curl_exec($curlSession);
				curl_close($curlSession);
			}
			else
			{
				$this->contents = file_get_contents($filename);
			}
		}
		
		public function GetValue($name)
		{
			$pos = strpos($this->contents, "{begin" . $name . "}");
				
			if($pos === FALSE)
				return FALSE;
			
			$endpos = strpos($this->contents, "{end" . $name . "}", $pos);
			
			if($endpos === FALSE)
				return FALSE;
				
			$pos = $pos + strlen("{begin" . $name . "}");
			
			return substr($this->contents, $pos, $endpos - $pos);
		}
		
		public function SetValue($name, $content)
		{
			$pos = strpos($this->contents, "{begin" . $name . "}");
				
			if($pos === FALSE)
			{
				$pos = strpos($this->contents, "{" . $name . "}");
				
				if($pos === FALSE)
					return FALSE;
					
				$this->contents = substr($this->contents, 0, $pos) . $content . substr($this->contents, $pos + strlen("{" . $name . "}"));
				
				return TRUE;
			}
			
			$endpos = strpos($this->contents, "{end" . $name . "}", $pos);
			
			if($endpos === FALSE)
				return FALSE;

			$this->contents = substr($this->contents, 0, $pos) . $content . substr($this->contents, $endpos + strlen("{end" . $name . "}"));
				
			return TRUE;
		}
		
		private function Cleanup()
		{
			for(;;)
			{
				$pos = strpos($this->contents, "{");
				
				if($pos === FALSE)
					break;
				
				$endpos = strpos($this->contents, "}", $pos);
				
				if($endpos === FALSE)
					break;
					
				//Add the }
				$endpos++;
				
				$name = substr($this->contents, $pos + 1, $endpos - $pos - 1);
				
				$isSection = strpos($name, "begin") == 0;
				
				if($isSection)
				{
					$name = substr($name, strlen("begin"));
				}

				$this->SetValue($name, "");
				
				$newpos = strpos($this->contents, "{");
				
				if($newpos === $pos)
				{
					$this->contents = substr($this->contents, 0, $pos) . substr($this->contents, $endpos);
				}
			}
		}
		
		public function Publish()
		{
			$this->Cleanup();
			
			eval("?>" . $this->contents);
		}
	}
?>
