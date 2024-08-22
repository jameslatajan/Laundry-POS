<?php

namespace App\Libraries;

class Htmlhelper
{
	public function tabular_header($headers, $sortby = '', $sortorder = '', $frm = 'frmFilter', $callback = '')
	{
		$view  = '';

		if (!empty($headers)) {
			foreach ($headers as $col) {

				$view .= '<th onclick="sorting(\'' . $col['column_field'] . '\', \'' . $frm . '\', \'' . $callback . '\')"';
				if ($col['column_field'] == $sortby) {
					if (strtolower($sortorder)  == "desc") {
						$view .= "\n" . 'class="' . $col['width'] . ' mdi mdi-sort-descending text-primary" align="' . $col['align'] . '" nowrap>';
					} else {
						$view .= "\n" . 'class="' . $col['width'] . ' mdi mdi-sort-ascending text-primary" align="' . $col['align'] . '" nowrap>';
					}
				} else {
					$view .= "\n" . ' class="' . $col['width'] . '" align="' . $col['align'] . '" nowrap>';
				}

				$view .=  $col['column_header'];
				$view .= '</th>';
			}
		}
		return $view;
	}
}
