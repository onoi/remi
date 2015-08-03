<?php

namespace Onoi\Remi;

use Onoi\HttpRequest\HttpRequest;

/**
 * @license GNU GPL v2+
 * @since 0.1
 *
 * @author mwjames
 */
abstract class FilteredHttpResponseParser implements ResponseParser {

	/**
	 * @var HttpRequest
	 */
	protected $httpRequest;

	/**
	 * @var FilteredRecord
	 */
	protected $record;

	/**
	 * @var array
	 */
	private $messages = array();

	/**
	 * @since 0.1
	 *
	 * @param HttpRequest $httpRequest
	 * @param FilteredRecord $record
	 */
	public function __construct( HttpRequest $httpRequest, FilteredRecord $record ) {
		$this->httpRequest = $httpRequest;
		$this->record = $record;
	}

	/**
	 * @since 0.1
	 *
	 * @param string|array $message
	 */
	public function addMessage( $message ) {
		$this->messages[] = $message;
	}

	/**
	 * @since 0.1
	 *
	 * @return string
	 */
	public function getMessages() {
		return $this->messages;
	}

	/**
	 * @since 0.1
	 *
	 * @return Record
	 */
	public function getRecord() {
		return $this->record;
	}

	/**
	 * @since 0.1
	 *
	 * @return boolean
	 */
	public function usedCache() {
		return method_exists( $this->httpRequest, 'isCached' ) ? $this->httpRequest->isCached() : false;
	}

	/**
	 * @since 0.1
	 *
	 * @param string $query
	 */
	abstract public function doParseFor( $query );

	/**
	 * @since 0.1
	 *
	 * @param string $query
	 *
	 * @return string
	 */
	abstract public function getRawResponse( $query );

}
