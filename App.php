<?php

require_once 'Db.php';

class App
{

	private $_tableName;
	private $_csv;
	/**
	 * @var Db
	 */
	private $_db;

	public function __construct($config)
	{
		$this->_csv       = $config['csv'];
		$this->_tableName = $config['table'];

		set_error_handler([$this, 'handleError'], E_ALL);
		set_exception_handler([$this, 'handleException']);

		$this->initDb($config['db']);
	}

	private function initDb($config)
	{
		$config = array_merge([
			'dsn'        => null,
			'username'   => null,
			'password'   => null,
			'attributes' => null,
			'charset'    => 'utf8',
		], $config);

		$this->_db = new Db(
			$config['dsn'],
			$config['username'],
			$config['password'],
			$config['attributes'],
			$config['charset']
		);
		$this->_db->open();
	}

	public function start()
	{
		if (!in_array($this->_tableName, $this->_db->fetchColumn("SHOW TABLES"))) {
			$this->createTable();
		}

		$error = '';
		if (!$row = $this->_db->fetchOne("SELECT * FROM `$this->_tableName` ORDER BY rand()", [], $error)) {
			throw new \Exception("Can not fetch the data: $error");
		}

		$row['status'] = ($from = $row['status']) ? 0 : 1;
		if (!$this->_db->execute(
			"UPDATE `$this->_tableName` SET `status` = ? WHERE `id` = ?",
			[$row['status'], $row['id']],
			$error
		)) {
			throw new \Exception("Can not update the data: $error");
		}

		echo $this->render('index.php', ['user' => $row, 'from' => $from]);
	}

	private function createTable()
	{
		if (!file_exists($this->_csv)) {
			throw new \Exception('CSV file not found!');
		}

		$params = [];

		$file = fopen($this->_csv, 'r');
		while (false !== $line = fgets($file)) {
			list($name, $status) = array_map('trim', explode(';', mb_convert_encoding($line, 'UTF-8', 'CP1251')));
			$params[] = $name;
			$params[] = $status;
		}
		fclose($file);

		// remove header columns
		$params = array_slice($params, 2);
		if (empty($params)) {
			return;
		}

		$sql = <<< SQL
			CREATE TABLE `$this->_tableName` (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(255) NOT NULL,
				`status` TINYINT(1) UNSIGNED DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE = MyISAM
SQL;

		$error = '';
		if (!$rows = $this->_db->execute($sql, [], $error)) {
			throw new \Exception("Can not create a database table `$this->_tableName`: $error");
		}

		$sql  = "INSERT INTO `$this->_tableName` (`name`, `status`) VALUES ";
		$sql .= implode(', ', array_fill(0, count($params) / 2, '(?, ?)'));
		if (!$rows = $this->_db->execute($sql, $params, $error)) {
			throw new \Exception("Can not set database data: $error");
		}
	}

	public function handleError($errno, $errstr, $errfile, $errline, $context = null)
	{
		echo $this->render('error.php', ['message' => $errstr]);
		die();
	}

	public function handleException(\Exception $e)
	{
		echo $this->render('error.php', ['message' => $e->getMessage()]);
		die();
	}

	private function render($view, $params = [])
	{
		$content = $this->renderFile($view, $params);
		return $this->renderFile("layout.php", array_merge($params, ['content' => $content]));
	}

	private function renderFile($file, $params = [])
	{
		ob_start();
		ob_implicit_flush(false);
		extract($params, EXTR_OVERWRITE);
		require("views/$file");
		return ob_get_clean();
	}

}