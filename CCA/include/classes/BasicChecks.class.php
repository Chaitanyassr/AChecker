<?php
/************************************************************************/
/* AChecker                                                             */
/************************************************************************/
/* Copyright (c) 2008 by Greg Gay, Cindy Li                             */
/* Adaptive Technology Resource Centre / University of Toronto			    */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

/**
* Basic Checks.class.php
* Class for accessibility validate
* This class contains basic functions called by Checks.class.php
*
* @access	public
* @author	Cindy Qi Li
* @package checker
*/

if (!defined("AC_INCLUDE_PATH")) die("Error: AC_INCLUDE_PATH is not defined.");
include_once(AC_INCLUDE_PATH. 'classes/DAO/LangCodesDAO.class.php');

class BasicChecks {
	/**
	* cut out language code from given $lang
	* return language code
	*/
	public static function cutOutLangCode($lang)
	{
		$words = explode("-", $lang);
		return trim($words[0]);
	}

	/**
	* return array of all the 2-letter & 3-letter language codes with direction 'rtl'
	*/
	public static function getRtlLangCodes()
	{
		$langCodesDAO = new LangCodesDAO();
		
		return $langCodesDAO->GetLangCodeByDirection('rtl');
	}
	
	/**
	* check if the text is in one of the search string defined in $search_strings
	* @param $text: text to check
	*        $search_strings: array of match string. The string could be %[string]% or %[string] or [string]%
	* @return true if in, otherwise, return false 
	*/
	public static function inSearchString($text, $search_strings)
	{
		foreach ($search_strings as $str)
		{
			$str = trim($str);
			$prefix = substr($str, 0 , 1);
			$suffix = substr($str, -1);
			
			if ($prefix == '%' && $suffix == '%')
			{  // match '%match%' 
				if (stripos($text, substr($str, 1, -1)) > 0) return true;
			}
			else if ($prefix == '%')
			{  // match '%match'
				$match = substr($str, 1);
				if (substr($text, strlen($match)*(-1)) == $match) return true;
			} 
			else if ($suffix == '%')
			{  // match 'match%'
				$match = substr($str, 0, -1);
				if (substr($text, 0, strlen($match)) == $match) return true;
			} 
			else if ($text == $str)
			{ 
				return true;
			}
		}
		
		return false;
	}
	
	/**
	* check if the inner text is in one of the search string defined in checks.search_str
	* return true if in, otherwise, return false 
	*/
	public static function isTextInSearchString($text, $check_id, $e)
	{
		$text = strtolower(trim($text));

		$checksDAO = new ChecksDAO();
		$row = $checksDAO->getCheckByID($check_id);
		
		$search_strings = explode(',', strtolower(_AC($row['search_str'])));
		
		if (!is_array($search_strings)) return true;
		else
		{
			return BasicChecks::inSearchString($text, $search_strings);
		}
	}

	/**
	* Makes a guess about the table type.
	* Returns true if this should be a data table, false if layout table.
	*/
	public static function isDataTable($e)
	{
		global $is_data_table;
		
		// "table" element containing <th> is considered a data table
		if ($is_data_table) return;

		foreach ($e->children() as $child)
		{
			if ($child->tag == "th") 
				$is_data_table = true;
			else 
				BasicChecks::isDataTable($child);
		}
	}
	
	/**
	* Check recursively to find if $global_e has a parent with tag $parent_tag
	* return true if found, otherwise, false
	*/
	public static function hasParent($e, $parent_tag)
	{
		if ($e->parent() == NULL) return false;
		
		if ($e->parent()->tag == $parent_tag)
			return true;
		else
			return BasicChecks::hasParent($e->parent(), $parent_tag);
	}
	
	/**
	* Check recursively to find the number of children in $e with tag $child_tag
	* return number of qualified children
	*/
	public static function getNumOfTagRecursiveInChildren($e, $tag)
	{
		$num = 0;
		
		foreach($e->children() as $child)
			if ($child->tag == $tag) $num++;
			else $num += BasicChecks::getNumOfTagRecursiveInChildren($child, $tag);

		return $num;
	}
	
	/**
	* Check recursively if there are duplicate $attr defined in children of $e
	* set global var hasDuplicateAttribute to true if there is, otherwise, set it to false
	*/
	public static function hasDuplicateAttribute($e, $attr, &$id_array)
	{
		global $has_duplicate_attribute;
		
		if ($has_duplicate_attribute) return;
		
		foreach($e->children() as $child)
		{
			$id_val = strtolower(trim($child->attr[$attr]));
			
			if ($id_val <> "" && in_array($id_val, $id_array)) $has_duplicate_attribute = true;
			else 
			{
				if ($id_val <> "") array_push($id_array, $id_val);
				BasicChecks::hasDuplicateAttribute($child, $attr, $id_array);
			}
		}
	}

	/**
	* Get number of header rows and number of rows that have header column
	* return array of (num_of_header_rows, num_of_rows_with_header_col)
	*/
	public static function getNumOfHeaderRowCol($e)
	{
		$num_of_header_rows = 0;
		$num_of_rows_with_header_col = 0;
		
		foreach ($e->find("tr") as $row)
		{
			$num_of_th = count($row->find("th"));
			
			if ($num_of_th > 1) $num_of_header_rows++;
			if ($num_of_th == 1) $num_of_rows_with_header_col++;
		}
		
		return array($num_of_header_rows, $num_of_rows_with_header_col);
	}

	/**
	 * called by BasicFunctions::hasFieldsetOnMultiCheckbox()
	 * Check if form has "fieldset" and "legend" to group multiple checkbox buttons.
	 * @return true if has, otherwise, false
	 */
	public static function hasFieldsetOnMultiCheckbox($e)
	{
		// find if there are radio buttons with same name
		$children = $e->children();
		$num_of_children = count($children);
		
		foreach ($children as $i => $child)
		{
			if (strtolower(trim($child->attr["type"])) == "checkbox")
			{
				$this_name = strtolower(trim($child->attr["name"]));
				
				for($j=$i+1; $j <=$num_of_children; $j++)
					// if there are radio buttons with same name,
					// check if they are contained in "fieldset" and "legend" elements
					if (strtolower(trim($children[$j]->attr["name"])) == $this_name)
						if (BasicChecks::hasParent($e, "fieldset"))
							return BasicChecks::hasParent($e, "legend");
						else
							return false;
			}
			else
				return BasicChecks::hasFieldsetOnMultiCheckbox($child);
		}
		
		return true;
	}

	/**
	* check if value in the given attribute is a valid language code
	* return true if valid, otherwise, return false
	*/
	public static function isValidLangCode($code)
	{
		$code = BasicChecks::cutOutLangCode($code);
		$langCodesDAO = new LangCodesDAO();

		if (strlen($code) == 2) 
		{
			$rows = $langCodesDAO->GetLangCodeBy2LetterCode($code);
		}
		else if (strlen($code) == 3)
		{
			$rows = $langCodesDAO->GetLangCodeBy3LetterCode($code);
		}
		else 
		{
			return false;
		}

		return (is_array($rows));
	}

	/**
	* Return file location based on base href or uri
	* return file itself if both base href and uri are empty.
	*/
	public static function getFile($src_file, $base_href, $uri)
	{
		if (preg_match('/http.*(\:\/\/).*/', $src_file))
			$file = $src_file;
		else
		{
			// URI that image relatively located to
			// Note: base_href is from <base href="...">
			if (isset($base_href) && $base_href <> '') 
			{
				if (substr($base_href, -1) <> '/') $base_href .= '/';
			}
			else if (isset($uri) && $uri <> '')
			{
				preg_match('/^(.*\:\/\/.*\/).*/', $uri, $matches);
				if (!isset($matches[1])) $uri .= '/';
				else $uri = $matches[1];
			}
				
			if (substr($src_file, 0, 1) == '/')  //absolute path
			{
				if (isset($base_href) && $base_href <> '') 
				{
					$file = $base_href.substr($src_file, 1);
				}
				else if (isset($uri) && $uri <> '')
				{
					preg_match('/^(.*\:\/\/)(.*)/', $uri, $matches);
					$root_uri = $matches[1].substr($matches[2], 0, strpos($matches[2], '/'));
					$file = $root_uri.$src_file;
				}
			}
			else // relative path
			{
				if (isset($base_href) && $base_href <> '') 
				{
					$file = $base_href.$src_file;
				}
				else if (isset($uri) && $uri <> '')
				{
					$file = $uri.$src_file;
				}
			}
		}
		
		if (!isset($file)) $file = $src_file;
		
		return $file;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	// Vecchi basic checks utilizzati dai checks
	/**
	Check if the luminosity contrast ratio between $color1 and $color2 is at least 5:1
	Input: color values to compare: $color1 & $color2. Color value can be one of: rgb(x,x,x), #xxxxxx, colorname
	Return: true or false
	*/
	public static function has_good_contrast_waiert($color1, $color2)
	{
		include_once (AC_INCLUDE_PATH . "classes/ColorValue.class.php");

		$color1 = new ColorValue($color1);
		$color2 = new ColorValue($color2);
		
		if (!$color1->isValid() || !$color2->isValid())
			return true;
		
		$colorR1 = $color1->getRed();
		$colorG1 = $color1->getGreen();
		$colorB1 = $color1->getBlue();
		
		$colorR2 = $color2->getRed();
		$colorG2 = $color2->getGreen();
		$colorB2 = $color2->getBlue();

		$brightness1 = (($colorR1 * 299) + 
							($colorG1 * 587) + 
							($colorB1 * 114)) / 1000;

		$brightness2 = (($colorR2 * 299) + 
							($colorG2 * 587) + 
							($colorB2 * 114)) / 1000;

		$difference = 0;
		if ($brightness1 > $brightness2)
		{
			$difference = $brightness1 - $brightness2;
		}
		else 
		{
			$difference = $brightness2 - $brightness1;
		}

		if ($difference < 125)
		{
			return false;
		}

		// calculate the color difference
		$difference = 0;
		// red
		if ($colorR1 > $colorR2)
		{
			$difference = $colorR1 - $colorR2;
		}
		else
		{
			$difference = $colorR2 - $colorR1;
		}

		// green
		if ($colorG1 > $colorG2)
		{
			$difference += $colorG1 - $colorG2;
		}
		else
		{
			$difference += $colorG2 - $colorG1;
		}

		// blue
		if ($colorB1 > $colorB2)
		{
			$difference += $colorB1 - $colorB2;
		}
		else
		{
			$difference += $colorB2 - $colorB1;
		}

		return ($difference > 499);
	}	
	
		/**
	* Check recursively to find if $e has a parent with tag $parent_tag
	* return true if found, otherwise, false
	*/
	public static function has_parent($e, $parent_tag)
	{
		if ($e->parent() == NULL) return false;
		
		if ($e->parent()->tag == $parent_tag)
			return true;
		else
			return BasicChecks::has_parent($e->parent(), $parent_tag);
	}
	
	
		/**
	* cut out language code from given $lang
	* return language code
	*/
	public static function cut_out_lang_code($lang)
	{
		$words = explode("-", $lang);
		return trim($words[0]);
	}
	
		/**
	* check if $code is a valid language code
	* return true if valid, otherwise, return false
	*/
	public static function valid_lang_code($code)
	{
		global $db;

		$code = BasicChecks::cut_out_lang_code($code);

		$sql = "SELECT COUNT(*) cnt FROM ". TABLE_PREFIX ."lang_codes WHERE ";
		
		if (strlen($code) == 2) $sql .= "code_2letters = '".$code ."'";
		else if (strlen($code) == 3) $sql .= "code_3letters = '".$code ."'";
		else return false;
		
		$result	= mysql_query($sql, $db) or die(mysql_error());
		$row = mysql_fetch_assoc($result);

		return ($row["cnt"] > 0);
	}
	
	
	
	/**
	* find language code defined in html
	* return language code
	*/
	public static function get_lang_code($content_dom)
	{
		// get html language
		$e_htmls = $content_dom->find("html");

		foreach ($e_htmls as $e_html)
		{
			if (isset($e_html->attr["xml:lang"])) 
			{
				$lang = trim($e_html->attr["xml:lang"]);
				break;
			}
			else if (isset($e_html->attr["lang"]))
			{
				$lang = trim($e_html->attr["lang"]);
				break;
			}
		}
		
		return BasicChecks::cut_out_lang_code($lang);
	}
		
	
		/**
	Check if the luminosity contrast ratio between $color1 and $color2 is at least 5:1
	Input: color values to compare: $color1 & $color2. Color value can be one of: rgb(x,x,x), #xxxxxx, colorname
	Return: true or false
	*/
	public static function get_luminosity_contrast_ratio($color1, $color2)
	{
		include_once (AC_INCLUDE_PATH . "classes/ColorValue.class.php");

		$color1 = new ColorValue($color1);
		$color2 = new ColorValue($color2);
		
		if (!$color1->isValid() || !$color2->isValid())
			return true;
		
		$linearR1 = $color1->getRed()/255;
		$linearG1 = $color1->getRed()/255;
		$linearB1 = $color1->getRed()/255;

		$lum1 = (pow ($linearR1, 2.2) * 0.2126) +
			(pow ($linearG1, 2.2) * 0.7152) +
			(pow ($linearB1, 2.2) * 0.0722) + .05;
			
		$linearR2 = $color2->getRed()/255;
		$linearG2 = $color2->getRed()/255;
		$linearB2 = $color2->getRed()/255;

		$lum2 = (pow ($linearR2, 2.2) * 0.2126) +
			(pow ($linearG2, 2.2) * 0.7152) +
			(pow ($linearB2, 2.2) * 0.0722) + .05;
			
		$ratio = max ($lum1, $lum2) / min($lum1, $lum2);

		// round the ratio to 2 decimal places
		$factor = pow(10,2);

		// Shift the decimal the correct number of places
		// to the right.
		$val = $ratio * $factor;

		// Round to the nearest integer.
		$tmp = round($val);

		// Shift the decimal the correct number of places back to the left.
		$ratio2 = $tmp / $factor;

		return $ratio2;
	}
	
		/**
	* Check recursively if there are duplicate $attr defined in children of $e
	* set global var $has_duplicate_attribute to true if there is, otherwise, set it to false
	*/
	public static function has_duplicate_attribute($e, $attr, &$id_array)
	{
		global $has_duplicate_attribute;
		
		if ($has_duplicate_attribute) return;
		
		foreach($e->children() as $child)
		{
			$id_val = strtolower(trim($child->attr[$attr]));
			
			if ($id_val <> "" && in_array($id_val, $id_array)) $has_duplicate_attribute = true;
			else 
			{
				if ($id_val <> "") array_push($id_array, $id_val);
				BasicChecks::has_duplicate_attribute($child, $attr, $id_array);
			}
		}
	}
	
	
	/**
	* check if $e has associated label
	* return true if has, otherwise, return false
	*/
	public static function has_associated_label($e, $content_dom)
	{
		// 1. The element $e is contained by a "label" element
		// 2. The element $e has a "title" attribute
		if ($e->parent()->tag == "label" || isset($e->attr["title"])) return true;
		
		// 3. The element $e has an "id" attribute value that matches the "for" attribute value of a "label" element
		$input_id = $e->attr["id"];
		
		if ($input_id == "") return false;  // attribute "id" must exist
		
		foreach ($content_dom->find("label") as $e_label)
		  if (strtolower(trim($e_label->attr["for"])) == strtolower(trim($e->attr["id"])))
		    return true;
	  
	  return false;
	}
	
		/**
	* ADD CODE FOR THIS!!!
	* check if the label for $e is closely positioned to $e
	* return true if closely positioned, otherwise, return false
	*/
	public static function is_label_closed($e)
	{
		return true;
	}
	
		/**
	* Check radio button groups are marked using "fieldset" and "legend" elements
	* Return: use global variable $is_radio_buttons_grouped to return true (grouped properly) or false (not grouped)
	*/
	public static function is_radio_buttons_grouped($e)
	{
		$radio_buttons = array();
		
		foreach ($e->find("input") as $e_input)
		{
			if (strtolower(trim($e_input->attr["type"])) == "radio")
				array_push($radio_buttons, $e_input);
		}

		for ($i=0; $i < count($radio_buttons); $i++)
		{
		  for ($j=0; $j < count($radio_buttons); $j++)
		  {
		    if ($i <> $j && strtolower(trim($radio_buttons[$i]->attr["name"])) == strtolower(trim($radio_buttons[$j]->attr["name"]))
		        && !BasicChecks::has_parent($radio_buttons[$i], "fieldset") && !BasicChecks::has_parent($radio_buttons[$i], "legend"))
		      return false;
		  }
		}
		
		return true;
	}
	
		/**
	* Makes a guess about the table type.
	* Returns true if this should be a data table, false if layout table.
	*/
	public static function is_data_table($e)
	{
		global $is_data_table;
		
		// "table" element containing <th> is considered a data table
		if ($is_data_table) return;

		foreach ($e->children() as $child)
		{
			if ($child->tag == "th") 
				$is_data_table = true;
			else 
				BasicChecks::is_data_table($child);
		}
	}
	
	
	/**
	* check if associated label of $e has text
	* return true if has, otherwise, return false
	*/
	public static function associated_label_has_text($e, $content_dom)
	{
		// 1. The element $e has a "title" attribute
		if (trim($e->attr["title"]) <> "") return true;

		// 2. The element $e is contained by a "label" element
		if ($e->parent()->tag == "label")
		{
			$pattern = "/(.*)". preg_quote($e->outertext, '/') ."/";
			preg_match($pattern, $e->parent->innertext, $matches);
			if (strlen(trim($matches[1])) > 0) return true;
		}
		
		// 3. The element $e has an "id" attribute value that matches the "for" attribute value of a "label" element
		$input_id = $e->attr["id"];
		
		if ($input_id == "") return false;  // attribute "id" must exist
		
		foreach ($content_dom->find("label") as $e_label)
		{
			if ($e_label->attr["for"] == $input_id)
			{
				// label contains text
				if (trim($e_label->plaintext) <> "") return true;
				
				// label contains an image with alt text
				foreach ($e_label->children as $e_label_child)
					if ($e_label_child->tag == "img" && strlen(trim($e_label_child->attr["alt"])) > 0)
						return true;
			}
		}
		
		return false;
	}
	
	
	// Non usate
		public static function check_next_header_not_in ($content_dom, $line_number, $col_number, $not_in_array)
	{
		global $header_array;
		
		// find the next header after $line_number, $col_number
		foreach ($header_array as $e)
		{
			if ($e->linenumber > $line_number || ($e->linenumber == $line_number && $e->colnumber > $col_number))
			{
				if (!isset($next_header)) 
					$next_header = $e;
				else if ($e->linenumber < $next_header->line_number || ($e->linenumber == $next_header->line_number && $e->colnumber > $next_header->col_number))
					$next_header = $e;
			}
		}

		if (isset($next_header) && !in_array($next_header->tag, $not_in_array))
			return false;
		else
			return true;
	}
	
	public static function find_all_headers($elements, &$header_array)
	{
		foreach ($elements as $e)
		{
			if (substr($e->tag, 0, 1) == "h" and intval(substr($e->tag, 1)) <> 0)
				array_push($header_array, $e);
			
			BasicChecks::find_all_headers($e->children(), &$header_array);
		}
		
		return $header_array;
	}
	

	
	/**
	* Check recursively to find the number of children in $e with tag $child_tag
	* return number of qualified children
	*/
	public static function count_children_by_tag($e, $tag)
	{
		$num = 0;
		
		foreach($e->children() as $child)
			if ($child->tag == $tag) $num++;
			else $num += BasicChecks::count_children_by_tag($child, $tag);

		return $num;
	}
	


	/**
	* Get number of header rows and number of rows that have header column
	* return array of (num_of_header_rows, num_of_rows_with_header_col)
	*/
	public static function get_num_of_header_row_col($e)
	{
		$num_of_header_rows = 0;
		$num_of_rows_with_header_col = 0;
		
		foreach ($e->find("tr") as $row)
		{
			$num_of_th = count($row->find("th"));
			
			if ($num_of_th > 1) $num_of_header_rows++;
			if ($num_of_th == 1) $num_of_rows_with_header_col++;
		}
		
		return array($num_of_header_rows, $num_of_rows_with_header_col);
	}
	
	
	
	
	

	
	
	

}
?>  
