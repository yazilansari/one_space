<?php
class Pdf extends \setasign\Fpdi\Tcpdf\Fpdi
		{
		    /**
		     * "Remembers" the template id of the imported page
		     */
		    protected $tplId;

		    /**
		     * Draw an imported PDF logo on every page
		     */
		    function Header()
		    {
		        if ($this->tplId === null) {
		            $this->setSourceFile(APPPATH.'TCPDF/examples/example_012.pdf');
		            $this->tplId = $this->importPage(1);
		        }
		        // $size = $this->useImportedPage($this->tplId, 130, 5, 60);
		        // $this->AddPage();
		        $this->useTemplate($this->tplId);
		        $this->SetFont('freesans', 'B', 20);
		        $this->SetTextColor(0);
		        $this->SetXY(PDF_MARGIN_LEFT, 5);
		        $this->Image(APPPATH.'TCPDF/examples/images/img.png', 150, 100, 20, 20);
		        // $this->Cell(0, $size['height'], 'TCPDF and FPDI');
		    }

		    function Footer()
		    {
		        // emtpy method body
		    }
		}