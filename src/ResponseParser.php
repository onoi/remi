<?php

namespace Onoi\Remi;

/**
 * @license GNU GPL v2+
 * @since 0.1
 *
 * @author mwjames
 */
interface ResponseParser {

	/**
	 * @since 0.1
	 *
	 * @return FilteredRecord
	 */
	public function getRecord();

	/**
	 * @since 0.1
	 *
	 * @return array
	 */
	public function getMessages();

	/**
	 * @since 0.1
	 *
	 * @return boolean
	 */
	public function usedCache();

	/**
	 * @since 0.1
	 *
	 * @param string $query
	 */
	public function doParseFor( $query );

	/**
	 * @since 0.1
	 *
	 * @param string $query
	 *
	 * @return string
	 */
	public function getRawResponse( $query );

}
