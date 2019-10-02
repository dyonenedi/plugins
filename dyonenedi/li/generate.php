<?php
	use Lidiun_Framework_v5\Layout;
	use Lidiun_Framework_v5\Path;
	use Plugin\Dyonenedi\Li;

	Class Generate
	{
		public function __construct() {
			if (!empty($_POST['table']) && !empty($_POST['inline'])) {
				$table    = $_POST['table'];
				$inline   = (!empty($_POST['inline'])) ? true: false;

				$path     = Path::$_path['segment'];
				$filename = $table;

				$return = Li::generate($table, $filename, $path, $inline);
				if ($return) {
					Layout::replaceContent('message', '<span class="col-sm-12 alert alert-success"><p>Formulário gerado com sucesso.</p></span>');
				} else {
					Layout::replaceContent('message', '<span class="col-sm-12 alert alert-danger"><p>O formulário não pode ser gerado.</p></span>');
				}

				Layout::replaceContent('value_1', $_POST['table']);
				Layout::replaceContent('value_2', (!empty($_POST['inline'])) ? 'checked': '');
			} else {
				Layout::replaceContent('message', '');
				Layout::replaceContent('value_1', '');
				Layout::replaceContent('value_2', '');
			}
		}
	}