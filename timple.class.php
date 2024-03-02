<?php

	/* Timple (Simple TPL Engine) v. 1.00 #08/11/2013# by Oscar González García (oscarlidenbrock@gmail.com). All rights reserved. */
	
	class timple {
		
		private $variables = array();
		
		public function parse($file, $variables = array(), $noprocess = false) {
			$functions_php = array(
				'include' => '/{\s*include\s*}([^{]*){\/include}/',
				'else' => '/{else}/',
				'elseif' => '/{\s*elseif\s*([^\}]*)}/',
				'for' => '/{\s*for\s*([^\}]*)}/',
				'for_end' => '/{\/for}/',
				'foreach' => '/{\s*foreach\s*([^\}]*)}/',
				'foreach_end' => '/{\/foreach}/',
				'if' => '/{\s*if\s*([^\}]*)}/',
				'if_end' => '/{\/if}/',
				'php' => '/{\s*php\s*}/',
				'php_end' => '/{\s*\/php\s*}/',
				'variable' => '/{\s*\$([^\}]*)}/',
			);
			
			$functions_html = array(
				'br' => '/{\s*br\s*}/',
				'link' => '/{\s*link\s*([^\}]*)}([^\{]*){\/link}/',
				'lower' => '/{\s*lower\s*}([^{]*){\s*\/lower\s*}/',
				'replace' => '/{\s*replace\s*\"([^\"])\"\s*,\s*\"([^\"])\"\s*}([^{]*){\/replace}/',
				'trim' => '/{\s*trim\s*}([^{]*){\s*\/trim\s*}/',
				'truncate' => '/{\s*truncate\s*([^},]*)\s*,?\s*([^},]*)}([^{]*){\/truncate}/',
				'upper' => '/{\s*upper\s*}([^{]*){\s*\/upper\s*}/',
			);
			
			if ($variables != null) $this->variables = $variables;
			$html = utf8_decode(file_get_contents($file));
			
			foreach ($functions_php as $key => $regex) $html = preg_replace_callback($regex, array($this, 'function_php_'.$key), $html);

			if (!$noprocess) {
				/* PHP process */
				flush();
				ob_start();
				eval(' ?> '.$html.'<?php ');
				$html = ob_get_clean();
				ob_start();
				
				/* HTML process */
				$temp = '';
				while ($temp != $html) {
					$temp = $html;
					foreach ($functions_html as $key => $regex) $html = preg_replace_callback($regex, array($this, 'function_html_'.$key), $html);
				}
			}
			
			return trim($html);
		}
		
		private function function_html_br($values) {
			return '<br>';
		}

		private function function_html_link($values) {
			return '<a '.$values[1].'>'.$values[2].'</a>';
		}
		
		private function function_html_lower($values) {
			return strtolower($values[1]);
		}
		
		private function function_html_replace($values) {
			return str_replace($values[1], $values[2], $values[3]);
		}
		
		private function function_html_trim($values) {
			return trim($values[1]);
		}
		
		private function function_html_truncate($values) {
			return $values[2] ? substr($values[3], (int)$values[1], (int)$values[2]) : substr($values[3], (int)$values[1]);
		}
		
		private function function_html_upper($values) {
			return strtoupper($values[1]);
		}
		
		private function function_php_else($values) {
			return '<?php } else { ?>';
		}
		
		private function function_php_elseif($values) {
			return '<?php } elseif('.$values[1].'){ ?>';
		}

		private function function_php_for($values) {
			return '<?php for('.$values[1].'){ ?>';
		}

		private function function_php_for_end($values) {
			return '<?php } ?>';
		}
		
		private function function_php_foreach($values) {
			return '<?php foreach('.$values[1].'){ ?>';
		}

		private function function_php_foreach_end($values) {
			return '<?php } ?>';
		}
		
		private function function_php_include($values) {
			return $this->parse($values[1], null, true);
		}
		
		private function function_php_if($values) {
			return '<?php if('.$values[1].'){ ?>';
		}

		private function function_php_if_end($values) {
			return '<?php } ?>';
		}

		private function function_php_php($values) {
			return '<?php ';
		}
		
		private function function_php_php_end($values) {
			return ' ?>';
		}
		
		private function function_php_variable($values) {
			if (isset($this->variables[$values[1]])) {
				$variable = $this->variables[$values[1]];
			} else {
				$variable = '<?php print $'.$values[1].'; ?>';
			}
			return $variable;
		}
	}

                
?>