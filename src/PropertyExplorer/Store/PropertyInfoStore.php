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
		$sql = $this->buildPropertyInfoSql();

		$rows = $this->db->fetchAll(
			$sql,
			array(
				'description',
				'property',
				'en',
				'label',
				'property',
				'en'
			)
		);

		return $rows;
	}

	private function buildTermSql() {
		$descSql = $this->buildTermDescriptionSql();

		$termQueryBuilder = $this->db->createQueryBuilder();
		$termQueryBuilder->select(
				'terms.term_text as label',
				'terms.term_entity_id',
				'description'
			)
			->from ( 'wb_terms', 'terms' )
			->leftJoin( 'terms', "( $descSql )", 'description',
				'terms.term_entity_id = description.term_entity_id' )
			->where( 'terms.term_type = ?' )
			->andWhere( 'terms.term_entity_type = ?' )
			->andWhere( 'terms.term_language = ?' );

		return $termQueryBuilder->getSql();
	}

	private function buildTermDescriptionSql() {
		$termQueryBuilder = $this->db->createQueryBuilder();
		$termQueryBuilder->select( 'term_text as description', 'term_entity_id' )
			->from( 'wb_terms', 'terms' )
			->where( 'terms.term_type = ?' )
			->andWhere( 'terms.term_entity_type = ?' )
			->andWhere( 'terms.term_language = ?' );

		return $termQueryBuilder->getSql();
	}

	private function buildPropertyInfoSql() {
		$termSql = $this->buildTermSql();

		$queryBuilder = $this->db->createQueryBuilder();
		$queryBuilder->select( 'pi_property_id as id', 'pi_type as type', 'label', 'description' )
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
		$propertyType = $row['type'];
		$label = $row['label'];
		$description = $row['description'];

		$info = array(
			'id' => $propertyId->getSerialization(),
			'type' => $propertyType ? $propertyType : '-',
			'label' => $label ? $label : '-',
			'description' => $description ? $description : '-'
		);

		return $info;
	}

	private function getPropertyIdFromRow( array $row ) {
		return PropertyId::newFromNumber( (int)$row['id'] );
	}

}
