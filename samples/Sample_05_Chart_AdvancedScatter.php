<?php

include_once 'Sample_Header.php';

use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\Chart\Marker;
use PhpOffice\PhpPresentation\Shape\Chart\Series;
use PhpOffice\PhpPresentation\Shape\Chart\Series\AdvancedScatterSeries;
use PhpOffice\PhpPresentation\Shape\Chart\Series\DataPoint;
use PhpOffice\PhpPresentation\Shape\Chart\Type\AdvancedScatter;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Style\Font;
use PhpOffice\PhpPresentation\Style\Outline;

// Create new PHPPresentation object
echo date('H:i:s') . ' Create new PHPPresentation object' . EOL;
$objPHPPresentation = new PhpPresentation();

// Set properties
$objPHPPresentation->getDocumentProperties()
    ->setCreator('PHPOffice')
    ->setLastModifiedBy('PHPPresentation Team')
    ->setTitle('Sample 05 — Advanced Scatter Chart');

// Remove first slide
$objPHPPresentation->removeSlideByIndex(0);

// --- Slide 1: multi-series advanced scatter --------------------------------
echo EOL . date('H:i:s') . ' Create templated slide #1' . EOL;
$currentSlide = createTemplatedSlide($objPHPPresentation);

echo date('H:i:s') . ' Create AdvancedScatter chart with two series' . EOL;
$scatter = new AdvancedScatter();
$scatter->setScatterStyle(AdvancedScatter::STYLE_LINE_MARKER);

$seriesA = new AdvancedScatterSeries('Product A', [
    [1.0, 12.5],
    [2.0, 15.0],
    [3.0, 13.0],
    [4.0, 17.5],
    [5.0, 20.0],
    [5.0, 18.5], // duplicate X value — only AdvancedScatter supports this
    [6.0, 22.0],
]);
$seriesA->getMarker()->setSymbol(Marker::SYMBOL_CIRCLE)->setSize(7);

$seriesB = new AdvancedScatterSeries('Product B', [
    [1.5, 8.0],
    [2.7, 11.0],
    [3.3, 9.5],
    [4.8, 13.0],
    [6.2, 16.5],
]);
$seriesB->getMarker()->setSymbol(Marker::SYMBOL_DIAMOND)->setSize(7);

$scatter->addSeries($seriesA)->addSeries($seriesB);

$shape = $currentSlide->createChartShape();
$shape->setName('Sales by Price Point')
    ->setResizeProportional(false)
    ->setHeight(550)->setWidth(700)->setOffsetX(120)->setOffsetY(80);
$shape->getBorder()->setLineStyle(Border::LINE_SINGLE);
$shape->getTitle()->setText('Sales by Price Point');
$shape->getPlotArea()->setType($scatter);
$shape->getPlotArea()->getAxisX()->setTitle('Price ($)');
$shape->getPlotArea()->getAxisY()->setTitle('Units Sold');

// --- Slide 2: per-data-point coloring + varyColors -------------------------
echo EOL . date('H:i:s') . ' Create templated slide #2' . EOL;
$currentSlide = createTemplatedSlide($objPHPPresentation);

echo date('H:i:s') . ' Highlight individual data points' . EOL;
$scatter2 = new AdvancedScatter();
$scatter2->setScatterStyle(AdvancedScatter::STYLE_MARKER);

$series = new AdvancedScatterSeries('Customers', [
    [10.0, 20.0],
    [15.0, 35.0],
    [25.0, 50.0],
    [35.0, 60.0],
    [45.0, 75.0], // outlier
]);
$series->getMarker()->setSymbol(Marker::SYMBOL_CIRCLE)->setSize(10);

// Color each individual data point.
$series->getDataPointFill(0)->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF1F77B4'));
$series->getDataPointFill(1)->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF2CA02C'));
$series->getDataPointFill(2)->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FFFF7F0E'));
$series->getDataPointFill(3)->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FF9467BD'));
$series->getDataPointFill(4)->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FFD62728'));

$scatter2->addSeries($series);

$shape = $currentSlide->createChartShape();
$shape->setResizeProportional(false)->setHeight(550)->setWidth(700)->setOffsetX(120)->setOffsetY(80);
$shape->getTitle()->setText('Per-data-point Coloring');
$shape->getPlotArea()->setType($scatter2);
$shape->getPlotArea()->getAxisX()->setTitle('Spend');
$shape->getPlotArea()->getAxisY()->setTitle('LTV');

// --- Slide 3: smooth curve scatter -----------------------------------------
echo EOL . date('H:i:s') . ' Create templated slide #3' . EOL;
$currentSlide = createTemplatedSlide($objPHPPresentation);

echo date('H:i:s') . ' Smooth curve advanced scatter' . EOL;
$scatter3 = new AdvancedScatter();
$scatter3->setScatterStyle(AdvancedScatter::STYLE_SMOOTH_MARKER);

$wave = new AdvancedScatterSeries('Wave');
for ($x = 0; $x <= 6.28; $x += 0.3) {
    $wave->addDataPoint($x, sin($x));
}

$outline = new Outline();
$outline->setWidth(2);
$outline->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color(Color::COLOR_BLUE));
$wave->setOutline($outline);
$wave->getMarker()->setSymbol(Marker::SYMBOL_CIRCLE)->setSize(5);

$scatter3->addSeries($wave);

$shape = $currentSlide->createChartShape();
$shape->setResizeProportional(false)->setHeight(550)->setWidth(700)->setOffsetX(120)->setOffsetY(80);
$shape->getTitle()->setText('Smooth Curve');
$shape->getPlotArea()->setType($scatter3);

// --- Slide 4: per-data-point labels (brand-style scatter) -----------------
echo EOL . date('H:i:s') . ' Create templated slide #4' . EOL;
$currentSlide = createTemplatedSlide($objPHPPresentation);

echo date('H:i:s') . ' Per-data-point labels (positional triples + DataPoint)' . EOL;
$scatter4 = new AdvancedScatter();
$scatter4->setScatterStyle(AdvancedScatter::STYLE_MARKER);

// Quick form: positional triples [x, y, label]. Title becomes the visible label
// rendered next to the marker via c:dLbl.
$brands = new AdvancedScatterSeries('Brands', [
    [91.5, 12.0, 'Apple'],
    [45.7, 8.5, 'Adidas'],
    [44.0, 6.0, 'Audi'],
    [79.6, 14.0, 'Allianz'],
    [35.5, 4.5, 'Amazon'],
    [94.9, 13.5, 'Airbnb'],
]);

// Or attach a DataPoint object for full styling control over a single label.
$outlier = new DataPoint(10.1, 22.0, 'Outlier');
$outlier->setLabelPosition(Series::LABEL_TOP);
$outlier->setFont((new Font())->setBold(true)->setSize(12)->setColor(new Color(Color::COLOR_RED)));
$outlier->setFill((new Fill())->setFillType(Fill::FILL_SOLID)->setStartColor(new Color(Color::COLOR_RED)));
$brands->addPoint($outlier);

$scatter4->addSeries($brands);

$shape = $currentSlide->createChartShape();
$shape->setResizeProportional(false)->setHeight(550)->setWidth(700)->setOffsetX(120)->setOffsetY(80);
$shape->getTitle()->setText('Brand Scatter (with per-point labels)');
$shape->getPlotArea()->setType($scatter4);
$shape->getPlotArea()->getAxisX()->setTitle('Brand Strength');
$shape->getPlotArea()->getAxisY()->setTitle('Growth %');

// Customize the axis lines: gray X axis, slightly thicker Y axis.
$shape->getPlotArea()->getAxisX()->getOutline()->setWidth(1);
$shape->getPlotArea()->getAxisX()->getOutline()->getFill()->getStartColor()->setRGB('888888');
$shape->getPlotArea()->getAxisY()->getOutline()->setWidth(2);
$shape->getPlotArea()->getAxisY()->getOutline()->getFill()->getStartColor()->setRGB('444444');

// Save file
echo EOL . write($objPHPPresentation, basename(__FILE__, '.php'));

if (!CLI) {
    include_once 'Sample_Footer.php';
}
