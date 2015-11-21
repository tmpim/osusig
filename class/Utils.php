<?php
/**
 * Small utility class.
 *
 * @author Lemmmy
 */
class Utils
{
	public static function largeNumberFormat($number) {
		$n = round($number);

		$n_number_format = number_format($n);
		$n_array = explode(',', $n_number_format);
		$n_parts = array('k', 'M', 'B', 'T');
		$n_count_parts = count($n_array) - 1;

		$n_display = $n_array[0] . ((int) $n_array[1][0] !== 0 ? '.' . $n_array[1][0] : '');
		$n_display .= $n_parts[$n_count_parts - 1];

		return $n_display;
	}
}