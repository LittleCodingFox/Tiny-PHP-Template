<?php
	include "../lib/template.php";
	
	$template = new Template("test.tpl.php");
	
	$template->SetValue("pagetitle", "Bootstrap Blog Template");
	$template->SetValue("blogtitle", "My awesome blog");
	
	$content = $template->GetValue("blogpost");
	
	$replacement = "";
	
	for($i = 0; $i < 3; $i++)
	{
		$replacement = $replacement . $content;
	}
	
	$template->SetValue("blogpost", $replacement);

	$template->SetValue("blogsubtitle", "Isn't it awesome?");
	$template->SetValue("blogposttitle", "This is a Template sample!");
	$template->SetValue("blogpostcontent", "Such an awesome template!");

	//2nd post
	$template->SetValue("blogposttitle", "This is a Template sample, again!");
	$template->SetValue("blogpostcontent", "It's pretty cool!");

	//3rd post
	$template->SetValue("blogposttitle", "Still awesome!");
	$template->SetValue("blogpostcontent", "Too bad we have to replace things separately!");
	
	$template->Publish();
?>
