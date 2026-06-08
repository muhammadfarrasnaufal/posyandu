<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Simple_pdf {
    protected $objects = array();
    protected $offsets = array();
    protected $pages = array();
    protected $pageContents = '';
    protected $fontSize = 10;
    protected $pageWidth = 595.28;
    protected $pageHeight = 841.89;

    public function __construct() {
        $this->AddPage();
        $this->SetFont('Helvetica', '', 10);
    }

    public function AddPage() {
        $this->pageContents = '';
        $this->pages = array();
        $this->pages[] = '';
    }

    public function SetFont($family, $style = '', $size = 10) {
        $this->fontSize = $size;
    }

    public function Text($x, $y, $text) {
        $this->pageContents .= sprintf("BT /F1 %s Tf %.2f %.2f Td (%s) Tj ET\n", $this->fontSize, $x, $this->pageHeight - $y, $this->escapeText($text));
    }

    public function Cell($x, $y, $text) {
        $this->Text($x, $y, $text);
    }

    public function Output($name = 'document.pdf', $dest = 'I') {
        $this->objects = array();
        $this->offsets = array();

        $content = $this->pageContents;
        $contentObject = $this->newObject("<< /Length " . strlen($content) . " >>\nstream\n" . $content . "endstream");
        $fontObject = $this->newObject("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>");
        $pageObject = $this->newObject("<< /Type /Page /Parent 2 0 R /MediaBox [0 0 " . $this->pageWidth . " " . $this->pageHeight . "] /Resources << /Font << /F1 " . $fontObject . " 0 R >> >> /Contents " . $contentObject . " 0 R >>");
        $pagesObject = $this->newObject("<< /Type /Pages /Kids [" . $pageObject . " 0 R] /Count 1 >>");
        $catalogObject = $this->newObject("<< /Type /Catalog /Pages " . $pagesObject . " 0 R >>");

        $pdf = "%PDF-1.3\n%\xE2\xE3\xCF\xD3\n";
        foreach ($this->objects as $id => $object) {
            $this->offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($this->objects) + 1) . "\n0000000000 65535 f \n";
        foreach ($this->offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        $pdf .= "trailer << /Size " . (count($this->objects) + 1) . " /Root " . $catalogObject . " 0 R >>\n";
        $pdf .= "startxref\n" . $xrefOffset . "\n%%EOF";

        if ($dest === 'I') {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $name . '"');
            echo $pdf;
            exit;
        }

        return $pdf;
    }

    protected function newObject($content) {
        $id = count($this->objects) + 1;
        $this->objects[$id] = $content;
        return $id;
    }

    protected function escapeText($text) {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}
