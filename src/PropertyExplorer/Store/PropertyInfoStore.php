<?php

namespace PropertyExplorer\Store;

use Wikibase\DataModel\Entity\PropertyId;

class PropertyInfoStore {

	private $db;

	public function __construct( $db ) {
		$this->db = $db;
	}

	public function getProperties() {
		$rows = $this->getPropertyInfoRows();
		$properties = $this->convertPropertyInfoRowsToParams( $rows );

		return $properties;
	}

	private function getPropertyInfoRows() {
		$queryBuilder = $this->db->createQueryBuilder();

		$queryBuilder->select( 'pi_property_id as id', 'pi_type as type' )
			->from( 'wb_property_info', 'pi' )
			->orderBy( 'id' )
			->setFirstResult( 0 )
			->setMaxResults( 25 );

		$sql = $queryBuilder->getSql();

		$rows = $this->db->fetchAll( $sql );

		return $rows;
	}

	private function convertPropertyInfoRowsToParams( array $rows ) {
		$properties = array();

		foreach( $rows as $row ) {
			$properties[] = $this->getPropertyParamsFromRow( $row );
		}

		return $properties;
	}

	private function getPropertyParamsFromRow( array $row ) {
		$propertyId = $this->getPropertyIdFromRow( $row );

		$info = array(
			'id' => $propertyId->getSerialization()
		);

		return $info;
	}

	private function getPropertyIdFromRow( array $row ) {
		return PropertyId::newFromNumber( (int)$row['id'] );
	}

}
