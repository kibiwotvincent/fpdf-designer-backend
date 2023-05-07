<?php 
namespace App\Lib\Fpdf;

use App\Lib\Fpdf\FPDF;
use App\Models\Document;

class PDF
{
	protected $pageWidth = 210;
	protected $pdf = null;
	protected $document = null;
	protected $save = false;
	
	public function __construct($document, $save = false) {
		$this->document = $document;
		$this->save = $save;
		$this->pdf = new FPDF('P','mm','A4');
		$this->create();
	}
	
	public function create() {
		
		$this->pdf->AddFont('Calibri','','Calibri.php');
		
		$this->pdf->AddPage();	
		//insert data
		$this->addData();
		if($this->save) {
			$this->pdf->Output('F', storage_path().'/app/public/documents/'.$this->document->uuid.'.pdf');
		}
		else {
			header('Access-Control-Allow-Origin: *');
			$this->pdf->Output('D', $this->document->uuid.'.pdf');
		}
	}
	
	private function addData() {
		foreach($this->document->draggables as $draggable):
			$scaleFactor = 190/718; //1px equivalent in milimeters
			$margin = 38 * $scaleFactor; //38 is margin in px
			
			$left = ($draggable['left'] * $scaleFactor) + $margin; //convert from px to mm
			$top = ($draggable['top'] * $scaleFactor) + $margin;
			$width = $draggable['width'] * $scaleFactor;
			$height = $draggable['height'] * $scaleFactor;
				
			if($draggable['type'] == 'text') {
				$fontSize = $draggable['font_size'];
				$fontWeight = $draggable['font_weight'] == 'bold' ? 'b' : '';
				$fontColor = $this->hexToRgb($draggable['font_color']);
				$fillBackground = ($draggable['background'] != 'none');
				$borderColor = $this->hexToRgb($draggable['border_color']);
				$textAlign = ucwords($draggable['text_align'][0]);
				
				$border = '';
				$this->pdf->SetLineWidth($draggable['border_weight'] * $scaleFactor);
				foreach(['left','top','right','bottom'] as $key) {
					if($draggable['border_'.$key] != 'yes') continue;
					
					$border .= ucfirst($key[0]);
				}
				
				if($fillBackground) {
					$backgroundColor = $this->hexToRgb($draggable['background_color']);
					$this->pdf->SetFillColor($backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
				}
				
				$this->pdf->SetXY($left, $top);
				$this->pdf->SetTextColor($fontColor[0], $fontColor[1], $fontColor[2]);
				$this->pdf->SetDrawColor($borderColor[0], $borderColor[1], $borderColor[2]);
				$this->pdf->SetFont($draggable['font_family'], $fontWeight, $fontSize);
				$text = iconv("UTF-8", "CP1252//TRANSLIT", $draggable['text']);
				
				//$this->pdf->Cell($width, $height, $draggable['text'], $border, 0, $textAlign, $fillBackground);
				$this->pdf->MultiCell($width, $height, $text, $border, $textAlign, $fillBackground);
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