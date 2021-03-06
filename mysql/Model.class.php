<?php
class Model
{
	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-09-07
	 */
    static public function connect($host='localhost', $port=3306, $dbName, $dbUser, $dbPwd)
    {
        $errMode = PDO::ERRMODE_SILENT;

        $conn = new PDO("mysql:host={$host};port={$port};dbname={$dbName}", $dbUser, $dbPwd);
        $conn->setAttribute(PDO::ATTR_ERRMODE, $errMode);
        $conn->exec("SET NAMES utf8");

        return $conn;
    }

	/*
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2013-04-01
	 */
	public function query($sql, $params = array(), $class = 'stdClass')
	{
		try {
			$dbh = $this->getDbHandle();

			if (empty($params)) {
				/**
				 * Bug Fix: limit 0,50 not work in PDO prepear
				 * referer http://stackoverflow.com/questions/5508993/pdo-limit-and-offset
				 */
				$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);

				$sth = $dbh->query($sql);
				if (!$sth) {
					throw new Exception(var_export($dbh->errorInfo(), TRUE));
				}

				return $sth->fetchAll(PDO::FETCH_CLASS, $class);
			} else {
				$sth = $dbh->prepare($sql);
				$sth->execute($params);

				return $sth->fetchAll(PDO::FETCH_CLASS, $class);
			}
		} catch (Exception $e) {
			error_log('FILE: ' . __FILE__);
			error_log('SQL: ' . $sql);
			error_log('PARAMS: ' . var_export($params, 1));
			error_log('Message: ' . $e->getMessage());

			return array();
		}
	}

	/*
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2013-04-04
	 */
	public function execute($sql, $params)
	{
		$dbh = $this->getDbHandle();
		$sth = $dbh->prepare($sql);
		return $sth->execute($params);
	}

	/*
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2013-02-24
	 */
	public function get($pk)
	{
		try {
			$keys = $this->getPrimaryKey();

			foreach ($keys as $key) {
				$value = is_array($pk) ? $pk[$key] : $pk;

				$where[] = $key . '= :' . $key;
				$params[':' . $key] = $value;
			}

			$sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE ' . implode(' AND ', $where);

			$result = array_shift($this->query($sql, $params, get_class($this)));

			return $result;
		} catch (PDOException $e) {
			error_log('FILE: ' . __FILE__);
			error_log('SQL: ' . $sql);
			error_log('Primary Key: ' . var_export($pk, 1));

			return NULL;
		}
	}

	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-11-21
	 */
	public function find($params)
	{
		$data = $this->findAll($params);

		return $data[0];

	}

	/**
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2016-09-24
	 */
	static public function findOne( array $options = [] )
	{
		$data = self::findAll($options);

		return $data[0];
	}

	/*
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2013-04-06
	 */
	public function findAll(array $args = array())
	{
		$sql = 'SELECT * FROM ' . $this->getTableName();
		$params = array();

		if (!empty($args)) {
			foreach ($args as $column => $value) {
				$where[] = $column . ' = :' . $column;;
				$params[':'.$column] = $value;
			}

			$sql .= ' WHERE ' . implode(' AND ', $where);
		}

		return self::query($sql, $params, get_class($this));
	}

	/*
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2013-02-24
	 */
	public function insert()
	{
		$fields = get_object_vars($this);

		foreach ($fields as $field => $value) {
			$key = ':' . $field;
			$column[] = $field;
			$column_key[] = $key;
			$params[$key] = $value;
		}

		$sql = 'INSERT INTO ' . $this->getTableName() . '(`' . join('`, `' , $column) . '`) VALUES (' . join(',', $column_key) . ')';

		$dbh = $this->getDbHandle();
		$sth = $dbh->prepare($sql);
		$rs = $sth->execute($params);

		if (!$rs) {
			error_log('ERROR CLASS:' . __CLASS__ . ', FUNCTION:' . __FUNCTION__ . ', LINE:' . __LINE__);
			error_log('SQL:' . $sql);
			error_log('Parameters:' . print_r($params, TRUE));
			error_log(print_r($sth->errorInfo(), TRUE));
			return '-1';
		}

		return $dbh->lastInsertId();
	}

	/*
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2013-02-24
	 */
	public function update()
	{
		$fields = get_object_vars($this);

		$pk = $this->getPrimaryKey();
		foreach ($pk as $key) {
			$pk_column[] = $key . ' = :' . $key;
			$params[':' . $key] = $fields[$key];
			unset($fields[$key]);
		}

		foreach ($fields as $field => $value) {
			$column[] = $field . ' = :' . $field;
			$params[':' . $field] = $value;
		}

		$sql = 'UPDATE ' . $this->getTableName() . ' SET ' . implode(', ', $column) . ' WHERE ' . implode(' AND ', $pk_column);

		$dbh = $this->getDbHandle();
		$sth = $dbh->prepare($sql);
		$rs = $sth->execute($params);

		if (!$rs) {
			error_log('ERROR CLASS:' . __CLASS__ . ', FUNCTION:' . __FUNCTION__ . ', LINE:' . __LINE__);
			error_log('SQL:' . $sql);
			error_log('Parameters:' . print_r($params, TRUE));
			error_log(print_r($sth->errorInfo(), TRUE));
		}

		return $rs;
	}

	/*
	 * @author chenliujin <liujin.chen@qq.com>
	 * @since 2013-02-24
	 */
	public function delete()
	{
		$sql = 'DELETE FROM ' . $this->getTableName() . ' WHERE ';

		foreach ($this->getPrimaryKey() as $pk) {
			$sql .= $pk . ' = :' . $pk;
			$params[':' . $pk] = $this->$pk;
		}

		$dbh = $this->getDbHandle();
		$sth = $dbh->prepare($sql);
		$rs = $sth->execute($params);

		if (!$rs) {
			error_log('ERROR CLASS:' . __CLASS__ . ', FUNCTION:' . __FUNCTION__ . ', LINE:' . __LINE__);
			error_log('SQL:' . $sql);
			error_log('Parameters:' . print_r($params, TRUE));
			error_log(print_r($sth->errorInfo(), TRUE));
		}

		return $rs;
	}
}
