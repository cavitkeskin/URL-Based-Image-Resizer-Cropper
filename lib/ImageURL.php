<?	
	/*
		Author: Cavit Keskin
				
		
	*/


interface IImageURL
{
	public function __construct($url);
	public function loadImage($filename);
	public function execScript($script);
	public function get($autosave=true);
	public function save($filename=null);
}

abstract class AImageURL implements IImageURL
{
	
	protected $FileName;
	protected $Canvas = null;
	protected $ProtectedArea = array( 'x1'=>50, 'y1'=>50, 'x2'=>50, 'y2'=>50 );	
	
		
	public function __construct($url)
	{
		error_log( 'ImageURL Class called for ' . $url );
		$ar=explode('@', $url);
		if( $this->loadImage( array_shift($ar) ))
			while($script=array_shift($ar)) $this->execScript($script);
	}
	
	protected function fileExists($file)
	{
		return file_exists($file);
	}	
	
	public function loadImage($file)
	{

		$this->FileName = '';	
		if( $this->Canvas ) ImageDestroy( $this->Canvas );
		$this->Canvas = null;
		
		if( ! $this->fileExists($file) )
		{
			error_log( '[ImageURL Class] file not found: ' . $file );
			return false;
		} 
		
		$ext = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) ); 	

		switch($ext)
		{
			case 'jpg':
				$this->Canvas = @imagecreatefromjpeg( $file );
				break;
			case 'png':
				$this->Canvas = @imagecreatefrompng( $file );
				break;
		}

		if( $this->Canvas ) $this->FileName = $file;
		return $this->Canvas;	
	}	
	
	public function execScript($script)
	{
		$key=explode(',', $script);
		switch($key[0])
		{
			case 'protect': 
				$this->protect($key[1], $key[2], $key[3], $key[4]); 
				break;
			case 'resize': 
				$this->resize($key[1], $key[2]); 
				break;
			case 'area': 
				$this->area($key[1], $key[2]); 
				break;
			case 'crop': 
				$this->crop($key[1], $key[2]); 
				break;
		}
	}
	
	
	protected function width()
	{ 
		return imageSX($this->Canvas); 
	}
	
	protected function height()
	{ 
		return imageSY($this->Canvas); 
	}

	private function protect($x1, $y1, $x2, $y2)
	{
		$x1*=1; $x2*=1; $y1*=1; $y2*=1;

		if( $x1>$x2 || $y1>$y2 ) return false;

		$this->ProtectedArea = array( 'x1' => $x1, 'y1' => $y1, 'x2' => $x2, 'y2' => $y2 );
		$this->FileName .= sprintf('@protect,%d,%d,%d,%d', $x1, $y1, $x2, $y2 );
		
		return true;
	}

	function resize($w, $h)
	{
		if(! $this->Canvas ) return false;
		
		$im = imagecreatetruecolor( $w, $h );
		$success = imagecopyresampled( $im, $this->Canvas, 0, 0, 0, 0, $w, $h, $this->Width(), $this->Height());
		if( $success )
		{
			imagedestroy( $this->Canvas );
			$this->Canvas = $im;
			$this->FileName .= sprintf( '@resize,%d,%d', $w, $h );
			return true;
		} 
		else
			return false;
	}
	
	private function area( $w, $h )
	{
		if(! $this->Canvas ) return false;
	
		$W = $this->width(); 
		$H = $this->height();		

		if( (float)( $W / $H ) > (float)( $w / $h ))
		{
			$G = $w;
			$Y = round($H*$w/$W);
		} 
		else 
		{
			$Y = $h;
			$G = round($W*$h/$H);
		}
		
		$im = imagecreatetruecolor( $G, $Y );
		$success = imagecopyresampled( $im, $this->Canvas, 0, 0, 0, 0, $G, $Y, $this->Width(), $this->Height());
		if( $success )
		{
			imagedestroy( $this->Canvas );
			$this->Canvas = $im;
			$this->FileName .= sprintf( '@area,%d,%d', $w, $h );
			return true;
		} 
		else
			return false;

	}

	private function crop($width, $height)
	{
		if(! $this->Canvas ) return false;

		$W = $this->width(); 
		$H = $this->height();
		$kx = $W / $width;
		$ky = $H / $height;
		$k = ($kx<$ky) ? $kx : $ky;
		$w = floor( $width * $k ); 
		$h = floor( $height * $k );

		$dw = $W - $w;
		$dh = $H - $h;
		
		$p = $this->ProtectedArea;

		$x = $dw * ( $p['x1'] + 100 - $p['x2'] ) / 100 * $p['x1'] / 100;
		$y = $dh * ( $p['y1'] + 100 - $p['y2'] ) / 100 * $p['y1'] / 100;

		$x = $dw * $p['x1'] / ( 100 + $p['x1'] - $p['x2'] );
		$y = $dh * $p['y1'] / ( 100 + $p['y1'] - $p['y2'] );

		$im=imagecreatetruecolor( $width, $height );
		
		$success = imagecopyresampled( $im, $this->Canvas, 0, 0, $x, $y, $width, $height, $w, $h );
		if( $success )
		{
			imagedestroy( $this->Canvas );
			$this->Canvas = $im;
			$this->FileName .= sprintf( '@crop,%d,%d', $width, $height );
			return true;
		} 
		else
			return false;
	
	}
	
	public function save($filename=null)
	{
		if(! $this->Canvas ) return false;
		
		$file = $filename ? $filename : $this->FileName;
		if($file)
			return imagejpeg( $this->Canvas, $file, 90);
		else 
			return false;
		
	}
	
	public function get($autosave=true)
	{
		if( $this->Canvas )
		{
			header('Content-type: image/jpg');
			header('filename: '.$this->FileName);
			if( $autosave && $this->trustedURL() ) $this->save();
			imagejpeg($this->Canvas, null, 90);
		} 
		else
		{
			header("HTTP/1.0 404 Not Found");
		}
	}
	
	protected function trustedURL()
	{
		return false;
	}
} 

class ImageURL extends AImageURL 
{

	protected $ExpectedSizes = array('90x60', '120x80', '160x90');

	protected function trustedURL()
	{
		$s = sprintf('%dx%d', $this->width(), $this->height());
		return in_array( $s, $this->ExpectedSizes );
	}	

}


		


?>