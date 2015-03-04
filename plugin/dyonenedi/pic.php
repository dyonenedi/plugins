<?php
	namespace App\Plugin\Dyonenedi;
	
	Class Pic 
	{
		public $errorMessage;
		public $callback = true;

		######################################################################################
		################################# UPLOAD IMAGE #######################################
		######################################################################################
		
		private $picture;
		private $pictureName;
		private $nameTarg;
		private $pathTarg;
		private $file;
		// Em MB
		private $maxSize = 3;
		private $ext  = ['jpeg' => 'image/jpeg', 'jpg' => 'image/jpg', 'png' => 'image/png', 'gif' => 'image/gif'];
		private $char = 255;
		private $minWidth = 750;
		private $minHeight = 750;

		public function setMaxSize($maxSize){
			$this->maxSize = $maxSize;
		}

		public function setExtension($ext){
			$this->ext = $ext;
		}

		public function setMaxChar($ext=false){
			$this->char = $char;
		}

		public function setMinWidth($w){
			$this->minWidth = $w;
		}

		public function setMinHeight($h){
			$this->minHeight = $h;
		}

		public function setFile($file){
			$this->file = $file;
		}

		public function setPath($pathTarg){
			if (!is_dir($pathTarg)) {
				if (mkdir($pathTarg, 0775, true)) {
					$this->pathTarg = $pathTarg;
				} else {
					$this->callback = false;
					$this->errorMessage = 'Não foi possível criar o diretório especificado: '.$pathTarg;
				}
			} else {
				$this->pathTarg = $pathTarg;
			}

			$this->pathTarg = (substr($this->pathTarg, -1) == '/') ? $this->pathTarg : $this->pathTarg.'/';
		}

		public function setPicName($name){
			$this->nameTarg = $name;
		}

		public function save($ext=false) {
			if (empty($this->pathTarg)) {
				if (empty($this->errorMessage)) {
					$this->errorMessage = 'A pasta de destino é obrigatória';
				}
				$this->callback = false;
				return false;
			}

			ini_set('memory_limit', '100M');
			
			if (!empty($this->file)) {
				if (is_uploaded_file($this->file['tmp_name'])) {
					if (strlen(utf8_decode($this->file['name'])) <= $this->char) {
						if ((($this->file['size'] / 1024) / 1024) <= $this->maxSize) {
						    $property = getimagesize($this->file['tmp_name']);
						    if ($property[0] >= $this->minWidth && $property[1] >= $this->minHeight) {
						    	if (false === $ex = array_search($this->file['type'], $this->ext, true)) {
							        $this->callback = false;
									$this->errorMessage = 'Extensão do arquivo inválida';
							    } else {
							    	if (empty($this->nameTarg)) {
							    		$this->nameTarg = md5(uniqid(rand(), true));
							    	}
							    	$ext = ($ext) ? $ext : $ex;
							    	$this->pictureName = $this->nameTarg.'.'.$ext;
							    	$this->picture = $this->pathTarg.$this->pictureName;

							    	if (!move_uploaded_file($this->file['tmp_name'], $this->picture)) {
							    		$this->callback = false;
										$this->errorMessage = 'Erro ao mover o arquivo';
							    	}
							    }
						    } else {
						    	$this->callback = false;
								$this->errorMessage = 'A largura mínima permitida é: '.$this->minWidth.'px e a altura mínima é '.$this->minHeight.'px';
						    }
						} else {
							$this->callback = false;
							$this->errorMessage = 'O arquivo tem mais de '.$this->maxSize.'MB';
						}
					} else {
						$this->callback = false;
						$this->errorMessage = 'O nome do arquivo tem mais de '.$this->char.' caracteres: '.$this->file['name'];
					}
				} else {
					$this->callback = false;
					$this->errorMessage = 'O arquivo ou imagem não pode ser subida no servidor: '.$this->file['name'];
				}
			} else {
				$this->callback = false;
				$this->errorMessage = 'O arquivo ou imagem não existe';
			}
		}

		public function getPicture(){
			return $this->picture;
		}

		public function getPicName(){
			return $this->pictureName;
		}

		######################################################################################
		################################### CROP IMAGE #######################################
		######################################################################################

		private $cropPathSrc;
		private $cropPathTarg;
		private $cropFile;

		private $x  = 150;
		private $y  = 150;
		private $w  = 150;
		private $h  = 150;
		private $quality = 90;
		private $width   = 500;
		private $height  = 500;

		public function setCropPathSrc($pathSrc) {
			$this->cropPathSrc = $pathSrc;
			$this->cropPathSrc = (substr($this->cropPathSrc, -1) == '/') ? $this->cropPathSrc : $this->cropPathSrc.'/';
		}

		public function setCropPathTarget($pathTarg) {
			if (!is_dir($pathTarg)) {
				if (mkdir($pathTarg, 0775, true)) {
					$this->cropPathTarg = $pathTarg;
				} else {
					$this->callback = false;
					$this->errorMessage = 'Não foi possível criar o diretório especificado: '.$pathTarg;
				}
			} else {
				$this->cropPathTarg = $pathTarg;
			}
			
			$this->cropPathTarg = (substr($this->cropPathTarg, -1) == '/') ? $this->cropPathTarg : $this->cropPathTarg.'/';
		}

		public function setCropFile($file) {
			$this->cropFile = $file;
		}

		public function setPosition($x, $y, $w, $h) {
			$this->x = $x;
			$this->y = $y;
			$this->w = $w;
			$this->h = $h;
		}

		public function setQuality($quality) {
			$this->quality = $quality;
		}

		public function setWidthHeight($width, $height) {
			$this->width  = $width;
			$this->height = $height;
		}

		public function crop() {
			if (file_exists($this->cropPathSrc.$this->cropFile)) {
				$img_r = imagecreatefromjpeg($this->cropPathSrc.$this->cropFile);
				$dst_r = ImageCreateTrueColor($this->width, $this->height);

				if (imagecopyresampled($dst_r, $img_r, 0, 0, $this->x, $this->y, $this->width, $this->height, $this->w, $this->h)) {
					if (!imagejpeg($dst_r, $this->cropPathTarg.$this->cropFile, $this->quality)) {
						$this->callback = false;
						$this->errorMessage = 'Não foi possível criar a imagem no diretório especificado: '.$this->cropPathTarg.$this->cropFile;
					}
				} else {
					$this->callback = false;
					$this->errorMessage = 'Não foi possível criar a imagem especificada.';
				}
			} else {
				$this->callback = false;
				$this->errorMessage = 'O arquivo especificado não existe: '.$this->cropPathSrc.$this->cropFile;
			}
		}
	
		######################################################################################
		################################### AUX IMAGE #######################################
		######################################################################################

		public function resize($pictureSrc, $pictureTarg, $newWidth, $newHeight=false){
			$path_parts = pathinfo($pictureTarg);
			if (!file_exists($path_parts['dirname'])) {
				mkdir($path_parts['dirname'], 0775, true);
			}

			$img    = imagecreatefromjpeg($pictureSrc);
			$width  = imagesx ($img);
			$height = imagesy ($img);
			if ($newHeight) {
				$w = $newWidth;
				$h = $newHeight;
			} else {
				$w = $newWidth;
				$h = ($height*$w) / $width;
			}

			$newImg	 = imagecreatetruecolor($w, $h);
			imagecopyresized($newImg, $img, 0, 0, 0, 0, $w, $h, $width, $height);
			if (!imagejpeg($newImg, $pictureTarg, $this->quality)) {
				$this->callback = false;
				$this->errorMessage = 'Não foi possível redimencionar sua imagem';
			}
			
			imagedestroy($newImg);
			imagedestroy($img);	
		}

		public function organizeArray($file){
			if (is_array($file['name'])) {
				foreach ($file as $key => $value) {
					foreach ($value as $k => $val) {
						$file[$k][$key] = $val;
						unset($file[$key][$k]);
					}
					unset($file[$key]);
				}
			} else {
				$file[0] = $file;
			}

			return $file;
		}

		public function deleteFile($file,$path=false) {
			if (!$path) {
				$path = $this->pathTarg;
			}

			if (file_exists($path.$file)) {
				unlink($path.$file);
			} else {
				$this->callback = false;
				$this->errorMessage = 'O arquivo especificado não existe: '.$path.$file;
			}
		}

		public function clearDir($path=false) {
			$path = ($path) ? $path: $this->pathTarg;

			if (file_exists($path)) {
				$d = dir($path);
				while (false !== ($file = $d->read())) {
				   if ($file != '.' && $file != '..') {
				   		unlink($path.$file);
				   }
				}
				$d->close();
			}
		}

		public function removeDir($dir=false) {
			$dir = ($dir) ? $dir: $this->pathTarg;

		    if (!file_exists($dir)) {
		        return true;
		    }

		    if (!is_dir($dir)) {
		        return true;
		    }

		    foreach (scandir($dir) as $item) {
		        if ($item == '.' || $item == '..') {
		            continue;
		        }

		        unlink($dir.DIRECTORY_SEPARATOR.$item);
		    }

		    rmdir($dir);
		}
	}