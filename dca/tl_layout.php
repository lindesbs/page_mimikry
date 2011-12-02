<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

$GLOBALS['TL_DCA']['tl_layout']['config']['onsubmit_callback'][] = array('tl_layout_pdf_mimikry','updateModules');
$GLOBALS['TL_DCA']['tl_layout']['palettes']['__selector__'][] = 'mimikry_output';
$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] = str_replace(",name,",",name,mimikry_output,",$GLOBALS['TL_DCA']['tl_layout']['palettes']['default']);

$GLOBALS['TL_DCA']['tl_layout']['palettes']['page_mimiky'] = '{title_legend},name,mimikry_output,mimikry_output_type;'.
			'{header_legend},mimikry_output_orientation,mimikry_output_unit,mimikry_margins;{style_legend},stylesheet,aggregate,skipTinymce;{modules_legend},modules';

$GLOBALS['TL_DCA']['tl_layout']['fields']['mimikry_output'] = array
(
	'label'                 => &$GLOBALS['TL_LANG']['tl_layout']['mimikry_output'],		
	'exclude'               => true,
	'filter'                => true,
	'inputType'				=> "select",
	'options'				=> array("default","page_mimiky"),
	'reference'               => &$GLOBALS['TL_LANG']['tl_layout']['mimikry_output'],
	'eval'                  => array('submitOnChange'=>true)
);
$GLOBALS['TL_DCA']['tl_layout']['fields']['mimikry_output_type'] = array
(
	'label'                 => &$GLOBALS['TL_LANG']['tl_layout']['mimikry_output_type'],		
	'exclude'               => true,
	'inputType'				=> "select",
	'options'				=> array("PDF"),
	'reference'               => &$GLOBALS['TL_LANG']['tl_layout']['mimikry_output'],
	'eval'                  => array('submitOnChange'=>true,'tl_style' => 'w50')
);
$GLOBALS['TL_DCA']['tl_layout']['fields']['mimikry_margins'] = array
(
	'label'                 => &$GLOBALS['TL_LANG']['tl_layout']['mimikry_margins'],		
	'exclude'               => true,
	'inputType'				=> "text",
	'eval'                  => array('rgxp'=>'digit','size'=>4,'multiple'=>true)
);
		
$GLOBALS['TL_DCA']['tl_layout']['fields']['mimikry_output_unit'] = array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['mimikry_output_unit'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'                 => array('mm', 'pt', 'cm','in'),
			'reference'               => &$GLOBALS['TL_LANG']['MSC'],
			'eval'                    => array('nospace'=>true, 'tl_class'=>'w50')
		);
		
$GLOBALS['TL_DCA']['tl_layout']['fields']['mimikry_output_orientation'] = array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['mimikry_output_orientation'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'                 => array('P', 'L'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_layout']['mimikry_output_orientation'],
			'eval'                    => array()
		);
		

class tl_layout_pdf_mimikry extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
		
		
		//getPageSizeFromFormat
	}
	
	public function updateModules(DataContainer $dc)
	{
	
	// Return if there is no active record (override all)
		if ((!$dc->activeRecord) || ($dc->activeRecord->mimikry_output == 'default'))
		{
			return;
		}
		
		
		$arrModules = array(
			'header'	=> 1,
			'headerHeight'	=> array('unit' => 'mm','value' => "1"),
			'footer'	=> 1,
			'footerHeight'	=> array('unit' => 'mm','value' => "1"),
			'cols'		=> '1cl',
			'modules' => array(
			
				array('mod' => "0",'col'=>'footer'),
				array('mod' => "0",'col'=>'header'),
				array('mod' => "0",'col'=>'main'),
			)
		);
		

		$this->Database->prepare("UPDATE tl_layout %s WHERE id=?")
						->set($arrModules)
					   ->execute($dc->id);
		

	}
}

?>