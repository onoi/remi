<?php

namespace Onoi\Remi;

/**
 * @license GNU GPL v2+
 * @since 0.1
 *
 * @author mwjames
 */
class NullResponseParser implements ResponseParser {

	/**
	 * @var FilteredRecord
	 */
	private $record;

	/**
	 * @since 0.1
	 *
	 * @param FilteredRecord $record
	 */
	public function __construct( FilteredRecord $record ) {
		$this->record = $record;
	}

	/**
	 * @since 0.1
	 *
	 * {@inheritDoc}
	 */
	public function getRecord() {
		return $this->record;
	}

	/**
	 * @since 0.1
	 *
	 * {@inheritDoc}
	 */
	public function getMessages() {
		return array();
	}

	/**
	 * @since 0.1
	 *
	 * {@inheritDoc}
	 */
	public function usedCache() {
		return false;
	}

	/**
	 * @since 0.1
	 *
	 * {@inheritDoc}
	 */
	public function doParseFor( $query ) {}

	/**
	 * @since 0.1
	 *
	 * {@inheritDoc}
	 */
	public function getRawResponse( $query ) {
		return '';
	}

}
