<?php 
namespace App\Lib\Fpdf;

use App\Lib\Fpdf\FPDF;
use App\Models\Document;

class PDF
{
	protected $pageWidth = 210;
	protected $pdf = null;
	protected $document = null;
	
	public function __construct($documentID) {
		$this->document = Document::where('uuid', $documentID)->first();
		$this->pdf = new FPDF('P','mm','A4');
		$this->create();
	}
	
	public function create() {
		
		$this->pdf->AddFont('Calibri','','Calibri.php');
		
		$this->pdf->AddPage();	
		//insert data
		$this->addData();
					
		$this->pdf->Output('D', 'Doc1.pdf');
	}
	
	private function addData() {
		$labels = [
					['x' => 48, 'y' => 49.5, 'value' => "##"], ['x' => 127, 'y' => 50, 'value' => "##"],
					['x' => 33, 'y' => 56, 'value' => "##"],
					['x' => 37, 'y' => 63, 'value' => "##"], ['x' => 66, 'y' => 63, 'value' => "##"],
					['x' => 50, 'y' => 69, 'value' => "##"],
					
					['x' => 43, 'y' => 85, 'value' => "##"],
					['x' => 36, 'y' => 91, 'value' => "##"],
					
					['x' => 90, 'y' => 102, 'value' => "##"],
					['x' => 38, 'y' => 108, 'value' => "##"],
					['x' => 21, 'y' => 118, 'value' => "##"],
					['x' => 42, 'y' => 125, 'value' => "##"],
					['x' => 33, 'y' => 130, 'value' => "##"],
					
					['x' => 53, 'y' => 153, 'value' => "##"],
					['x' => 25, 'y' => 236, 'value' => "##"], ['x' => 115, 'y' => 236, 'value' => "##"],
					
				   ];
		foreach($this->document->draggables as $draggable):
			$scaleFactor = 190/718; //1px equivalent in milimeters
			$margin = 38 * $scaleFactor; //38 is margin in px
			
			$left = ($draggable['left'] * $scaleFactor) + $margin; //convert from px to mm
			$top = ($draggable['top'] * $scaleFactor) + $margin;
			$width = $draggable['width'] * $scaleFactor;
			$height = $draggable['height'] * $scaleFactor;
				
			if($draggable['type'] == 'text') {
				$fontSize = $draggable['font_size'] ?? 10;
				$fontWeight = $draggable['font_weight'] == 'bold' ? 'b' : '';
				$fontColor = (isset($draggable['font_color']) && $draggable['font_color'] != '') ? $this->hexToRgb($draggable['font_color']) : [0,0,0];
				$backgroundColor = (isset($draggable['background_color']) && $draggable['background_color'] != '') ? $this->hexToRgb($draggable['background_color']) : [255,255,255];
				$borderColor = (isset($draggable['border_color']) && $draggable['border_color'] != '') ? $this->hexToRgb($draggable['border_color']) : [0,0,0];
				$border = '';
				if((isset($draggable['border_bottom']) && $draggable['border_bottom'] == '1')) $border .= 'B';
				if(isset($draggable['border_weight']) && $draggable['border_weight'] != '') {
					$this->pdf->SetLineWidth($draggable['border_weight'] * $scaleFactor);
				}
				
				$textAlign = isset($draggable['text_align']) && $draggable['text_align'] != '' ? ucwords($draggable['text_align'][0]) : 'L';
				
				$this->pdf->SetXY($left, $top);
				$this->pdf->SetTextColor($fontColor[0], $fontColor[1], $fontColor[2]);
				$this->pdf->SetFillColor($backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
				$this->pdf->SetDrawColor($borderColor[0], $borderColor[1], $borderColor[2]);
				$this->pdf->SetFont('helvetica', $fontWeight, $fontSize);
				$text = iconv("UTF-8", "CP1252//TRANSLIT", $draggable['text']);
				
				//$this->pdf->Cell($width, $height, $draggable['text'], $border, 0, $textAlign, $fill = true);
				$this->pdf->MultiCell($width, $height, $text, $border, $textAlign, $fill = true);
			}
			elseif($draggable['type'] == 'image') {
				$this->pdf->SetXY($left, $top);
				
				$this->pdf->Image('logo.png', null, null, $width, $height,);
			}
		endforeach;
	}
	
	public function hexToRgb($hexColor) {
		$rgbColorArray = [0,0,0];
		$hexColorArray = str_split(str_ireplace('#', '', $hexColor), 2);
		for($i = 0; $i < count($hexColorArray); $i++) {
			$rgbColorArray[$i] = hexdec($hexColorArray[$i]);
		}
		return $rgbColorArray;
	}
}
?>