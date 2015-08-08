<?php
  namespace Plugin\Dyonenedi;

  class ImageFill
  {
      public $quality = 100;
      public $color = [255, 255, 255];

      public function execute($target_file, $save_file) {
        // Pega altura e largura
        list($width, $height, $type, $attr) = getimagesize($target_file);
        
		if ($height > $width) {
			$new_width = $height;
			$new_height = $height;
		} else if ($height < $width) {
			$new_width = $width;
			$new_height = $width;
		} else {
			$new_width = $width;
			$new_height = $height;
		}

		// Cria imagem de destino temporária
		$img_dst	= imagecreatetruecolor($new_width, $new_height);
		// Adiciona cor de fundo branca à nova imagem
		$background = imagecolorallocate($img_dst, $this->color[0], $this->color[1], $this->color[2]);
		imagefill($img_dst, 0, 0, $background );

		// Seta o x e y da imagem de destino
		if ($width < $new_width) {
			$dst_x = (($new_width - $width) / 2);
			$dst_y = 0;
			$new_width = $new_width - ($dst_x * 2);
		} else if ($height < $new_height) {
			$dst_x = 0;
			$dst_y = (($new_height - $height) / 2);
			$new_height = $new_height - ($dst_y * 2);
		}	else {
			$dst_x = 0;
			$dst_y = 0;
		}

		// Seta o x e y da imagem de origem
		$src_x = 0;
		$src_y = 0;

        // Cria a imagem de origem
        $img_src = imagecreatefromjpeg($target_file);

        // Faz uma nova imagem destino baseado na imagem origem
		imagecopyresampled($img_dst, $img_src, $dst_x, $dst_y, $src_x, $src_y, $new_width, $new_height, $width, $height);

        // Salva imagem destino
		imagejpeg($img_dst, $save_file, $this->quality);
      }
  }
