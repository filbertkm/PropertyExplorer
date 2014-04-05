<?php

namespace PropertyExplorer\Store;

class PropertyStore {

	private $db;

	public function __construct( $db ) {
		$this->db = $db;
	}

	public function getProperties() {
		$sql = 'SELECT pi_property_id as id, pi_type as type'
			. ' FROM wb_property_info'
			. ' ORDER BY id'
			. ' LIMIT 20';

		$rows = $this->db->fetchAll( $sql );

		return $rows;
	}

}
