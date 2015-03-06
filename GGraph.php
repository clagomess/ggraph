<?php
/**
 * GGraph
 *
 * @author Claudio Gomes <cla.gomess@gmail.com>
 * @version 1.0
 * @copyright None
 * @since 06/03/2015
 * @link https://github.com/clagomess/ggraph
 */

class GGraph {
    private $tamanho; // Tamanho da imagem
    private $margem = 10; // Margem de trabalho do grafico
    private $image; // Handle
    private $vetor; // Dados do grafico
    private $gtamanho; // Tamanho do grafico
    private $columSize; // Tamalho das coluna

    // Opções
    public $opMostrarPontoValor = true;
    public $opTransparente = true;

    function __construct($tamanho = 500, $vetor = array()) {
        $this->vetor = $vetor;
        $this->tamanho = $tamanho;
        $this->gtamanho = ($this->tamanho - 350);

        $this->image = imagecreatetruecolor($this->tamanho, ($this->tamanho - 260));
        imagefilledrectangle($this->image, 0, 0, $this->tamanho, ($this->tamanho - 260), imagecolorallocate($this->image, 255, 255, 255));
    }

    // Criar uma cor ou pega uma cor na paleta existente
    private function getColor($id = null, $r = 0, $g = 0, $b = 0, $alpha = 0) {
        $paleta = array(
            array('r' => 200, 'g' => 200, 'b' => 200),
            array('r' => 51, 'g' => 102, 'b' => 204),
            array('r' => 220, 'g' => 57, 'b' => 18),
            array('r' => 255, 'g' => 153, 'b' => 0),
            array('r' => 16, 'g' => 150, 'b' => 24),
            array('r' => 153, 'g' => 0, 'b' => 153),
            array('r' => 0, 'g' => 153, 'b' => 198),
            array('r' => 221, 'g' => 68, 'b' => 119),
            array('r' => 102, 'g' => 170, 'b' => 0)
        );

        if(!$this->opTransparente){
            $alpha = 0;
        }

        if ($id || $id == 0) {
            return imagecolorallocatealpha($this->image, $paleta[$id]['r'], $paleta[$id]['g'], $paleta[$id]['b'], $alpha);
        } else {
            return imagecolorallocatealpha($this->image, $r, $g, $b, $alpha);
        }
    }

    // Cria um ponto na vertice da linha
    private function desenhaPonto($x, $y, $color) {
        imagefilledpolygon($this->image, array(
            ($x - 3), ($y - 6),
            ($x - 3), ($y - 1),
            ($x + 3), ($y - 1),
            ($x + 3), ($y - 6),
        ), 4, $color);
    }

    // Cria descrição da cor da linha referente no grafico
    private function desenhaLabel($text, $color, $y) {
        $this->desenhaPonto(($this->gtamanho + ($this->margem * 5)), ($y + $this->margem), $color);
        imagestring($this->image, 3, ($this->gtamanho + ($this->margem * 6)), $y, utf8_decode($text), null);
    }

    // Desenha linha simples
    private function desenhaLinha($vetor, $cor, $maxValue) {
        $x = $this->margem;
        $y = $this->gtamanho + $this->margem;

        foreach ($vetor as $referencia => $value) {
            $val = ($this->gtamanho + ($this->margem * 1)) - (floor((($this->gtamanho) * $value) / $maxValue));

            imageline($this->image, $x, $y, ($x + $this->columSize), $val, $cor);
            $this->desenhaPonto(($x + $this->columSize), $val, $cor);
            if($this->opMostrarPontoValor) {
                imagestring($this->image, 2, ($x + ($this->columSize + $this->margem)), ($val - $this->margem), utf8_decode($value), null);
            }

            $y = $val;
            $x += $this->columSize;
        }
    }

    // Desenha base do gráfico
    private function desenhaBase() {
        $maxValue = 0;

        $arReferencia = array();

        foreach ($this->vetor as $item) {
            foreach ($item as $ref => $value) {
                if ($value > $maxValue) {
                    $maxValue = $value;
                }

                $arReferencia[] = $ref;
            }
        }

        $arReferencia = array_unique($arReferencia);
        $idxRef = 0;

        $this->columSize = floor($this->gtamanho / count($arReferencia));

        for ($linex = ($this->margem + $this->columSize); $linex <= ($this->gtamanho + $this->margem); $linex += $this->columSize) {
            // Linha para descrição do eixo X
            imageline($this->image, $linex, $this->margem, $linex, ($this->gtamanho + $this->margem), $this->getColor(0)); // linha guia

            // Desenha descrição no eixo X
            if(isset($arReferencia[$idxRef])){
                imagestringup($this->image, 2, ($linex - $this->margem), ($this->gtamanho + ($this->margem * 6)), $arReferencia[$idxRef], null);
            }

            $idxRef++;
        }

        $liney = $this->margem;
        for ($i = count($arReferencia); $i >= 1; $i--) {
            $val = floor($i * ($maxValue / count($arReferencia)));

            // Valores de referencia do lado direito eixo Y
            imageline($this->image, $this->margem, $liney, ($this->gtamanho + $this->margem), $liney, $this->getColor(0));
            imagestring($this->image, 1, ($this->gtamanho + ($this->margem * 2)), $liney, utf8_decode($val), $this->getColor(0));

            $liney += $this->columSize;
        }

        return $maxValue;
    }

    // Instancia de grafico linha
    function graficoLinha() {
        $maxValue = $this->desenhaBase();

        $labelY = $this->margem;
        $idx = 0;
        foreach ($this->vetor as $label => $item) {
            $idx++;
            $color = $this->getColor($idx);
            $this->desenhaLinha($item, $color, $maxValue);
            $this->desenhaLabel($label, $color, $labelY);
            $labelY += ($this->margem * 2);
        }
    }

    // Instancia de grafico poligno
    function graficoPoligno() {
        $maxValue = $this->desenhaBase();

        $labelY = $this->margem;

        $idx = 0;
        foreach ($this->vetor as $label => $item) {
            $points = array();
            $arPonto = array();
            $arValor = array();
            $idx++;

            $color = $this->getColor($idx, 0, 0, 0, 60);
            $x = $this->margem;
            $y = $this->gtamanho + $this->margem;

            foreach ($item as $referencia => $value) {
                $val = ($this->gtamanho + ($this->margem * 1)) - (floor((($this->gtamanho) * $value) / $maxValue));

                $points[] = $x;
                $points[] = $y;
                $points[] = ($x + $this->columSize);
                $points[] = $val;

                $arPonto[] = array(
                    'x' => ($x + $this->columSize),
                    'y' => $val
                );

                $arValor[] = array(
                    'x' => ($x + ($this->columSize + $this->margem)),
                    'y' => ($val - 3),
                    'value' => $value
                );

                $y = $val;
                $x += $this->columSize;
            }

            $points[] = $this->gtamanho + $this->margem;
            $points[] = $this->gtamanho + $this->margem;

            imagefilledpolygon($this->image, $points, (count($points) / 2), $color);

            if($this->opMostrarPontoValor) {
                foreach ($arPonto as $item) {
                    $this->desenhaPonto($item['x'], $item['y'], null);
                }

                foreach ($arValor as $item) {
                    imagestring($this->image, 2, $item['x'], ($item['y'] - $this->margem), utf8_decode($item['value']), null);
                }
            }

            $this->desenhaLabel($label, $color, $labelY);
            $labelY += ($this->margem * 2);
        }
    }

    function empilharGrafico(){
        $lastKey = null;

        // Empilhar
        foreach($this->vetor as $keyA => $item){
            foreach($item as $keyB => $value){
                if($lastKey){
                    $this->vetor[$keyA][$keyB] += $this->vetor[$lastKey][$keyB];
                }
            }

            $lastKey = $keyA;
        }

        $this->vetor = array_reverse($this->vetor);
    }

    function out() {
        // Linhas Preta
        imageline($this->image, $this->margem, ($this->gtamanho + $this->margem), ($this->gtamanho + $this->margem), ($this->gtamanho + $this->margem), null);
        imageline($this->image, $this->margem, $this->margem, $this->margem, ($this->gtamanho + $this->margem), null);

        imagepng($this->image);
        imagedestroy($this->image);
    }
}
