<?php 
namespace App\Lib\Fpdf;

use App\Lib\Fpdf\Fpdf;
use App\Models\Interfaces\DocumentInterface;
use Log;

class FpdfAdapter extends Fpdf
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
				$fontColor = $this->hexToRgb($draggable['font_color']);
				$fillBackground = ($draggable['background'] != 'none');
				$borderColor = $this->hexToRgb($draggable['border_color']);
				$textAlign = ucwords($draggable['text_align'][0]);
				
				$fontStyle = '';
				foreach($draggable['font_style'] as $style) {
					$fontStyle .= ucfirst($style[0]);
				}
				
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
				$this->SetFont($draggable['font_family'], $fontStyle, $fontSize);
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
			elseif($draggable['type'] == 'table') {
				$columnSettings = $draggable['column_settings'];
				$rowSettings = $draggable['row_settings'];
				
				foreach($draggable['cells'] as $rowIndex => $row) {
					$rowHeight = $this->getCellHeight($draggable, $rowIndex) * $scaleFactor;
					$this->SetY($top);
					$top += $rowHeight;
					$originX = $left;
					foreach($row as $columnIndex => $cell) {
						$settings = $rowIndex == 0 ? $columnSettings : $rowSettings;
						
						$textAlign = isset($cell['text_align']) ? ucwords($cell['text_align'][0]) : ucwords($settings['text_align'][0]);
						$fontSize = $cell['font_size'] ?? $settings['font_size'];
						$fontColor = $this->hexToRgb($cell['font_color'] ?? $settings['font_color']);
						
						$fillBackground = ($settings['background'] != 'none');
						$borderColor = $this->hexToRgb($settings['border_color']);
						
						$fontStyle = '';
						$fontStyles = isset($cell['font_style']) ? array_merge($cell['font_style'], $settings['font_style']) : $settings['font_style'];
						$uniqueFontStylesArray = [];
						foreach($fontStyles as $style) {
							//check if font style has been added already
							if(!in_array($style, $uniqueFontStylesArray)) {
								array_push($uniqueFontStylesArray, $style);
								$fontStyle .= ucfirst($style[0]);
							}
						}
						
						$border = $this->getCellBorderStyle($rowIndex, $columnIndex, $draggable);
						$this->SetLineWidth($settings['border_weight']);
						
						if($fillBackground) {
							$backgroundColor = $this->hexToRgb($settings['background_color']);
							$this->SetFillColor($backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
						}
						
						$cellWidth = $this->getCellWidth($draggable, $columnIndex) * $scaleFactor;
						
						$this->SetX($originX);
						$originX += $cellWidth;
						$this->SetTextColor($fontColor[0], $fontColor[1], $fontColor[2]);
						$this->SetDrawColor($borderColor[0], $borderColor[1], $borderColor[2]);
						$this->SetFont($settings['font_family'], $fontStyle, $fontSize);
						$text = iconv("UTF-8", "CP1252//TRANSLIT", $cell['value']);
						
						$this->Cell($cellWidth, $rowHeight, $text, $border, 0, $textAlign, $fillBackground);
					}
				}
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
	
	private function getCellWidth($table, $cellColumnIndex) {
		$row = $table['cells'][0];
		$columnsWithAutoWidth = [];
		$widths = [];
		foreach($row as $columnIndex => $cell) {
			if($cell['is_width_auto'] == 'yes') {
				array_push($columnsWithAutoWidth, $columnIndex);
			}
			else {
				$widths[$columnIndex] = $cell['width'];
			}
		}
		
		if(! empty($columnsWithAutoWidth)) {
			//calculate widths of the auto columns
			$remainingWidthToShare = $table['width'] - array_sum($widths);
			$cellWidth = $remainingWidthToShare / count($columnsWithAutoWidth);
			foreach($columnsWithAutoWidth as $columnIndex) {
				//assign calculated width to the cells whose width was set to auto
				$widths[$columnIndex] = $cellWidth;
			}
		}
		
		return $widths[$cellColumnIndex];
	}
	private function getCellHeight($table, $cellRowIndex) {
		$rowsWithAutoHeight = [];
		$heights = [];
		foreach($table['cells'] as $rowIndex => $row) {
			$cell = $row[0]; //get the first column only
			if($cell['is_height_auto'] == 'yes') {
				array_push($rowsWithAutoHeight, $rowIndex);
			}
			else {
				$heights[$rowIndex] = $cell['height'];
			}
		}
		
		if(! empty($rowsWithAutoHeight)) {
			//calculate height of the auto columns
			$remainingHeightToShare = $table['height'] - array_sum($heights);
			$cellHeight = $remainingHeightToShare / count($rowsWithAutoHeight);
			foreach($rowsWithAutoHeight as $rowIndex) {
				//assign calculated height to the cells whose height was set to auto
				$heights[$rowIndex] = $cellHeight;
			}
		}
		return $heights[$cellRowIndex];
	}
	private function getCellBorderStyle($rowIndex, $columnIndex, $table) {
		if($rowIndex == 0) {
			return $this->getColumnBorderStyle($rowIndex, $columnIndex, $table);
		}
		else {
			return $this->getRowBorderStyle($rowIndex, $columnIndex, $table);
		}
	}
	
	private function getColumnBorderStyle($rowIndex, $columnIndex, $table) {
		$style = '';
		$draggable  = $table['column_settings'];
		
		$includeVerticalBorders = $draggable['border_columns'] == "yes";
		
		if($includeVerticalBorders) {
			if($draggable['border_left'] == "yes" && $rowIndex == 0) {
				$style .= 'L';
			}
			else {
				if($rowIndex == 0 && $columnIndex != 0) {
					$style .= 'L';
				}
			}
			if($draggable['border_right'] == "yes" && $rowIndex == 0) {
				$style .= 'R';
			}
			else {
				if($rowIndex == 0 && $columnIndex != $table['columns'] - 1) {
					$style .= 'R';
				}
			}
		}
		else {
			if($draggable['border_left'] == "yes" && $rowIndex == 0 && $columnIndex == 0) {
				$style .= 'L';
			}
			if($draggable['border_right'] == "yes" && $rowIndex == 0 && $columnIndex == $table['columns'] - 1) {
				$style .= 'R';
			}
		}
		
		if($draggable['border_top'] == "yes" && $rowIndex == 0) {
			$style .= 'T';
		}
		if($draggable['border_bottom'] == "yes" && $rowIndex == 0) {
			$style .= 'B';
		}
		
		return $style;
	}
	
	private function getRowBorderStyle($rowIndex, $columnIndex, $table) {
		$style = '';
		$draggable  = $table['row_settings'];
		
		$includeVerticalBorders = $draggable['border_columns'] == "yes";
		$includeHorizontalBorders = $draggable['border_rows'] == "yes";
		
		if($includeVerticalBorders) {
			if($draggable['border_left'] == "yes" && $rowIndex > 0) {
				$style .= 'L';
			}
			else {
				if($rowIndex > 0 && $columnIndex != 0) {
					$style .= 'L';
				}
			}
			if($draggable['border_right'] == "yes" && $rowIndex > 0) {
				$style .= 'R';
			}
			else {
				if($rowIndex > 0 && $columnIndex != $table['columns'] - 1) {
					$style .= 'R';
				}
			}
		}
		else {
			if($draggable['border_left'] == "yes" && $rowIndex > 0 && $columnIndex == 0) {
				$style .= 'L';
			}
			if($draggable['border_right'] == "yes" && $rowIndex > 0 && $columnIndex == $table['columns'] - 1) {
				$style .= 'R';
			}
		}
		
		if($includeHorizontalBorders) {
			if($draggable['border_top'] == "yes" && $rowIndex >= 1) {
				$style .= 'T';
			}
			else {
				if($rowIndex > 1) {
					$style .= 'T';
				}
			}
			
			if($draggable['border_bottom'] == "yes" && $rowIndex == $table['rows'] - 1) {
				$style .= 'B';
			}
		}
		else {
			if($draggable['border_top'] == "yes" && $rowIndex == 1) {
				$style .= 'T';
			}
			if($draggable['border_bottom'] == "yes" && $rowIndex == $table['rows'] - 1) {
				$style .= 'B';
			}
		}
		
		return $style;
	}
}
?>