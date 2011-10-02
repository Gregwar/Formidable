<?php

namespace Gregwar\DSD\Captcha;

/**
 * Génération d'un Captcha
 *
 * @author Grégoire Passault <g.passault@gmail.com>
 */
class Captcha
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Retourne le code de l'image en HTML qu'on peut embeder
     */
    public function inline()
    {
        return 'data:image/png;base64,'.base64_encode($this->raw());
    }

    /**
     * Retourne le contenu de l'image brut
     */
    public function raw()
    {
        $i = imagecreatetruecolor(120,40);
        $back = imagecolorallocate($i, 255, 255, 255);
        $col = imagecolorallocate($i, mt_rand(0,110), mt_rand(0,110), mt_rand(0,110));
        imagefill($i, 0, 0, $back);

        // Lignes aléatoire
        for ($t=0; $t<10; $t++) {
            $tcol = imagecolorallocate($i, 100+mt_rand(0,150), 100+mt_rand(0,150), 100+mt_rand(0,150));
            $Xa = mt_rand(0, 120);
            $Ya = mt_rand(0, 40);
            $Xb = mt_rand(0, 120);
            $Yb = mt_rand(0, 40);
            imageline($i, $Xa, $Ya, $Xb, $Yb, $tcol);
        }

        // Code
        imagettftext($i, 28, 0, 5, 32, $col, __DIR__.'/Fonts/captcha.ttf', $this->value);

        // Brouillage
        $X = mt_rand(0, 120);
        $Y = mt_rand(0, 40);
        $Phase=mt_rand(0,10);
        $Scale = 1.3 + mt_rand(0,10000)/30000;
        $Amp=1+mt_rand(0,1000)/1000;

        $out = imagecreatetruecolor(120,40);
        for ($x=0; $x<120; $x++) {
            for ($y=0; $y<40; $y++) {
                $Vx=$x-$X;
                $Vy=$y-$Y;
                $Vn=sqrt($Vx*$Vx+$Vy*$Vy);
                if ($Vn!=0) {
                    $Vn2=$Vn+4*sin($Vn/8);
                    $nX=$X+($Vx*$Vn2/$Vn);
                    $nY=$Y+($Vy*$Vn2/$Vn);
                } else {
                    $nX=$X;
                    $nY=$Y;
                }
                $nY = $nY+$Scale*sin($Phase + $nX*0.2);
                $p = $this->bilinearInterpolate($nX-floor($nX), $nY-floor($nY), 
                $this->getCol($i,floor($nX),floor($nY)),
                $this->getCol($i,ceil($nX),floor($nY)),
                $this->getCol($i,floor($nX),ceil($nY)),
                $this->getCol($i,ceil($nX),ceil($nY)));

                if ($p==0) $p=0xFFFFFF;
                imagesetpixel($out, $x, $y, $p);
            }
        }

        ob_start();
        imagejpeg($out, null, 10);
        return ob_get_clean();
    }

    protected function bilinearInterpolate($x, $y, $nw, $ne, $sw, $se) {
        $r0 = (int)($nw >> 16) & 0xff;
        $g0 = (int)($nw >>  8) & 0xff;
        $b0 = (int)($nw      ) & 0xff;
        $r1 = (int)($ne >> 16) & 0xff;
        $g1 = (int)($ne >>  8) & 0xff;
        $b1 = (int)($ne      ) & 0xff;
        $r2 = (int)($sw >> 16) & 0xff;
        $g2 = (int)($sw >>  8) & 0xff;
        $b2 = (int)($sw      ) & 0xff;
        $r3 = (int)($se >> 16) & 0xff;
        $g3 = (int)($se >>  8) & 0xff;
        $b3 = (int)($se      ) & 0xff;

        $cx = 1.0 - $x;
        $cy = 1.0 - $y;

        $m0 = $cx * $r0 + $x * $r1;
        $m1 = $cx * $r2 + $x * $r3;
        $r = (int)($cy * $m0 + $y * $m1);

        $m0 = $cx * $g0 + $x * $g1;
        $m1 = $cx * $g2 + $x * $g3;
        $g = (int)($cy * $m0 + $y * $m1);

        $m0 = $cx * $b0 + $x * $b1;
        $m1 = $cx * $b2 + $x * $b3;
        $b = (int)($cy * $m0 + $y * $m1);

        return ($r << 16) | ($g << 8) | $b;
    }

    protected function getCol($image, $x, $y)
    {
        $L = imagesx($image);
        $H = imagesy($image);
        if ($x<0 || $x>=$L || $y<0 || $y>=$H)
            return 0xFFFFFF;
        else return imagecolorat($image, $x, $y);
    }
}
