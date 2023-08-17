
<?php


/**
 * 
 */
class Input

{



	public static function inputErrors()
	{
		if (Input::errors()) {


			$output = ' <div class="list-group" style="text-align:center;">';


			foreach (Input::errors() as $field => $errors) {


				$field = ucfirst(str_replace('_', ' ', $field));

				$output .=  ' <a class="list-group-item list-group-item-danger" style="padding:0px;">
		         <strong class="list-group-item-heading">' . $field . '</strong>';

				foreach ($errors as $error) {

					$error = ucfirst(str_replace('_', ' ', $error));

					$output .= '<p class="list-group-item-text" style="margin:0px;">' . $error . '</p>';
				}

				$output .= '</a>';
			}

			$output .= '</div>';
		}


		return $output;
	}




	public static function inputError($field)
	{

		$output = '  <span role="alert">';

		if (Input::errors($field)) {
			foreach (Input::errors($field) as $error) {
				$error = ucfirst(str_replace('_', ' ', $error));
				$output .= $error . ' ';
			}

			$output .= '</span>';
			return $output;
		}
	}



	public static function exists($csrf_field = null)
	{
		$_SESSION["inputs"] = $_POST;

		if ($csrf_field != null) {
			$key = Token::$csrf_prefix . $csrf_field;

			$status = (isset($_REQUEST[$key])) ?  $_REQUEST[$key] == Token::csrf_field($key) : false;
		} else {



			foreach ($_REQUEST as $key => $value) {
				$csrf_prefix = Token::$csrf_prefix;

				if (strpos($key, $csrf_prefix) !== false) {

					$status =  $_REQUEST[$key] == Token::csrf_field($key);
				}
			}
		}

		if (($status == true)) {
			return true;
		}

		return false;
	}


	public static function get($item)
	{
		return Input::all()[$item] ?? null;
	}

	public static function all()
	{
		// self::exists();

		$json = file_get_contents('php://input');
		$input = json_decode($json, TRUE) ?? [];

		$input = array_merge($_REQUEST, $input);

		return $input ?? [];
	}

	public static function old($item)
	{
		$input = (array) Session::get('inputs');
		return $input[$item] ?? "";
	}

	public static function errors($fieldError = '')
	{
		if ($fieldError != '') {

			return Session::get('inputs-errors')[$fieldError] ?? '';
		}
		return Session::get('inputs-errors');
	}
}
