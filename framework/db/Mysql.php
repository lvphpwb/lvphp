<?php
class frm_db_Mysql extends frm_Baseobject{

	private $sql = '';


	// 当前连接ID
	private $link_id = null; // 当前使用的连接

	// 当前查询ID
	private $query_id = null;

	// 数据库连接参数配置
	private $config = array ();

	public function __construct($config) {
		$this->config = $config;
        $this->connect($config);
	}

	public function reConnect($master = true){
		$this->connect ( $this->config );
		$this->m_link_id = $this->link_id;
	}

	/**
	 * 连接数据库方法
	 *
	 * @param array $config
	 */
	private function connect(&$config) {
		$this->link_id = mysql_connect ( $config ['host'] . ":" . $config ['port'], $config ['user'], $config ['password'], true);
		if (! $this->link_id) {
			$this->halt ( '数据库连接错误！' );
		}
		mysql_select_db($this->config['dbname'], $this->link_id) || $this->halt('无法使用数据库！');;
		mysql_query ( "SET NAMES '" . $this->config ['charset'] . "'", $this->link_id );
	}

	function ping(){
		if(!mysql_ping($this->link_id)){
			mysql_close($this->link_id); //注意：一定要先执行数据库关闭，这是关键
			$this->reConnect(true);
		}
	}


	public function query($sql = '') {
		$this->sql = $sql;
		return $this->doExecute ( $sql );
	}

	public function uQuery($sql = '') {
		$this->sql = $sql;
		return $this->uDoExecute ( $sql );
	}

	public function execute($sql = '') {
		$this->sql = $sql;
		return $this->doExecute ( $sql );
	}

	private function doExecute($sql) {
		if(isset($_GET['sql_debug'])){
		  echo $sql;
		  echo "<br /> \n";
		}
		($this->query_id = mysql_query ( $sql, $this->link_id )) || $this->halt ( '执行SQL查询时出错！' );
		return $this->query_id;
	}

	private function uDoExecute($sql) {
		($this->query_id = mysql_unbuffered_query ( $sql, $this->link_id )) || $this->halt ( '执行SQL查询时出错！' );
		return $this->query_id;
	}

	public function getArray($query_id, $mode = MYSQL_ASSOC) {
		return mysql_fetch_array ( $query_id, $mode );
	}

	public function getAffectedRows() {
		return mysql_affected_rows ( $this->link_id );
	}

	public function getNumRows($query_id) {
		return mysql_num_rows ( $query_id );
	}

	public function getNumFields($query_id) {
		return mysql_num_fields ( $query_id );
	}

	public function result($query_id, $row = 0) {
		return mysql_result ( $query_id, $row );
	}

	public function getOne($sql) {
		$one = false;
		$rs = $this->query ( $sql );
		if ($rs) {
			$one = @$this->result ( $rs, 0 );
		}
		return $one;
	}

	public function getRow($sql) {
		$rs = $this->query ( $sql . ' LIMIT 0, 1');
		$arr = $this->getArray ( $rs );
		$this->free ();
		return $arr;
	}

	public function getAll($sql) {
		$rs = $this->query ( $sql );
		$arr = array ();
		while ( $row = $this->getArray ( $rs ) ) {
			$arr [] = $row;
		}
		$this->free ();
		return $arr;
	}

	public function lastId() {
		return mysql_insert_id ( $this->link_id );
	}

	public function lastSql() {
		return $this->sql;
	}

	private function free() {
		@mysql_free_result ( $this->query_id );
		$this->query_id = 0;
	}

	public function close() {
		mysql_close ( $this->link_id );
		$this->link_id = 0;
	}


	public function quote($string) {
		$this->link_id || $this->initConnect ();
		$string = mysql_real_escape_string ( $string, $this->link_id );
		return $string;
	}

	public function quoteLike($string) {
		return $this->quote ( str_replace ( array ('%', '_' ), array ('\\%', '\\_' ), $string ) );
	}

	public function select($fields, $tables, $where = '', $limit = '', $order_by = '', $group_by = '') {
		$sql = $this->getSelectSql($fields, $tables, $where, $limit, $order_by, $group_by);
        $data = $this->getAll ( $sql);
        return $limit == '0,1' ? $data[0] : $data;
	}

    public function count($table_name, $where = '1'){
        return $this->getOne("SELECT count(*) as count FROM {$table_name} WHERE {$where}");
    }

	public function insert($tble_name, $data) {
		$sql = $this->getInsertSql ( $tble_name, $data );
		return $this->execute ( $sql );
	}

	public function insertMulti($tble_name, $data) {
		$sql = $this->getMultiInsertSql ( $tble_name, $data );
		return $this->execute ( $sql );
	}

	public function replace($tble_name, $data) {
		$sql = $this->getReplaceSql ( $tble_name, $data );
		return $this->execute ( $sql );
	}

	public function update($tble_name, $data, $where = '') {
		$sql = $this->getUpdateSql ( $tble_name, $data, $where );
		return $this->execute ( $sql );
	}

    public function delete($tble_name, $where){
        return $this->execute ( "DELETE FROM {$tble_name} WHERE " . $where );
    }

	public function getSelectSql($fields, $tables, $where = '', $limit = '', $order_by = '', $group_by = '') {
		$sql = "SELECT {$fields} FROM {$tables}";
		empty ( $where ) || $sql .= " WHERE {$where}";
		empty ( $group_by ) || $sql .= " GROUP BY {$group_by}";
		empty ( $order_by ) || $sql .= " ORDER BY {$order_by}";
		empty ( $limit ) || $sql .= " LIMIT {$limit}";
		return $sql;
	}

	public function getInsertSql($tble_name, $data) {
		$sql = '';
		if (! empty ( $data ) && is_array ( $data )) {

			foreach ( array_keys ( $data ) as $key ) {
				$data [$key] = $this->quote ( $data [$key] );
			}

			$fields = "(`" . implode ( "`,`", array_keys ( $data ) ) . "`)";
			$values = "('" . implode ( "','", $data ) . "')";

			$sql = "INSERT INTO
						{$tble_name}
						{$fields}
                    VALUES
                    	{$values}";
		}
		return $sql;
	}

	public function getMultiInsertSql($tble_name, $data) {
		$sql = '';
		if (! empty ( $data ) && is_array ( $data )) {
			$values = array ();
			$fields = array ();
			$first = true;
			foreach ( $data as $k => $v ) {
				foreach ( array_keys ( $v ) as $kk ) {
					$first && $fields [] = $kk;
					$v [$kk] = $this->quote ( $v [$kk] );
				}
				$first = false;
				$values [] = "('" . implode ( "','", $v ) . "')";
			}
			$fields = "(`" . implode ( "`,`", $fields ) . "`)";
			$values = implode ( ",", $values );

			$sql = "INSERT INTO
						{$tble_name}
						{$fields}
                    VALUES
                        {$values}";
		}
		return $sql;
	}

	public function getReplaceSql($tble_name, $data) {
		$sql = '';
		if (! empty ( $data ) && is_array ( $data )) {
			foreach ( array_keys ( $data ) as $key ) {
				$data [$key] = $this->quote ( $data [$key] );
			}

			$fields = "(`" . implode ( "`,`", array_keys ( $data ) ) . "`)";
			$values = "('" . implode ( "','", $data ) . "')";

			$sql = "REPLACE INTO
						{$tble_name}
						{$fields}
                    VALUES
                        {$values}";
		}
		return $sql;
	}

	public function getUpdateSql($tble_name, $data, $condition = '') {
		$sql = '';
		if (! empty ( $data ) && is_array ( $data )) {
			$set = '';
			foreach ( $data as $k => $v ) {
				$v = $this->quote ( $v );
				$set [] = "`{$k}` = '{$v}'";
			}
			$set = implode ( ', ', $set );

			empty ( $condition ) || $condition = ' WHERE ' . $condition;

			$sql = "UPDATE
						{$tble_name}
					SET
						{$set}
						{$condition}";
		}
		return $sql;
	}

	private function halt($message) {
		$out = "DB Error:";
		$out .= $message		? "[message : {$message}]" : '';
		$out .= $this->link_id	? "[error : ".mysql_error ( $this->link_id )."]" : '';
		$out .= $this->link_id	? "[errno : ".mysql_errno ( $this->link_id )."]" : '';
		$out .= $this->sql		? "[SQL : {$this->sql}]" : '';
		echo $out;exit;
	}
}
?>