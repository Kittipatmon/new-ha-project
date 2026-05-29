<?php
require __DIR__.'/../vendor/autoload.php';

try {
    $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
    $fontDirs = $defaultConfig['fontDir'];

    $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
    $fontData = $defaultFontConfig['fontdata'];

    $mpdf = new \Mpdf\Mpdf([
        'mode'            => 'utf-8',
        'format'          => 'A4-L',
        'default_font'    => 'thsarabun',
        'fontDir'         => array_merge($fontDirs, [
            __DIR__ . '/fonts',
        ]),
        'fontdata'        => $fontData + [
            'thsarabun' => [
                'R'  => 'THSarabun.ttf',
                'B'  => 'THSarabun Bold.ttf',
                'I'  => 'THSarabun Italic.ttf',
                'BI' => 'THSarabun BoldItalic.ttf',
            ],
        ],
        'tempDir'         => __DIR__ . '/../storage/app/mpdf-tmp',
    ]);

    $mpdf->WriteHTML('<h1>ทดสอบภาษาไทย</h1>');
    echo "Success";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
