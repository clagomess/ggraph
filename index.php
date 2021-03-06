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

error_reporting(0);
include_once 'GGraph.php';

$arValues = array(
    'R$ 66.64 - Ourocard > Jaqueta Fuguetinho' => array(
        '11/2014' => 156.20,
        '12/2014' => 536.00,
        '01/2014' => 800.01,
        '02/2014' => 370.62,
        '03/2014' => 970.62,
        '04/2014' => 081.50,
        '05/2014' => 40.20,
        '06/2014' => 536.00,
        '07/2014' => 122.01,
        '08/2014' => 79.62,
        '09/2014' => 111.62,
        '10/2014' => 360.50,
        '11/2015' => 156.20,
        '12/2015' => 100.00,
        '01/2015' => 789.01,
        '02/2015' => 144.62,
        '03/2015' => 078.62,
        '04/2015' => 630.50
    ),
    'Bar' => array(
        '11/2014' => 100.20,
        '12/2014' => 154.00,
        '01/2014' => 70.01,
        '02/2014' => 23.62,
        '03/2014' => 452.62,
        '04/2014' => 234.50
    ),
    'Ação Açucar' => array(
        '11/2014' => 345,
        '12/2014' => 800,
        '01/2014' => 92,
        '02/2014' => 623,
        '03/2014' => 716,
        '04/2014' => 256
    )
);


// flush image

$graph = new GGraph(750, $arValues);
$graph->opMostrarPontoValor = false;
$graph->opTransparente = false;
$graph->empilharGrafico();
//$graph->graficoPoligno();
$graph->graficoLinha();

header('Content-type: image/png');
$graph->out();

