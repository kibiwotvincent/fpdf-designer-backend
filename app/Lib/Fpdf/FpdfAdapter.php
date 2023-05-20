<?php 
namespace App\Lib\Fpdf;

use App\Lib\Fpdf\FPDF;
use App\Models\Interfaces\DocumentInterface;
use Log;

class FpdfAdapter extends FPDF
{
	protected $document = null;
	
	public function __construct(DocumentInterface $document) {
		parent::__construct($document->page_settings['orientation'], 'mm', $document->page_settings['size']);
		$this->document = $document;
		
		//add fonts
		$this->addFonts();
		//set page auto break to match the document's bottom margin
		$this->SetAutoPageBreak(true, $this->document->page_settings['bottom_margin']);
		//add page
		$this->AddPage();	
		//insert data
		$this->addData();
	}
	
	private function addFonts() {
		//add available fonts
		$this->AddFont('Calibri','','Calibri.php');
	}
	
	public function Header() {
		// Page header
		
	}

	public function Footer() {
		// Page footer
		//$this->SetY(-15);
	}
	
	public function save() {
		$this->Output('F', storage_path().'/app/public/'.$this->document->storageDirectory.'/'.$this->document->uuid.'.pdf');
	}
	
	public function preview() {
		header('Access-Control-Allow-Origin: *');
		$this->Output('D', $this->document->uuid.'.pdf');
	}
	
	private function addData() {
		foreach($this->document->draggables as $draggable):
			$scaleFactor = 1 / $this->document->page_settings['scale_factor']; //1px equivalent in millimeters under 96PPI
			
			$leftMargin = $this->document->page_settings['left_margin'];
			$topMargin = $this->document->page_settings['top_margin'];
			
			//convert left, top, width & height units from px to mm
			$left = ($draggable['left'] * $scaleFactor) + $leftMargin; 
			$top = ($draggable['top'] * $scaleFactor) + $topMargin;
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
				$this->SetLineWidth($draggable['border_weight']);
				foreach(['left','top','right','bottom'] as $key) {
					if($draggable['border_'.$key] != 'yes') continue;
					
					$border .= ucfirst($key[0]);
				}
				
				if($fillBackground) {
					$backgroundColor = $this->hexToRgb($draggable['background_color']);
					$this->SetFillColor($backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
				}
				
				$this->SetXY($left, $top);
				$this->SetTextColor($fontColor[0], $fontColor[1], $fontColor[2]);
				$this->SetDrawColor($borderColor[0], $borderColor[1], $borderColor[2]);
				$this->SetFont($draggable['font_family'], $fontWeight, $fontSize);
				$text = iconv("UTF-8", "CP1252//TRANSLIT", $draggable['text']);
				
				//$this->Cell($width, $height, $draggable['text'], $border, 0, $textAlign, $fillBackground);
				$this->MultiCell($width, $height, $text, $border, $textAlign, $fillBackground);
			}
			elseif($draggable['type'] == 'image') {
				$this->SetXY($left, $top);
				if(isset($draggable['is_local']) && $draggable['is_local'] == "yes") {
					$imageArray = explode('/', $draggable['url']);
					$imageName = $imageArray[count($imageArray) - 1];
					$this->Image(base_path('storage/app/public/uploads/'.$imageName), $left, $top, $width, $height);
				}
				else {
					$this->Image($draggable['url'], $left, $top, $width, $height);
				}
			}
			elseif($draggable['type'] == 'rectangle') {
				$this->SetLineWidth($draggable['border_weight']);
				$borderColor = $this->hexToRgb($draggable['border_color']);
				$this->SetDrawColor($borderColor[0], $borderColor[1], $borderColor[2]);
				
				$style = 'D';
				if($draggable['background'] != 'none') {
					$backgroundColor = $this->hexToRgb($draggable['background_color']);
					$this->SetFillColor($backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
					$style .= 'F';
				}
				
				$this->Rect($left, $top, $width, $height, $style);
			}
			elseif($draggable['type'] == 'line') {
				$this->SetLineWidth($draggable['line_weight']);
				$borderColor = $this->hexToRgb($draggable['line_color']);
				$this->SetDrawColor($borderColor[0], $borderColor[1], $borderColor[2]);
				
				$x1 = $left;
				$y1 = $top;
				
				if(isset($draggable['line_type']) && $draggable['line_type'] == "vertical") {
					$x2 = $left;
					$y2 = $top + $width;
				}
				else {
					$x2 = $left + $width;
					$y2 = $top;
				}
				$this->Line($x1, $y1, $x2, $y2);
			}
		endforeach;
	}
	
	private function hexToRgb($hexColor) {
		$rgbColorArray = [0,0,0];
		$hexColorArray = str_split(str_ireplace('#', '', $hexColor), 2);
		for($i = 0; $i < count($hexColorArray); $i++) {
			$rgbColorArray[$i] = hexdec($hexColorArray[$i]);
		}
		return $rgbColorArray;
	}
}
?>