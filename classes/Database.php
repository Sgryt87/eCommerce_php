<?php

class Database {

	private $_host = 'localhost';
	private $_user = 'root';
	private $_password = 'root';
	private $_name = 'ecom_php_oop';

	private $_conndb = false;
	public $_last_query = null;
	public $_affected_rows = 0;

	public $_inserted_keys = [];
	public $_inserted_values = [];
	public $_update_sets = [];

	public $_id;

	public function __construct() {
		$this->connect();
	}

	private function connect() {
		$this->_conndb = new mysqli( $this->_host, $this->_user, $this->_password, $this->_name );

		if ( mysqli_connect_errno() ) {
			printf( "Connection Failed: %s\n" . mysqli_connect_error() );
			exit();
		}

		if ( $_result = $this->_conndb->query( "SELECT DATABASE()" ) ) {
			$_row = $_result->fetch_row();
			printf( "Default database is %s.\n", $_row[0] );
			$_result->close();
		}

		$this->_conndb->select_db( $this->_name );

		mysqli_set_charset( $this->_conndb, 'utf8' );
	}

	public function close() {
		if ( ! mysqli_close( $this->_conndb ) ) {
			die( 'Closing failed' );
		}
	}

	public function escape( $value ) {
		return mysqli_real_escape_string( $value );
	}

	public function query( $sql ) {
		$this->_last_query = $sql;
		$result            = mysqli_query( $sql, $this->_conndb );
		$this->displayQuery( $result );

		return $result;
	}

	public function displayQuery( $result ) {
		if ( ! $result ) {
			$msg = 'Query Failed: ' . mysqli_error() . '<br>' . 'Last query was: ' . $this->_last_query;
			die( $msg );
		} else {
			$this->_affected_rows = mysqli_affected_rows( $this->_conndb );
		}
	}

	public function fetchAll( $sql ) {
		$result = $this->query( $sql );
		$out    = [];
		while ( $row = mysqli_fetch_assoc( $result ) ) {
			$out[] = $row;
		}
		mysqli_free_result( $result );

		return $out;
	}

	public function fetchOne( $sql ) {
		$out = $this->fetchAll( $sql );

		return array_shift( $out );
	}

	public function lastId() {
		return mysqli_insert_id( $this->_conndb );
	}


}