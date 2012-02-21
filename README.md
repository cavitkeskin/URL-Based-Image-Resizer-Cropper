URL / Script Based Image Resizer, Cropper
=========================================

You can use like below

	$img = new ImageURL('my-coffe-cup.jpg');
	$img->apply('crop,160,90');
	$img->save('my-small-sized-coffee-cup.jpg');
	
**or** 

	$img = new ImageURL('my-coffe-cup.jpg@crop,160,90');
	$img->save();  
		
**or**  _( it is my first reason for writing this class )_
	
put .htaccess file under /images folder your web site and make sure about RewriteRule URL is correct then try to reach blow url 
		
	/images/my-coffe-cup.jpg@crop,160,90
	

Possibles Scripts
----------------
- area,240,180
- protect,40,30,70,60 
- crop,160,160
- resize,160,90
 

you can use below url to reach image 
	
	/images/my-coffee-cup.jpg@protect,40,30,70,60@crop,160,160


Have fun


Licensed under The MIT License


Cavit Keskin
