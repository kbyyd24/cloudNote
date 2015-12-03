<?php

class Methods {
	function init_value_quote($value, $data) {
		if (is_string($value)) {
			if (is_array($data)) {
				if (array_key_exists($value, $data)) {
					return "'".$data[$value]."'";
				}
			}
		}
		return '';
	}
	
	function init_value($value, $data) {
		if (is_string($value)) {
			if (is_array($data)) {
				if (array_key_exists($value, $data)) {
					return $data[$value];
				}
			}
		}
		return '';
	}
}
