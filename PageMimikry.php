<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

class PageMimikry extends PageRegular
{

	public function mimikryGeneratePage(Database_Result $objPage, Database_Result $objLayout, PageRegular $objPageRegular)
	{
		if ($objLayout->mimikry_output!='default')
		{
		
			//print_a($objPage);
			//print_a($objLayout);
			//print_a($objPageRegular);
			
			$this->Template = $objPageRegular->Template;
			$this->outputMimikryPage($objPage,$objPageRegular,$objLayout);
			
			die();
		}
		
    
}


	public function outputMimikryPage(Database_Result $objPage,$objRegular,$objLayout)
	{

		// Set the page title and description AFTER the modules have been generated
		$this->Template->mainTitle = $objPage->rootTitle;
		$this->Template->pageTitle = strlen($objPage->pageTitle) ? $objPage->pageTitle : $objPage->title;

		// Remove shy-entities (see #2709)
		$this->Template->mainTitle = str_replace('[-]', '', $this->Template->mainTitle);
		$this->Template->pageTitle = str_replace('[-]', '', $this->Template->pageTitle);

		// Assign the title (backwards compatibility)
		$this->Template->title = $this->Template->mainTitle . ' - ' . $this->Template->pageTitle;
		$this->Template->description = str_replace(array("\n", "\r", '"'), array(' ' , '', ''), $objPage->description);

		// Body onload and body classes
		$this->Template->onload = trim($objLayout->onload);
		$this->Template->class = trim($objLayout->cssClass . ' ' . $objPage->cssClass);

		// HOOK: extension "bodyclass"
		if (in_array('bodyclass', $this->Config->getActiveModules()))
		{
			if (strlen($objPage->cssBody))
			{
				$this->Template->class .= ' ' . $objPage->cssBody;
			}
		}

		// Execute AFTER the modules have been generated and create footer scripts first
		$objRegular->createFooterScripts($objLayout);
		$objRegular->createHeaderScripts($objLayout);

		// Add an invisible character to empty sections (IE fix)
		if (!$this->Template->header && $objLayout->header)
		{
			$this->Template->header = '&nbsp;';
		}

		if (!$this->Template->left && ($objLayout->cols == '2cll' || $objLayout->cols == '3cl'))
		{
			$this->Template->left = '&nbsp;';
		}

		if (!$this->Template->right && ($objLayout->cols == '2clr' || $objLayout->cols == '3cl'))
		{
			$this->Template->right = '&nbsp;';
		}

		if (!$this->Template->footer && $objLayout->footer)
		{
			$this->Template->footer = '&nbsp;';
		}

		
		require_once(TL_ROOT . '/system/config/tcpdf.php');
		require_once(TL_ROOT . '/plugins/tcpdf/tcpdf.php');

		$pdf = new MimikryPDF($objLayout->mimikry_output_orientation,$objLayout->mimikry_output_unit, "A4", true, 'UTF-8', false);

		// Set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(PDF_AUTHOR);
		$pdf->SetTitle($this->Template->pageTitle);
		$pdf->SetSubject($this->Template->description);
		$pdf->SetKeywords($objArticle->keywords);
		$pdf->SetDisplayMode("fullpage");
		
		$arrMargin = deserialize($objLayout->mimikry_margins);		
		
		$strHeader = $this->replaceInsertTags($this->Template->header);
		$strFooter = $this->replaceInsertTags($this->Template->footer);
		
		$strTitle = $this->replaceInsertTags($this->Template->title);
				
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->SetMargins($arrMargin[1],$arrMargin[0],$arrMargin[2]);
		$pdf->SetHeaderMargin($arrMargin[0]);
		$pdf->SetFooterMargin($arrMargin[3]);

		$pdf->setHeaderText($strHeader);
		$pdf->setFooterText($strFooter);

		
		
// add a page
$pdf->AddPage();

$strMain = $this->replaceInsertTags($this->Template->main);

$pdf->writeHTML($strMain, true, false, true, false, '');

$pdf->lastPage();

$pdf->Output(standardize($this->Template->pageTitle).'.pdf', 'I');


	}


	
}

		require_once(TL_ROOT . '/plugins/tcpdf/tcpdf.php');

		
		
class MimikryPDF extends TCPDF 
{

	public function setHeaderText($strHedaerText)
	{
		$this->strHeaderText = $strHedaerText;
	}
	
	public function setFooterText($strFooterText)
	{
		$this->strFooterText = $strFooterText;
	}

    //Page header
    public function Header() 
	{		
		$this->writeHTML($this->strHeaderText, true, false, true, false, '');
    }

    // Page footer
    public function Footer() 
	{
        // Position at 15 mm from bottom
        $this->SetY(-3);
    	$this->writeHTML($this->strFooterText, true, false, true, false, '');
    }
}



?>