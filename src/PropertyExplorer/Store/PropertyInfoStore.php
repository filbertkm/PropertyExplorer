<?php

namespace PropertyExplorer\Store;

use Wikibase\DataModel\Entity\PropertyId;

class PropertyInfoStore {

	private $db;

	private $entityLookup;

	public function __construct( $db, $entityLookup ) {
		$this->db = $db;
		$this->entityLookup = $entityLookup;
	}

	public function getProperties() {
		$rows = $this->getPropertyInfoRows();
		$properties = $this->convertPropertyInfoRowsToParams( $rows );

		return $properties;
	}

	private function getPropertyInfoRows() {
		$termSql = $this->buildTermSql();

		$sql = $this->buildPropertyInfoSql();
		$rows = $this->db->fetchAll( $sql, array( 'label', 'property', 'en' ) );

		return $rows;
	}

	private function buildTermSql() {
        $termQueryBuilder = $this->db->createQueryBuilder();
        $termQueryBuilder->select( 'term_text', 'term_entity_id' )
            ->from ( 'wb_terms', 'terms' )
            ->where( 'terms.term_type = ?' )
            ->andWhere( 'terms.term_entity_type = ?' )
            ->andWhere( 'terms.term_language = ?' );

        $termSql = $termQueryBuilder->getSql();

		return $termSql;
	}

	private function buildPropertyInfoSql() {
		$termSql = $this->buildTermSql();

        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select( 'pi_property_id as id', 'pi_type as type', 'term_text as label' )
            ->from( 'wb_property_info', 'pi' )
            ->leftJoin( 'pi', "( $termSql )", 'term', 'term.term_entity_id = pi.pi_property_id' )
            ->orderBy( 'id' )
            ->setFirstResult( 0 )
            ->setMaxResults( 25 );

        $sql = $queryBuilder->getSql();

		return $sql;
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
		$label = $row['label'];
		$propertyType = $row['type'];

		$info = array(
			'id' => $propertyId->getSerialization(),
			'label' => $label ? $label : '-',
			'type' => $propertyType ? $propertyType : '-'
		);

		return $info;
	}

	private function getPropertyIdFromRow( array $row ) {
		return PropertyId::newFromNumber( (int)$row['id'] );
	}

}
