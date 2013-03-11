<?php

/**
 * GlobalVar : Singleton / classCache()
 *
 * @author Bulent Gercek <bulentgercek@gmail.com>
 * @package ClassAutoMate
 */
class GlobalVar
{

		/**
		 * globallerin yerlestirildigi array
		 *
		 * @var static array
		 */
		public static $variables = array();

		/**
		 * global yarat veya varsa icerigini degistir
		 *
		 * @param array,string $var
		 * @param string $value
		 */
		public static function set($var, $value = "")
		{
				$key = array_search($var, self::$variables);

				if (strlen($key) == 0) {
						$expVar = explode('[', trim($var, "]"));

						if (count($expVar) < 2) {
								self::$variables[$var] = $value;
						} else {
								$arrayVar = $expVar[0];
								$arrayKey = $expVar[1];
								self::$variables[$arrayVar][$arrayKey] = $value;
						}
				}
		}
		/**
		 * global dondur
		 *
		 * @param array,string $var
		 * @return array,string
		 */
		public static function get($var = "")
		{
				$expVar = explode('[', trim($var, "]"));

				if (count($expVar) < 2) {
						return self::$variables[$var];
				} else {
						$arrayVar = $expVar[0];
						$arrayKey = $expVar[1];
						return self::$variables[$arrayVar][$arrayKey];
				}
		}
}

?>