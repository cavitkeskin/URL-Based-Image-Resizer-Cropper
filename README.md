URL / Script Based Image Resizer, Cropper


You can use like below

	$img = new ImageURL('my-coffe-cup.jpg');
	$img->execScript('crop,160,90');
	$img->save('my-small-sized-coffee-cup.jpg');
	
or 

	$img = new ImageURL('my-coffe-cup.jpg@crop,160,90');
	$img->save();  
		
or (it is my first reason for writing this class)

	put .htaccess file under /images folder your web site and make sure about RewriteRule URL is correct.

	and try to reach blow url 
		
		/images/my-coffe-cup.jpg@crop,160,90
	
	

Have fun


Licensed under The MIT License


Cavit Keskin
